<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}

    /**
     * Member mengupload bukti pembayaran.
     * POST /rentals/{rental}/payment
     */
    public function submitProof(Request $request, string $rentalId)
    {
        $rental = Rental::with(['item', 'package', 'addons'])->findOrFail($rentalId);

        // Pastikan rental milik member yang login
        if ($rental->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke transaksi ini.');
        }

        $request->validate([
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // 5MB
            'payment_notes' => ['nullable', 'string', 'max:500'],
        ], [
            'payment_proof.required' => 'Bukti pembayaran wajib diupload.',
            'payment_proof.mimes'    => 'Format file harus JPG, PNG, atau PDF.',
            'payment_proof.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        try {
            $this->paymentService->submitPaymentProof(
                $rental,
                $request->file('payment_proof'),
                $request->input('payment_notes')
            );

            return redirect()->route('dashboard')
                ->with('success', 'Bukti pembayaran berhasil diupload! Admin akan segera memverifikasi.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Gagal upload bukti pembayaran', [
                'rental_id' => $rentalId,
                'error'     => $e->getMessage(),
            ]);
            return back()->with('error', 'Gagal mengupload bukti pembayaran. Silakan coba lagi.');
        }
    }

    /**
     * Admin mengkonfirmasi pembayaran.
     * POST /admin/rentals/{rental}/confirm-payment
     */
    public function confirmPayment(Request $request, string $rentalId)
    {
        $rental = Rental::findOrFail($rentalId);

        $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->paymentService->confirmPayment($rental, Auth::user(), $request->input('admin_notes'));

            return redirect()->route('dashboard')
                ->with('success', 'Pembayaran berhasil dikonfirmasi. Member dapat mengambil baju setelah diizinkan.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin mengizinkan pengambilan baju.
     * POST /admin/rentals/{rental}/allow-pickup
     */
    public function allowPickup(string $rentalId)
    {
        $rental = Rental::findOrFail($rentalId);

        try {
            $this->paymentService->allowPickup($rental);

            return redirect()->route('dashboard')
                ->with('success', 'Baju berhasil diizinkan diambil. Status menjadi "Sedang Disewa".');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilkan invoice/nota pembayaran.
     * GET /rentals/{rental}/invoice
     */
    public function invoice(string $rentalId)
    {
        $rental = Rental::with([
            'user',
            'item.category',
            'package.category',
            'addons.service',
            'paymentConfirmedBy',
        ])->findOrFail($rentalId);

        $user = Auth::user();

        // Member hanya bisa lihat invoice miliknya; admin bisa lihat semua
        if (!$user->isAdmin() && $rental->user_id !== $user->id) {
            abort(403);
        }

        // Invoice hanya tersedia setelah pembayaran dikonfirmasi atau sudah disewa
        $allowedStatuses = [
            Rental::STATUS_PEMBAYARAN_DIKONFIRMASI,
            Rental::STATUS_SEDANG_DISEWA,
            Rental::STATUS_DIKEMBALIKAN,
            Rental::STATUS_TERLAMBAT,
        ];

        if (!in_array($rental->status, $allowedStatuses) && !$user->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Nota pembayaran belum tersedia.');
        }

        return view('rentals.invoice', compact('rental'));
    }
}
