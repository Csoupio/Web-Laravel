<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

        $collaborateurs = DB::table('users')
            ->where('role', 'Collaborateur')
            ->select('id', 'name')
            ->get()->map(fn($i) => (array)$i)->toArray();

        $assignations = DB::table('projet_user')
            ->join('users', 'projet_user.user_id', '=', 'users.id')
            ->select('projet_user.projet_id', 'users.id as user_id', 'users.name')
            ->get()
            ->groupBy('projet_id')
            ->map(fn($group) => $group->map(fn($i) => (array)$i)->toArray())
            ->toArray();

        return view('admin.index', compact(
            'users', 'clients', 'projets', 'tickets',
            'collaborateurs', 'assignations'
        ));
    }

    // ── Utilisateurs ──────────────────────────────────────────
    public function storeUser(Request $request)
    {
        $userId = DB::table('users')->insertGetId([
            'name'     => $request->input('nom'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role'     => $request->input('role'),
        ]);

        // Lien automatique si un client existant a le même email
        if ($request->input('role') === 'Client') {
            DB::table('clients')
                ->where('email', $request->input('email'))
                ->whereNull('user_id')
                ->update(['user_id' => $userId]);
        }

        return redirect()->route('admin.index')->with('success', 1);
    }

    // ── Clients ───────────────────────────────────────────────
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
        DB::table('clients')
            ->where('ID', $id)
            ->update([
                'Nom'   => $request->input('nom_client'),
                'email' => $request->input('email_client'),
            ]);

        return redirect()->route('admin.index')->with('success', 1);
    }

    // ── Projets ───────────────────────────────────────────────
    public function storeProjet(Request $request)
    {
        DB::table('projets')->insert([
            'Nom'         => $request->input('nom_projet'),
            'ClientsID'   => $request->input('id_client'),
            'Description' => $request->input('desc'),
        ]);

        return redirect()->route('admin.index')->with('success', 1);
    }

    // ── Assignation collaborateur ─────────────────────────────
    public function assignCollaborateur(Request $request, $projetId)
    {
        $userId = $request->input('user_id');

        $exists = DB::table('projet_user')
            ->where('projet_id', $projetId)
            ->where('user_id', $userId)
            ->exists();

        if (!$exists) {
            DB::table('projet_user')->insert([
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
        DB::table('projet_user')
            ->where('projet_id', $projetId)
            ->where('user_id', $userId)
            ->delete();

        return redirect()->route('admin.index')->with('success', 1);
    }

    // ── Statut ticket ─────────────────────────────────────────
    public function forceStatus(Request $request, $id)
    {
        $allowed = ['Ouvert', 'En cours', 'Terminé', 'Bloqué'];
        $status  = $request->input('new_status');

        if (!in_array($status, $allowed)) {
            return redirect()->route('admin.index')->with('error', 'Statut invalide.');
        }

        DB::table('ticket')
            ->where('ID', $id)
            ->update(['Status' => $status]);

        return redirect()->route('admin.index')->with('success', 1);
    }
}
