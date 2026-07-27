<?php

namespace App\Services;

use App\Models\Fitting;
use App\Models\Rental;
use Illuminate\Support\Facades\DB;

/**
 * BookingService — proses booking rental + fitting secara atomik.
 */
class BookingService
{
    public function __construct(
        private readonly RentalService $rentalService,
        private readonly FittingService $fittingService,
    ) {}

    /**
     * @param  array<string, mixed>  $rentalData
     * @param  array<string, mixed>  $fittingData
     * @return array{rental: Rental, fitting: Fitting}
     */
    public function createBooking(array $rentalData, array $fittingData): array
    {
        return DB::transaction(function () use ($rentalData, $fittingData): array {
            $rental = $this->rentalService->createRental($rentalData);
            $fitting = $this->fittingService->createFitting([
                ...$fittingData,
                'rental_id' => $rental->id,
            ]);

            return ['rental' => $rental, 'fitting' => $fitting];
        });
    }
}
