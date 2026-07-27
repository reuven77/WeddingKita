<?php

namespace App\Services;

use App\Models\Fitting;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * FittingService — logika bisnis jadwal fitting di butik.
 *
 * Tanggung jawab:
 * - Cek kapasitas slot fitting (5 ruang paralel per slot waktu)
 * - Buat jadwal fitting dalam DB::transaction() + lockForUpdate()
 *   untuk mencegah dua user mendapat slot terakhir bersamaan
 * - Update status fitting
 *
 * Kapasitas: Fitting::SLOT_CAPACITY = 5 (02-PRD.md §11)
 * Lihat: 03-RULES.md §2, §3.
 */
class FittingService
{
    // =========================================================================
    // Cek Kapasitas Slot
    // =========================================================================

    /**
     * Hitung jumlah fitting terjadwal di rentang waktu tertentu.
     * Digunakan untuk validasi kapasitas sebelum booking.
     *
     * @param  Carbon  $scheduledAt   Waktu mulai fitting yang diinginkan
     * @param  int     $durationMinutes  Durasi fitting (default 60 menit)
     * @param  string|null  $excludeFittingId  UUID fitting yang dikecualikan (untuk edit)
     */
    public function countActiveInSlot(
        Carbon $scheduledAt,
        int $durationMinutes = 60,
        ?string $excludeFittingId = null
    ): int {
        $slotEnd = $scheduledAt->copy()->addMinutes($durationMinutes);

        $query = Fitting::query()
            ->where('status', Fitting::STATUS_TERJADWAL)
            // Overlap: fitting lain yang dimulai sebelum slot ini selesai
            // DAN berakhir setelah slot ini mulai
            ->where('scheduled_at', '<', $slotEnd)
            ->whereRaw(
                "(scheduled_at + (duration_minutes || ' minutes')::interval) > ?",
                [$scheduledAt->toDateTimeString()]
            );

        if ($excludeFittingId) {
            $query->where('id', '!=', $excludeFittingId);
        }

        return $query->count();
    }

    /**
     * Cek apakah masih ada kapasitas di slot yang diminta.
     */
    public function isSlotAvailable(
        Carbon $scheduledAt,
        int $durationMinutes = 60,
        ?string $excludeFittingId = null
    ): bool {
        return $this->countActiveInSlot($scheduledAt, $durationMinutes, $excludeFittingId)
            < Fitting::SLOT_CAPACITY;
    }

    // =========================================================================
    // Buat Fitting
    // =========================================================================

    /**
     * Buat jadwal fitting baru secara atomik.
     *
     * Menggunakan lockForUpdate() saat cek kapasitas untuk mencegah
     * race condition (dua user merebut slot terakhir bersamaan).
     * Lihat: 03-RULES.md §2.
     *
     * @param  array{
     *   user_id: string,
     *   rental_id?: string|null,
     *   item_id?: string|null,
     *   package_id?: string|null,
     *   scheduled_at: string,
     *   duration_minutes?: int,
     *   notes?: string|null,
     * }  $data
     *
     * @throws \RuntimeException  Jika slot sudah penuh.
     */
    public function createFitting(array $data): Fitting
    {
        return DB::transaction(function () use ($data): Fitting {
            $scheduledAt     = Carbon::parse($data['scheduled_at']);
            $durationMinutes = $data['duration_minutes'] ?? 60;
            $slotEnd         = $scheduledAt->copy()->addMinutes($durationMinutes);

            // lockForUpdate: lock baris yang tumpang tindih dengan slot ini
            // supaya tidak ada dua request yang lolos bersamaan
            $activeCount = Fitting::query()
                ->where('status', Fitting::STATUS_TERJADWAL)
                ->where('scheduled_at', '<', $slotEnd)
                ->whereRaw(
                    "(scheduled_at + (duration_minutes || ' minutes')::interval) > ?",
                    [$scheduledAt->toDateTimeString()]
                )
                ->lockForUpdate()
                ->get()
                ->count();


            if ($activeCount >= Fitting::SLOT_CAPACITY) {
                throw new \RuntimeException(
                    'Slot fitting untuk waktu tersebut sudah penuh. ' .
                    'Silakan pilih waktu lain atau hubungi butik.'
                );
            }

            $fitting = Fitting::create([
                'user_id'          => $data['user_id'],
                'rental_id'        => $data['rental_id'] ?? null,
                'item_id'          => $data['item_id'] ?? null,
                'package_id'       => $data['package_id'] ?? null,
                'scheduled_at'     => $scheduledAt,
                'duration_minutes' => $durationMinutes,
                'status'           => Fitting::STATUS_TERJADWAL,
                'notes'            => $data['notes'] ?? null,
            ]);

            Log::info('Fitting scheduled', [
                'fitting_id'   => $fitting->id,
                'user_id'      => $fitting->user_id,
                'scheduled_at' => $fitting->scheduled_at,
                'slot_used'    => $activeCount + 1,
                'capacity'     => Fitting::SLOT_CAPACITY,
            ]);

            return $fitting;
        });
    }

