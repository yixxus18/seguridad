<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Request;

class RedirectIfPageExpired
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            // Limpiar la sesión
            session()->invalidate();
            session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.')
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
        }
    }
}