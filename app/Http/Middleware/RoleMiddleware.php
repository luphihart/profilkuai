<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Menangani permintaan masuk.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        abort(403, 'Akses ditolak. Peran Anda (' . $user->role . ') tidak memiliki wewenang untuk membuka halaman ini.');
    }
}
