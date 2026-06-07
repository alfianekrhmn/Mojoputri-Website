<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // WAJIB ADA BARIS INI

class RoleManager
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Cek apakah role user sesuai dengan parameter middleware
        if (Auth::user()->role !== $role) {
            return response()->json(['error' => 'Akses ditolak. Anda tidak memiliki izin.'], 403);
        }

        return $next($request);
    }
}
