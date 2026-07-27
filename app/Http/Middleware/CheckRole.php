<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Cek apakah user yang login memiliki role yang sesuai (admin / member).
     * Sesuai 03-RULES.md §3 (Role check admin).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $user = Auth::user();

        if ($user->role !== $role) {
            Log::warning('Unauthorized access attempt', [
                'user_id' => $user->id,
                'required_role' => $role,
                'actual_role' => $user->role,
                'path' => $request->path(),
            ]);

            abort(403, 'Akses ditolak. Halaman ini memerlukan hak akses ' . $role);
        }

        return $next($request);
    }
}
