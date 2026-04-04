<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index()
    {
        $sessionUser = session('user');
        $userId      = is_array($sessionUser) ? $sessionUser['id'] : $sessionUser->id;
        $role        = is_array($sessionUser) ? $sessionUser['role'] : $sessionUser->role;

        if ($role === 'Administrateur') {
            // L'admin voit tous les tickets
            $tickets = DB::table('ticket')
                ->join('projets', 'ticket.IDProjet', '=', 'projets.ID')
                ->select('ticket.*', 'projets.Nom as projetNom')
                ->get()->map(fn($i) => (array)$i)->toArray();

            $ticketsOuverts = DB::table('ticket')->where('Status', 'Ouvert')->count();
            $projetsActifs  = DB::table('projets')->count();

        } elseif ($role === 'Collaborateur') {
            // Le collaborateur voit les tickets des projets qui lui sont assignés
            $tickets = DB::table('ticket')
                ->join('projets', 'ticket.IDProjet', '=', 'projets.ID')
                ->join('projet_user', 'projets.ID', '=', 'projet_user.projet_id')
                ->where('projet_user.user_id', $userId)
                ->select('ticket.*', 'projets.Nom as projetNom')
                ->get()->map(fn($i) => (array)$i)->toArray();

            $ticketsOuverts = DB::table('ticket')
                ->join('projet_user', 'ticket.IDProjet', '=', 'projet_user.projet_id')
                ->where('projet_user.user_id', $userId)
                ->where('ticket.Status', 'Ouvert')
                ->count();

            $projetsActifs = DB::table('projet_user')->where('user_id', $userId)->count();

        } else {
            // Le client voit uniquement les tickets de ses propres projets
            // (projets liés au client dont le user_id correspond)
            $tickets = DB::table('ticket')
                ->join('projets', 'ticket.IDProjet', '=', 'projets.ID')
                ->join('clients', 'projets.ClientsID', '=', 'clients.ID')
                ->where('clients.user_id', $userId)
                ->select('ticket.*', 'projets.Nom as projetNom')
                ->get()->map(fn($i) => (array)$i)->toArray();

            $ticketsOuverts = DB::table('ticket')
                ->join('projets', 'ticket.IDProjet', '=', 'projets.ID')
                ->join('clients', 'projets.ClientsID', '=', 'clients.ID')
                ->where('clients.user_id', $userId)
                ->where('ticket.Status', 'Ouvert')
                ->count();

            $projetsActifs = DB::table('projets')
                ->join('clients', 'projets.ClientsID', '=', 'clients.ID')
                ->where('clients.user_id', $userId)
                ->count();
        }

        return view('clients.index', compact('tickets', 'ticketsOuverts', 'projetsActifs'));
    }
}
