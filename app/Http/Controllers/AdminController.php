<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $users = DB::table('users')->get()->map(fn($i) => (array)$i)->toArray();
        $clients = DB::table('clients')->get()->map(fn($i) => (array)$i)->toArray();
        $projets = DB::table('projets')
                    ->join('clients', 'projets.ClientsID', '=', 'clients.ID')
                    ->select('projets.*', 'clients.Nom as ClientsNom')
                    ->get()->map(fn($i) => (array)$i)->toArray();
        $tickets = DB::table('ticket')
                    ->join('projets', 'ticket.IDProjet', '=', 'projets.ID')
                    ->select('ticket.*', 'projets.Nom as projetNom')
                    ->get()->map(fn($i) => (array)$i)->toArray();
        $collaborateurs = DB::table('projet_collaborateurs')
                    ->join('users', 'projet_collaborateurs.user_id', '=', 'users.id')
                    ->join('projets', 'projet_collaborateurs.projet_id', '=', 'projets.ID')
                    ->select('projet_collaborateurs.*', 'users.name as user_name', 'projets.Nom as projet_nom')
                    ->get()->map(fn($i) => (array)$i)->toArray();

        return view('admin.index', compact('users', 'clients', 'projets', 'tickets', 'collaborateurs'));
    }

    public function storeUser(Request $request)
    {
        DB::table('users')->insert([
            'name'     => $request->input('nom'),
            'email'    => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'role'     => $request->input('role'),
        ]);
        return redirect()->route('admin.index')->with('success', 1);
    }

    public function storeClient(Request $request)
    {
        DB::table('clients')->insert([
            'Nom'   => $request->input('nom_client'),
            'email' => $request->input('email_client'),
        ]);
        return redirect()->route('admin.index')->with('success', 1);
    }

    public function updateClient(Request $request, $id)
    {
        DB::table('clients')->where('ID', $id)->update([
            'Nom'   => $request->input('nom_client'),
            'email' => $request->input('email_client'),
        ]);
        return redirect()->route('admin.index')->with('success', 1);
    }

    public function storeProjet(Request $request)
    {
        DB::table('projets')->insert([
            'Nom'           => $request->input('nom_projet'),
            'ClientsID'     => $request->input('id_client'),
            'Description'   => $request->input('desc'),
            'contrat_heures'=> $request->input('contrat_heures', 0),
        ]);
        return redirect()->route('admin.index')->with('success', 1);
    }

    public function updateContrat(Request $request, $id)
    {
        $request->validate(['contrat_heures' => 'required|numeric|min:0']);
        DB::table('projets')->where('ID', $id)->update([
            'contrat_heures' => $request->input('contrat_heures'),
        ]);
        return redirect()->route('admin.index')->with('success', 1);
    }

    public function forceStatus(Request $request, $id)
    {
        $allowed = ['Ouvert', 'En cours', 'Terminé', 'Bloqué'];
        $status  = $request->input('new_status');
        if (!in_array($status, $allowed)) {
            return redirect()->route('admin.index')->with('error', 'Statut invalide.');
        }
        DB::table('ticket')->where('ID', $id)->update(['Status' => $status]);
        return redirect()->route('admin.index')->with('success', 1);
    }

    public function assignCollaborateur(Request $request, $projetId)
    {
        $userId = $request->input('user_id');
        $exists = DB::table('projet_collaborateurs')
            ->where('projet_id', $projetId)->where('user_id', $userId)->exists();
        if (!$exists) {
            DB::table('projet_collaborateurs')->insert([
                'projet_id'  => $projetId,
                'user_id'    => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return redirect()->route('admin.index')->with('success', 1);
    }

    public function removeCollaborateur($projetId, $userId)
    {
        DB::table('projet_collaborateurs')
            ->where('projet_id', $projetId)->where('user_id', $userId)->delete();
        return redirect()->route('admin.index')->with('success', 1);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SUPPRESSION
    // ─────────────────────────────────────────────────────────────────────────

    public function destroyUser($id)
    {
        // Empêcher la suppression de son propre compte
        $sessionUser = session('user');
        $currentId   = is_array($sessionUser) ? $sessionUser['id'] : $sessionUser->id;
        if ((int) $id === (int) $currentId) {
            return redirect()->route('admin.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Supprimer les entrées de temps de cet utilisateur
        DB::table('time_entries')->where('IDUser', $id)->delete();
        // Supprimer les assignations de projets
        DB::table('projet_collaborateurs')->where('user_id', $id)->delete();
        // Supprimer le lien client éventuel
        DB::table('clients')->where('user_id', $id)->update(['user_id' => null]);
        // Supprimer l'utilisateur
        DB::table('users')->where('id', $id)->delete();

        return redirect()->route('admin.index')->with('success', 1);
    }

    public function destroyClient($id)
    {
        // Récupérer les projets du client
        $projetIds = DB::table('projets')->where('ClientsID', $id)->pluck('ID');

        if ($projetIds->count() > 0) {
            // Récupérer les tickets de ces projets
            $ticketIds = DB::table('ticket')->whereIn('IDProjet', $projetIds)->pluck('ID');

            if ($ticketIds->count() > 0) {
                DB::table('time_entries')->whereIn('IDTicket', $ticketIds)->delete();
                DB::table('ticket')->whereIn('ID', $ticketIds)->delete();
            }

            DB::table('projet_collaborateurs')->whereIn('projet_id', $projetIds)->delete();
            DB::table('projets')->whereIn('ID', $projetIds)->delete();
        }

        DB::table('clients')->where('ID', $id)->delete();

        return redirect()->route('admin.index')->with('success', 1);
    }

    public function destroyProjet($id)
    {
        // Supprimer les tickets et leurs entrées de temps
        $ticketIds = DB::table('ticket')->where('IDProjet', $id)->pluck('ID');

        if ($ticketIds->count() > 0) {
            DB::table('time_entries')->whereIn('IDTicket', $ticketIds)->delete();
            DB::table('ticket')->whereIn('ID', $ticketIds)->delete();
        }

        DB::table('projet_collaborateurs')->where('projet_id', $id)->delete();
        DB::table('projets')->where('ID', $id)->delete();

        return redirect()->route('admin.index')->with('success', 1);
    }

    public function destroyTicket($id)
    {
        DB::table('time_entries')->where('IDTicket', $id)->delete();
        DB::table('ticket')->where('ID', $id)->delete();

        return redirect()->route('admin.index')->with('success', 1);
    }
}
