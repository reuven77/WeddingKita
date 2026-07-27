<?php

namespace App\Providers;

use App\Models\Fitting;
use App\Models\Item;
use App\Models\Package;
use App\Models\Rental;
use App\Policies\FittingPolicy;
use App\Policies\ItemPolicy;
use App\Policies\PackagePolicy;
use App\Policies\RentalPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * Daftarkan semua Authorization Policy di sini.
     * Lihat: 03-RULES.md §6 (Authorization).
     */
    public function boot(): void
    {
        Gate::policy(Rental::class, RentalPolicy::class);
        Gate::policy(Fitting::class, FittingPolicy::class);
        Gate::policy(Item::class, ItemPolicy::class);
        Gate::policy(Package::class, PackagePolicy::class);
    }
}
