<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index()
    {
        $tickets = DB::table('ticket')
             ->join('projets', 'ticket.IDProjet', '=', 'projets.ID')
             ->select('ticket.*', 'projets.Nom as projetNom')
             ->get()->map(fn($i) => (array)$i)->toArray();

        $ticketsOuverts = DB::table('ticket')
                            ->where('Status', 'Ouvert')
                            ->count();

        $projetsActifs  = DB::table('projets')->count();

        return view('clients.index', compact('tickets', 'ticketsOuverts', 'projetsActifs'));
    }
}