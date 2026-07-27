<?php

namespace App\Policies;

use App\Models\Package;
use App\Models\User;

/**
 * PackagePolicy — otorisasi manajemen paket sewa busana pengantin.
 *
 * Single-vendor: hanya Admin (pemilik butik) yang berhak mengelola paket.
 * Member hanya bisa melihat katalog paket (route publik tanpa auth).
 *
 * Lihat: 03-RULES.md §6 (Authorization).
 */
class PackagePolicy
{
    /**
     * Hanya Admin yang bisa membuat paket baru.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya Admin yang bisa memperbarui data paket.
     */
    public function update(User $user, Package $package): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya Admin yang bisa menghapus paket.
     */
    public function delete(User $user, Package $package): bool
    {
        return $user->isAdmin();
    }
}
