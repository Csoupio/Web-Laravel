<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    /**
     * Vérifie qu'un utilisateur est connecté (peu importe son rôle).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('user')) {
            return redirect()->route('login')
                             ->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        return $next($request);
    }
}
