<?php

namespace App\Console\Commands;

use App\Models\Rental;
use App\Services\RentalService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckOverdueRentals extends Command
{
    /**
     * Nama dan signature command.
     *
     * @var string
     */
    protected $signature = 'rentals:check-overdue';

    /**
     * Deskripsi command console.
     *
     * @var string
     */
    protected $description = 'Cek otomatis rental aktif yang melewati return_due_at, ubah status ke terlambat dan hitung denda harian (Rp 20.000/hari)';

    public function __construct(private readonly RentalService $rentalService)
    {
        parent::__construct();
    }

    /**
     * Jalankan console command.
     */
    public function handle(): int
    {
        $now = Carbon::now();

        // Cari rental yang berstatus sedang_disewa tetapi return_due_at sudah di masa lalu
        $overdueRentals = Rental::whereIn('status', [
            Rental::STATUS_SEDANG_DISEWA,
            Rental::STATUS_TERLAMBAT,
        ])
        ->where('return_due_at', '<', $now)
        ->get();

        $count = 0;
        foreach ($overdueRentals as $rental) {
            $daysLate = (int) $rental->return_due_at->diffInDays($now);
            if ($daysLate < 1) {
                $daysLate = 1; // Minimal 1 hari jika sudah lewat batas jam return_due_at
            }

            $fineAmount = $daysLate * RentalService::FINE_PER_DAY;

            $rental->fine_amount = $fineAmount;
            $rental->status      = Rental::STATUS_TERLAMBAT;
            $rental->save();

            $count++;

            Log::info("Rental {$rental->id} marked as overdue ({$daysLate} days, fine: Rp {$fineAmount})");
        }

        $this->info("Berhasil memperbarui {$count} transaksi sewa yang terlambat.");

        return Command::SUCCESS;
    }
}
