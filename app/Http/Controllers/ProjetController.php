<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Projet;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class ProjetController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        if ($user->role === 'Administrateur') {
            $projets = Projet::with('client')->get()->map(function($p) {
                $arr = $p->toArray();
                $arr['clientNom'] = $p->client->nom ?? 'N/A';
                return $arr;
            })->toArray();

        } elseif ($user->role === 'Collaborateur') {
            $projets = $user->projets()->with('client')->get()->map(function($p) {
                $arr = $p->toArray();
                $arr['clientNom'] = $p->client->nom ?? 'N/A';
                return $arr;
            })->toArray();

        } else {
            // Client : uniquement ses projets
            $client = $user->client;
            if (!$client) {
                $projets = [];
            } else {
                $projets = $client->projets()->with('client')->get()->map(function($p) {
                    $arr = $p->toArray();
                    $arr['clientNom'] = $p->client->nom ?? 'N/A';
                    return $arr;
                })->toArray();
            }
        }

        return view('projets.index', compact('projets'));
    }

    public function show($id)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $projet = Projet::with(['client', 'tickets', 'collaborateurs'])->find($id);

        if (!$projet) {
            abort(404, 'Projet introuvable.');
        }

        // Vérification des droits d'accès au projet
        if ($user->role === 'Client') {
            $client = $user->client;
            if (!$client || $projet->client_id !== $client->id) {
                abort(403, 'Accès refusé.');
            }

        } elseif ($user->role === 'Collaborateur') {
            if (!$user->projets->contains($projet->id)) {
                abort(403, 'Accès refusé.');
            }
        }

        $tickets = $projet->tickets->map(fn($t) => $t->toArray())->toArray();
        $collaborateurs = $projet->collaborateurs->map(function($c) {
            return [
                'id'   => $c->id,
                'name' => $c->name
            ];
        })->toArray();

        // On convertit le projet en tableau pour la vue (pour garder la compatibilité si besoin)
        $projetArr = $projet->toArray();
        $projetArr['clientNom'] = $projet->client->nom ?? 'N/A';

        return view('projets.show', [
            'projet' => $projetArr,
            'tickets' => $tickets,
            'collaborateurs' => $collaborateurs
        ]);
    }
}
