<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckAuth
{
    /**
     * Vérifie qu'un utilisateur est connecté (peu importe son rôle).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                             ->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        return $next($request);
    }
}