    // =========================================================================
    // Update Status
    // =========================================================================

    /**
     * Tandai fitting sebagai selesai.
     * Jika fitting memiliki rental terkait, update rental ke STATUS_MENUNGGU_PEMBAYARAN
     * agar member bisa melakukan pembayaran. Lihat alur: 03-RULES.md §4.
     */
    public function completeFitting(Fitting $fitting): void
    {
        DB::transaction(function () use ($fitting) {
            $fitting->status = Fitting::STATUS_SELESAI;
            $fitting->save();

            // Ubah rental terkait ke status menunggu_pembayaran
            if ($fitting->rental_id) {
                $rental = Rental::find($fitting->rental_id);
                if ($rental && $rental->status === Rental::STATUS_MENUNGGU_FITTING) {
                    DB::table('rentals')->where('id', $rental->id)->update([
                        'status'         => Rental::STATUS_MENUNGGU_PEMBAYARAN,
                        'payment_amount' => $rental->total_price, // tagihan = total_price
                        'updated_at'     => now(),
                    ]);
                }
            }

            Log::info('Fitting completed, rental set to menunggu_pembayaran', [
                'fitting_id' => $fitting->id,
                'rental_id'  => $fitting->rental_id,
            ]);
        });
    }


    /**
     * Batalkan fitting.
     */
    public function cancelFitting(Fitting $fitting): void
    {
        $fitting->status = Fitting::STATUS_BATAL;
        $fitting->save();
        Log::info('Fitting cancelled', ['fitting_id' => $fitting->id]);
    }

    /**
     * Tandai fitting tidak hadir (no-show).
     */
    public function markNoShow(Fitting $fitting): void
    {
        $fitting->status = Fitting::STATUS_TIDAK_HADIR;
        $fitting->save();
        Log::info('Fitting no-show', ['fitting_id' => $fitting->id]);
    }

    // =========================================================================
    // Kalender Fitting
    // =========================================================================

    /**
     * Ambil jadwal fitting untuk rentang tanggal tertentu (untuk kalender admin).
     * Hanya kolom yang diperlukan — tidak SELECT * (03-RULES.md §2).
     *
     * @param  Carbon  $from
     * @param  Carbon  $to
     * @return \Illuminate\Database\Eloquent\Collection<int, Fitting>
     */
    public function getFittingsForRange(Carbon $from, Carbon $to)
    {
        return Fitting::query()
            ->select(['id', 'user_id', 'rental_id', 'item_id', 'package_id',
                      'scheduled_at', 'duration_minutes', 'status', 'notes'])
            ->with(['user:id,name,phone', 'item:id,call_code,name', 'package:id,name'])
            ->where('scheduled_at', '>=', $from)
            ->where('scheduled_at', '<', $to)
            ->orderBy('scheduled_at')
            ->get();
    }

    /**
     * Ambil sisa kapasitas slot per jam untuk suatu hari.
     * Digunakan untuk menampilkan slot tersedia di UI kalender.
     *
     * @param  Carbon  $date
     * @return array<string, int>  ['09:00' => 3, '10:00' => 5, ...]  (sisa kapasitas)
     */
    public function getSlotCapacityForDay(Carbon $date): array
    {
        $slots = [];
        // Jam kerja butik: 09:00 - 17:00
        for ($hour = 9; $hour < 17; $hour++) {
            $slotStart = $date->copy()->setHour($hour)->setMinute(0)->setSecond(0);
            $used      = $this->countActiveInSlot($slotStart);
            $slots[$slotStart->format('H:i')] = max(0, Fitting::SLOT_CAPACITY - $used);
        }

        return $slots;
    }
}
