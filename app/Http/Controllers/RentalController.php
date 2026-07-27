<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRentalRequest;
use App\Mail\FittingScheduledMail;
use App\Mail\RentalConfirmedMail;
use App\Models\Rental;
use App\Services\FittingService;
use App\Services\RentalService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RentalController extends Controller
{
    public function __construct(
        private readonly RentalService $rentalService,
        private readonly FittingService $fittingService
    ) {}

    /**
     * Daftarkan/Simpan transaksi penyewaan & jadwal fitting baru.
     * Validasi ditangani oleh StoreRentalRequest (Form Request).
     * Sesuai dengan aturan wajib DB::transaction di Service layer & error catching 23P01.
     * Lihat: 03-RULES.md §2.
     */
    public function store(StoreRentalRequest $request)
    {
        $user = Auth::user();

        // 1. Konversi format waktu & tanggal
        $eventDate = $request->input('event_date');
        $pickupAt = Carbon::parse($eventDate)->subDays(2)->setHour(9)->setMinute(0); // H-2 jam 09:00
        $returnDueAt = Carbon::parse($eventDate)->addDay()->setHour(17)->setMinute(0); // H+1 jam 17:00

        $scheduledAtStr = $request->input('scheduled_date') . ' ' . $request->input('scheduled_time');
        $scheduledAt = Carbon::parse($scheduledAtStr);

        try {
            // 2 & 3. Proses Atomik Buat Rental + Add-ons + Fitting
            [$rental, $fitting] = \Illuminate\Support\Facades\DB::transaction(function () use ($user, $request, $eventDate, $pickupAt, $returnDueAt, $scheduledAt) {
                $rental = $this->rentalService->createRental([
                    'user_id'           => $user->id,
                    'item_id'           => $request->input('item_id'),
                    'package_id'        => $request->input('package_id'),
                    'event_date'        => $eventDate,
                    'pickup_at'         => $pickupAt->toDateTimeString(),
                    'return_due_at'     => $returnDueAt->toDateTimeString(),
                    'addon_service_ids' => $request->input('addon_services') ?? [],
                ]);

                $fitting = $this->fittingService->createFitting([
                    'user_id'          => $user->id,
                    'rental_id'        => $rental->id,
                    'item_id'          => $request->input('item_id'),
                    'package_id'       => $request->input('package_id'),
                    'scheduled_at'     => $scheduledAt->toDateTimeString(),
                    'duration_minutes' => 60,
                    'notes'            => 'Booking awal bersama transaksi sewa ' . ($request->input('item_id') ? 'item' : 'paket'),
                ]);

                return [$rental, $fitting];
            });

            // 4. Kirim email konfirmasi ke member
            try {
                Mail::to($user->email)->queue(new RentalConfirmedMail($rental, $fitting));
                Mail::to($user->email)->queue(new FittingScheduledMail($fitting));
            } catch (\Exception $mailException) {
                // Email gagal tidak boleh menghentikan proses booking
                Log::warning('Gagal mengirim email konfirmasi rental/fitting', [
                    'rental_id' => $rental->id,
                    'error'     => $mailException->getMessage(),
                ]);
            }

            return redirect()->route('dashboard')->with('success', 'Pemesanan berhasil! Jadwal fitting telah dikunci. Silakan datang ke butik pada jam yang dipilih.');

        } catch (\RuntimeException $e) {
            // Tangkap feedback validasi bisnis dari Service (termasuk daterange exclusion constraint 23P01)
            Log::error('Gagal memproses transaksi sewa', [
                'user_id' => $user->id,
                'message' => $e->getMessage()
            ]);

            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error tidak terduga pada transaksi sewa', [
                'user_id'   => $user->id,
                'exception' => $e->getMessage()
            ]);

            return back()->withInput()->with('error', 'Terjadi kesalahan sistem saat memproses pesanan Anda. Silakan coba kembali.');
        }
    }

    /**
     * Member membatalkan pesanan sewa (hanya jika masih berstatus menunggu_fitting).
     * POST /rentals/{rental}/cancel
     */
    public function cancel(string $rentalId)
    {
        $rental = Rental::findOrFail($rentalId);
        $this->authorize('cancel', $rental);

        try {
            $this->rentalService->cancelRental($rental);

            if ($rental->fitting) {
                $this->fittingService->cancelFitting($rental->fitting);
            }

            return redirect()->route('dashboard')->with('success', 'Pesanan sewa berhasil dibatalkan.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }
}

