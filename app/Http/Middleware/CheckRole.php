<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Vérifie que l'utilisateur connecté a bien le rôle requis.
     * Usage dans les routes : middleware('role:Administrateur')
     *                       : middleware('role:Administrateur,Collaborateur')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = session('user');

        // Pas connecté du tout → page de login
        if (!$user) {
            return redirect()->route('login')
                             ->with('error', 'Vous devez être connecté.');
        }

        $userRole = is_array($user) ? $user['role'] : $user->role;

        // Connecté mais rôle insuffisant → dashboard
        if (!in_array($userRole, $roles)) {
            return redirect()->route('dashboard')
                             ->with('error', 'Accès refusé : vous n\'avez pas les droits nécessaires.');
        }

        return $next($request);
    }
}
