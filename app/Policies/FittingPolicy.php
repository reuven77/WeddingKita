<?php

namespace App\Policies;

use App\Models\Fitting;
use App\Models\User;

/**
 * FittingPolicy — otorisasi aksi pada model Fitting.
 *
 * Aturan role:
 * - Admin  : boleh view semua fitting, updateStatus semua fitting.
 * - Member : hanya bisa view fitting miliknya sendiri.
 *
 * Lihat: 03-RULES.md §6 (Authorization).
 */
class FittingPolicy
{
    /**
     * Admin dapat melihat semua jadwal fitting.
     * Member hanya bisa melihat fitting miliknya sendiri.
     */
    public function view(User $user, Fitting $fitting): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $fitting->user_id;
    }

    /**
     * Hanya Admin yang bisa mengubah status fitting (complete, cancel, noshow).
     */
    public function updateStatus(User $user, Fitting $fitting): bool
    {
        return $user->isAdmin();
    }
}
