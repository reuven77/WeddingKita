<?php

namespace App\Services;

use App\Models\Rental;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Member mengupload bukti pembayaran.
     * Mengubah payment_status → menunggu_konfirmasi.
     *
     * @throws \RuntimeException
     */
    public function submitPaymentProof(Rental $rental, UploadedFile $proofFile, ?string $notes = null): Rental
    {
        if ($rental->status !== Rental::STATUS_MENUNGGU_PEMBAYARAN) {
            throw new \RuntimeException(
                'Pembayaran hanya bisa dilakukan saat status transaksi adalah "Menunggu Pembayaran".'
            );
        }

        if ($rental->payment_status === Rental::PAYMENT_LUNAS) {
            throw new \RuntimeException('Transaksi ini sudah lunas.');
        }

        return DB::transaction(function () use ($rental, $proofFile, $notes) {
            // Hapus file lama jika ada (re-upload)
            if ($rental->payment_proof_path) {
                $oldPath = str_replace('storage/', '', $rental->payment_proof_path);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }

            // Simpan file bukti bayar
            $path = $proofFile->store('payment_proofs', 'public');
            $storagePath = 'storage/' . $path;

            DB::table('rentals')->where('id', $rental->id)->update([
                'payment_proof_path' => $storagePath,
                'payment_status'     => Rental::PAYMENT_MENUNGGU_KONFIRMASI,
                'payment_notes'      => $notes,
                'updated_at'         => now(),
            ]);

            $rental->refresh();

            Log::info('Bukti pembayaran diupload oleh member', [
                'rental_id' => $rental->id,
                'user_id'   => $rental->user_id,
                'path'      => $storagePath,
            ]);

            return $rental;
        });
    }

    /**
     * Admin mengkonfirmasi pembayaran sudah diterima.
     * Mengubah payment_status → lunas, rental status → pembayaran_dikonfirmasi.
     *
     * @throws \RuntimeException
     */
    public function confirmPayment(Rental $rental, User $admin, ?string $adminNotes = null): Rental
    {
        if ($rental->payment_status !== Rental::PAYMENT_MENUNGGU_KONFIRMASI) {
            throw new \RuntimeException(
                'Konfirmasi hanya bisa dilakukan jika member sudah mengupload bukti pembayaran.'
            );
        }

        return DB::transaction(function () use ($rental, $admin, $adminNotes) {
            DB::table('rentals')->where('id', $rental->id)->update([
                'payment_status'       => Rental::PAYMENT_LUNAS,
                'status'               => Rental::STATUS_PEMBAYARAN_DIKONFIRMASI,
                'payment_confirmed_at' => now(),
                'payment_confirmed_by' => $admin->id,
                'payment_notes'        => $adminNotes ?? $rental->payment_notes,
                'updated_at'           => now(),
            ]);

            $rental->refresh();

            Log::info('Pembayaran dikonfirmasi oleh admin', [
                'rental_id' => $rental->id,
                'admin_id'  => $admin->id,
            ]);

            return $rental;
        });
    }

    /**
     * Admin mengizinkan baju diambil (setelah pembayaran dikonfirmasi).
     * Mengubah rental status → sedang_disewa.
     *
     * @throws \RuntimeException
     */
    public function allowPickup(Rental $rental): Rental
    {
        if ($rental->status !== Rental::STATUS_PEMBAYARAN_DIKONFIRMASI) {
            throw new \RuntimeException(
                'Baju hanya bisa diizinkan diambil setelah pembayaran dikonfirmasi.'
            );
        }

        return DB::transaction(function () use ($rental) {
            DB::table('rentals')->where('id', $rental->id)->update([
                'status'     => Rental::STATUS_SEDANG_DISEWA,
                'updated_at' => now(),
            ]);

            $rental->refresh();

            Log::info('Admin mengizinkan pengambilan baju', [
                'rental_id' => $rental->id,
            ]);

            return $rental;
        });
    }

    /**
     * Hitung total tagihan rental (base price + addons).
     */
    public function calculateTotal(Rental $rental): int
    {
        $base = (int) $rental->total_price;
        $addons = $rental->addons->sum(fn($a) => (int) $a->price_at_booking);
        return $base + $addons;
    }
}
