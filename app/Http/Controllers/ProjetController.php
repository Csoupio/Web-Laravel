<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjetController extends Controller
{
    public function index()
    {
        $projets = DB::table('projets')
                     ->join('clients', 'projets.ClientsID', '=', 'clients.ID')
                     ->select('projets.*', 'clients.Nom as clientNom')
                     ->get()->map(fn($i) => (array)$i)->toArray();

        return view('projets.index', compact('projets'));
    }

    public function show($id)
    {
        $projet = DB::table('projets')
                    ->join('clients', 'projets.ClientsID', '=', 'clients.ID')
                    ->select('projets.*', 'clients.Nom as clientNom')
                    ->where('projets.ID', $id)
                    ->first();

        if (!$projet) {
            abort(404, 'Projet introuvable.');
        }

        $projet = (array) $projet;

        $tickets = DB::table('ticket')
                     ->where('IDProjet', $id)
                     ->get()->map(fn($i) => (array)$i)->toArray();

        return view('projets.show', compact('projet', 'tickets'));
    }
}