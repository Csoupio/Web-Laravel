<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Projet;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        if ($user->role === 'Administrateur') {
            // L'admin voit tous les tickets
            $tickets = Ticket::with('projet')->get()->map(function($t) {
                $arr = $t->toArray();
                $arr['projetNom'] = $t->projet->nom ?? 'N/A';
                return $arr;
            })->toArray();

            $ticketsOuverts = Ticket::where('statut', 'Ouvert')->count();
            $projetsActifs  = Projet::count();

        } elseif ($user->role === 'Collaborateur') {
            // Le collaborateur voit les tickets des projets qui lui sont assignés
            $tickets = Ticket::whereHas('projet.collaborateurs', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })->with('projet')->get()->map(function($t) {
                $arr = $t->toArray();
                $arr['projetNom'] = $t->projet->nom ?? 'N/A';
                return $arr;
            })->toArray();

            $ticketsOuverts = Ticket::where('statut', 'Ouvert')
                ->whereHas('projet.collaborateurs', function($q) use ($user) {
                    $q->where('users.id', $user->id);
                })->count();

            $projetsActifs = $user->projets()->count();

        } else {
            // Le client voit uniquement les tickets de ses propres projets
            $client = $user->client;
            if (!$client) {
                return view('clients.index', [
                    'tickets' => [],
                    'ticketsOuverts' => 0,
                    'projetsActifs' => 0
                ]);
            }

            $tickets = Ticket::whereHas('projet', function($q) use ($client) {
                $q->where('client_id', $client->id);
            })->with('projet')->get()->map(function($t) {
                $arr = $t->toArray();
                $arr['projetNom'] = $t->projet->nom ?? 'N/A';
                return $arr;
            })->toArray();

            $ticketsOuverts = Ticket::where('statut', 'Ouvert')
                ->whereHas('projet', function($q) use ($client) {
                    $q->where('client_id', $client->id);
                })->count();

            $projetsActifs = $client->projets()->count();
        }

        return view('clients.index', compact('tickets', 'ticketsOuverts', 'projetsActifs'));
    }
}
