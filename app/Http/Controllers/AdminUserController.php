<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

class AdminUserController extends Controller
{
    /**
     * Admin: Tampilkan daftar semua member terdaftar.
     */
    public function index()
    {
        $users = User::where('role', 'member')
            ->withCount(['rentals', 'fittings'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Admin: Hapus akun user member beserta seluruh data terkait.
     * Rentals & fittings ikut terhapus via cascadeOnDelete di migration.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Cegah admin menghapus sesama admin
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Akun admin tidak dapat dihapus melalui panel ini.');
        }

        $name = $user->name;
        $user->delete(); // cascadeOnDelete pada rentals & fittings berlaku otomatis

        return redirect()->route('admin.users.index')
            ->with('success', "Akun member \"{$name}\" beserta seluruh data transaksinya telah berhasil dihapus.");
    }
}
