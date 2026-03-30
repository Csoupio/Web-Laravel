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

        return view('admin.index', compact('users', 'clients', 'projets', 'tickets'));
    }

    // ── Utilisateurs ──────────────────────────────────────────
    public function storeUser(Request $request)
    {
        DB::table('users')->insert([
            'Nom'      => $request->input('nom'),
            'email'    => $request->input('email'),
            'password' => $request->input('password'),
            'role'     => $request->input('role'),
        ]);

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