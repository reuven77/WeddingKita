<?php

namespace App\Policies;

use App\Models\Rental;
use App\Models\User;

/**
 * RentalPolicy — otorisasi aksi pada model Rental.
 *
 * Aturan role:
 * - Admin  : boleh view semua, updateStatus semua, cancel semua.
 * - Member : hanya boleh view miliknya sendiri, cancel miliknya yang masih berstatus menunggu_fitting.
 *
 * Lihat: 03-RULES.md §6 (Authorization).
 */
class RentalPolicy
{
    /**
     * Admin dapat melihat semua rental.
     * Member hanya bisa melihat rental miliknya sendiri.
     */
    public function view(User $user, Rental $rental): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $rental->user_id;
    }

    /**
     * Hanya Admin yang bisa mengubah status rental (confirm, pickup, return, cancel-admin).
     */
    public function updateStatus(User $user, Rental $rental): bool
    {
        return $user->isAdmin();
    }

    /**
     * Member hanya bisa membatalkan rental miliknya yang masih berstatus menunggu_fitting.
     * Admin bisa membatalkan rental mana pun.
     */
    public function cancel(User $user, Rental $rental): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $rental->user_id
            && $rental->status === Rental::STATUS_MENUNGGU_FITTING;
    }
}
