<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjetController extends Controller
{
    public function index()
    {
        $sessionUser = session('user');
        $userId      = is_array($sessionUser) ? $sessionUser['id'] : $sessionUser->id;
        $role        = is_array($sessionUser) ? $sessionUser['role'] : $sessionUser->role;

        if ($role === 'Administrateur') {
            $projets = DB::table('projets')
                ->join('clients', 'projets.ClientsID', '=', 'clients.ID')
                ->select('projets.*', 'clients.Nom as clientNom')
                ->get()->map(fn($i) => (array)$i)->toArray();

        } elseif ($role === 'Collaborateur') {
            $projets = DB::table('projets')
                ->join('clients', 'projets.ClientsID', '=', 'clients.ID')
                ->join('projet_user', 'projets.ID', '=', 'projet_user.projet_id')
                ->where('projet_user.user_id', $userId)
                ->select('projets.*', 'clients.Nom as clientNom')
                ->get()->map(fn($i) => (array)$i)->toArray();

        } else {
            // Client : uniquement ses projets
            $projets = DB::table('projets')
                ->join('clients', 'projets.ClientsID', '=', 'clients.ID')
                ->where('clients.user_id', $userId)
                ->select('projets.*', 'clients.Nom as clientNom')
                ->get()->map(fn($i) => (array)$i)->toArray();
        }

        return view('projets.index', compact('projets'));
    }

    public function show($id)
    {
        $sessionUser = session('user');
        $userId      = is_array($sessionUser) ? $sessionUser['id'] : $sessionUser->id;
        $role        = is_array($sessionUser) ? $sessionUser['role'] : $sessionUser->role;

        $projet = DB::table('projets')
            ->join('clients', 'projets.ClientsID', '=', 'clients.ID')
            ->select('projets.*', 'clients.Nom as clientNom')
            ->where('projets.ID', $id)
            ->first();

        if (!$projet) {
            abort(404, 'Projet introuvable.');
        }

        // Vérification des droits d'accès au projet
        if ($role === 'Client') {
            $hasAccess = DB::table('clients')
                ->where('ID', $projet->ClientsID)
                ->where('user_id', $userId)
                ->exists();
            if (!$hasAccess) abort(403, 'Accès refusé.');

        } elseif ($role === 'Collaborateur') {
            $hasAccess = DB::table('projet_user')
                ->where('projet_id', $id)
                ->where('user_id', $userId)
                ->exists();
            if (!$hasAccess) abort(403, 'Accès refusé.');
        }

        $projet = (array) $projet;

        $tickets = DB::table('ticket')
            ->where('IDProjet', $id)
            ->get()->map(fn($i) => (array)$i)->toArray();

        // Collaborateurs assignés au projet
        $collaborateurs = DB::table('projet_user')
            ->join('users', 'projet_user.user_id', '=', 'users.id')
            ->where('projet_user.projet_id', $id)
            ->select('users.id', 'users.name')
            ->get()->map(fn($i) => (array)$i)->toArray();

        return view('projets.show', compact('projet', 'tickets', 'collaborateurs'));
    }
}
