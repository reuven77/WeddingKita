<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

/**
 * ItemPolicy — otorisasi manajemen katalog item (gaun, jas, aksesoris).
 *
 * Single-vendor: hanya Admin (pemilik butik) yang berhak mengelola item.
 * Member hanya bisa membaca/melihat katalog (akses read ditangani tanpa auth di route).
 *
 * Lihat: 03-RULES.md §6 (Authorization).
 */
class ItemPolicy
{
    /**
     * Hanya Admin yang bisa membuat item baru.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya Admin yang bisa memperbarui data item.
     */
    public function update(User $user, Item $item): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya Admin yang bisa menghapus item dari katalog.
     */
    public function delete(User $user, Item $item): bool
    {
        return $user->isAdmin();
    }
}
