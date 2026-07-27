<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateFittingStatusRequest;
use App\Http\Requests\UpdateRentalStatusRequest;
use App\Models\Fitting;
use App\Models\Item;
use App\Models\Package;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilan dashboard dinamis berdasarkan role (Member / Admin)
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->memberDashboard($user);
    }

    /**
     * Dashboard Member
     */
    private function memberDashboard($user)
    {
        // Ambil riwayat rental member
        $rentals = Rental::with(['item.category', 'package.category', 'addons.service'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil jadwal fitting member
        $fittings = Fitting::with(['item', 'package'])
            ->where('user_id', $user->id)
            ->orderBy('scheduled_at', 'desc')
            ->get();

        return view('dashboard', [
            'role' => 'member',
            'rentals' => $rentals,
            'fittings' => $fittings,
        ]);
    }

    /**
     * Dashboard Admin / Pemilik Butik
     * Menampilkan statistik ringkasan & seluruh transaksi butik.
     * Sesuai 01-DESIGN.md §4: Kalender fitting, Ringkasan angka besar.
     */
    private function adminDashboard()
    {
        // 1. Statistik Ringkasan
        $totalItems = Item::count();
        $totalPackages = Package::count();
        
        $activeRentalsCount = Rental::whereIn('status', [
            Rental::STATUS_DIKONFIRMASI,
            Rental::STATUS_SEDANG_DISEWA
        ])->count();
        
        $fittingsTodayCount = Fitting::whereDate('scheduled_at', Carbon::today())
            ->where('status', Fitting::STATUS_TERJADWAL)
            ->count();

        $totalFinesOutstanding = Rental::where('status', Rental::STATUS_TERLAMBAT)
            ->sum('fine_amount');

        // Antrian pembayaran: bukti sudah diupload, menunggu konfirmasi admin
        $pendingPaymentsCount = Rental::where('payment_status', Rental::PAYMENT_MENUNGGU_KONFIRMASI)->count();

        // 2. Daftar Transaksi & Fitting (Admin overview)
        $allRentals = Rental::with(['user', 'item', 'package'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'rentals_page');

        $allFittings = Fitting::with(['user', 'item', 'package'])
            ->whereDate('scheduled_at', '>=', Carbon::today())
            ->orderBy('scheduled_at', 'asc')
            ->get();

        // 3. Antrian Pembayaran: yang butuh dikonfirmasi
        $pendingPayments = Rental::with(['user', 'item.category', 'package.category', 'addons'])
            ->where('payment_status', Rental::PAYMENT_MENUNGGU_KONFIRMASI)
            ->orderBy('updated_at', 'asc')
            ->get();

        // 4. Sudah dikonfirmasi, menunggu diambil
        $awaitingPickup = Rental::with(['user', 'item', 'package'])
            ->where('status', Rental::STATUS_PEMBAYARAN_DIKONFIRMASI)
            ->orderBy('payment_confirmed_at', 'asc')
            ->get();

        // 5. Menunggu pembayaran (fitting selesai, belum upload bukti)
        $awaitingPayment = Rental::with(['user', 'item', 'package'])
            ->where('status', Rental::STATUS_MENUNGGU_PEMBAYARAN)
            ->where('payment_status', Rental::PAYMENT_MENUNGGU)
            ->orderBy('updated_at', 'asc')
            ->get();

        return view('dashboard', [
            'role'                  => 'admin',
            'totalItems'            => $totalItems,
            'totalPackages'         => $totalPackages,
            'activeRentalsCount'    => $activeRentalsCount,
            'fittingsTodayCount'    => $fittingsTodayCount,
            'totalFinesOutstanding' => $totalFinesOutstanding,
            'pendingPaymentsCount'  => $pendingPaymentsCount,
            'rentals'               => $allRentals,
            'fittings'              => $allFittings,
            'pendingPayments'       => $pendingPayments,
            'awaitingPickup'        => $awaitingPickup,
            'awaitingPayment'       => $awaitingPayment,
        ]);
    }

    /**
     * Admin: Update status rental (pickup / return / cancel)
     * Validasi ditangani oleh UpdateRentalStatusRequest.
     * Otorisasi: hanya Admin yang bisa update (via RentalPolicy::updateStatus).
     */
    public function updateRentalStatus(UpdateRentalStatusRequest $request, string $id)
    {
        $rental = Rental::findOrFail($id);
        $this->authorize('updateStatus', $rental);

        $action = $request->input('status');

        $rentalService = app(\App\Services\RentalService::class);

        try {
            if ($action === 'confirm') {
                $rentalService->confirmRental($rental);
            } elseif ($action === 'pickup') {
                $rentalService->markAsPickedUp($rental);
            } elseif ($action === 'return') {
                $rentalService->processReturn($rental, Carbon::now());
            } elseif ($action === 'cancel') {
                $rentalService->cancelRental($rental);
            }

            return redirect()->route('dashboard')->with('success', 'Status transaksi sewa berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    /**
     * Admin: Update status fitting
     * Validasi ditangani oleh UpdateFittingStatusRequest.
     * Otorisasi: hanya Admin yang bisa update (via FittingPolicy::updateStatus).
     */
    public function updateFittingStatus(UpdateFittingStatusRequest $request, string $id)
    {
        $fitting = Fitting::findOrFail($id);
        $this->authorize('updateStatus', $fitting);
        $action = $request->input('status');

        $fittingService = app(\App\Services\FittingService::class);

        if ($action === 'complete') {
            $fittingService->completeFitting($fitting);
        } elseif ($action === 'cancel') {
            $fittingService->cancelFitting($fitting);
        } elseif ($action === 'noshow') {
            $fittingService->markNoShow($fitting);
        }

        return redirect()->route('dashboard')->with('success', 'Status jadwal fitting berhasil diperbarui.');
    }
}
