<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\Projet;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all()->toArray();
        $clients = Client::all()->toArray();
        $projets = Projet::with('client')->get()->map(function($p) {
            $arr = $p->toArray();
            $arr['clientNom'] = $p->client->nom ?? 'N/A';
            return $arr;
        })->toArray();
        
        $tickets = Ticket::with('projet')->get()->map(function($t) {
            $arr = $t->toArray();
            $arr['projetNom'] = $t->projet->nom ?? 'N/A';
            return $arr;
        })->toArray();

        // Liste des utilisateurs ayant le rôle 'Collaborateur' (pour le select d'assignation)
        $collaborateurs = User::where('role', 'Collaborateur')->get()->toArray();

        // On prépare les assignations groupées par projet
        $projetsWithCollabs = Projet::with('collaborateurs')->get();
        $assignations = [];
        foreach ($projetsWithCollabs as $projet) {
            foreach ($projet->collaborateurs as $collab) {
                $assignations[$projet->id][] = [
                    'projet_id' => $projet->id,
                    'user_id'   => $collab->id,
                    'name'      => $collab->name
                ];
            }
        }

        return view('admin.index', compact('users', 'clients', 'projets', 'tickets', 'collaborateurs', 'assignations'));
    }

    public function storeUser(Request $request)
    {
        User::create([
            'name'     => $request->input('nom'),
            'email'    => $request->input('email'),
            'password' => $request->input('password'), // User model casts password to hashed
            'role'     => $request->input('role'),
        ]);
        return redirect()->route('admin.index')->with('success', 1);
    }

    public function storeClient(Request $request)
    {
        Client::create([
            'nom'   => $request->input('nom_client'),
            'email' => $request->input('email_client'),
        ]);
        return redirect()->route('admin.index')->with('success', 1);
    }

    public function updateClient(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $client->update([
            'nom'   => $request->input('nom_client'),
            'email' => $request->input('email_client'),
        ]);
        return redirect()->route('admin.index')->with('success', 1);
    }

    public function storeProjet(Request $request)
    {
        Projet::create([
            'nom'           => $request->input('nom_projet'),
            'client_id'     => $request->input('id_client'),
            'description'   => $request->input('desc'),
            'contrat_heures'=> $request->input('contrat_heures', 0),
        ]);
        return redirect()->route('admin.index')->with('success', 1);
    }

    public function updateContrat(Request $request, $id)
    {
        $request->validate(['contrat_heures' => 'required|numeric|min:0']);
        $projet = Projet::findOrFail($id);
        $projet->update([
            'contrat_heures' => $request->input('contrat_heures'),
        ]);
        return redirect()->route('admin.index')->with('success', 1);
    }

    public function forceStatus(Request $request, $id)
    {
        $allowed = ['Ouvert', 'En cours', 'Terminé', 'Bloqué', 'Refusé par client', 'En attente de validation', 'Facturation acceptée'];
        $status  = $request->input('new_status');
        if (!in_array($status, $allowed)) {
            return redirect()->route('admin.index')->with('error', 'Statut invalide.');
        }
        $ticket = Ticket::findOrFail($id);
        $ticket->update(['statut' => $status]);
        return redirect()->route('admin.index')->with('success', 1);
    }

    public function assignCollaborateur(Request $request, $projetId)
    {
        $projet = Projet::findOrFail($projetId);
        $userId = $request->input('user_id');
        
        $projet->collaborateurs()->syncWithoutDetaching([$userId]);
        
        return redirect()->route('admin.index')->with('success', 1);
    }

    public function removeCollaborateur($projetId, $userId)
    {
        $projet = Projet::findOrFail($projetId);
        $projet->collaborateurs()->detach($userId);
        return redirect()->route('admin.index')->with('success', 1);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SUPPRESSION
    // ─────────────────────────────────────────────────────────────────────────

    public function destroyUser($id)
    {
        $currentId = Auth::id();
        if ((int) $id === (int) $currentId) {
            return redirect()->route('admin.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user = User::findOrFail($id);
        
        // Nettoyage manuel (comme dans le code d'origine)
        $user->timeEntries()->delete();
        $user->projets()->detach();
        Client::where('user_id', $id)->update(['user_id' => null]);
        
        $user->delete();

        return redirect()->route('admin.index')->with('success', 1);
    }

    public function destroyClient($id)
    {
        $client = Client::with('projets.tickets')->findOrFail($id);

        foreach ($client->projets as $projet) {
            foreach ($projet->tickets as $ticket) {
                $ticket->timeEntries()->delete();
                $ticket->delete();
            }
            $projet->collaborateurs()->detach();
            $projet->delete();
        }

        $client->delete();

        return redirect()->route('admin.index')->with('success', 1);
    }

    public function destroyProjet($id)
    {
        $projet = Projet::with('tickets')->findOrFail($id);

        foreach ($projet->tickets as $ticket) {
            $ticket->timeEntries()->delete();
            $ticket->delete();
        }

        $projet->collaborateurs()->detach();
        $projet->delete();

        return redirect()->route('admin.index')->with('success', 1);
    }

    public function destroyTicket($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->timeEntries()->delete();
        $ticket->delete();

        return redirect()->route('admin.index')->with('success', 1);
    }
}
