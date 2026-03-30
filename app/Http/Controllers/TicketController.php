<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function show($id)
    {
        $ticket = DB::table('ticket')
                    ->join('projets', 'ticket.IDProjet', '=', 'projets.ID')
                    ->select('ticket.*', 'projets.Nom as projetNom')
                    ->where('ticket.ID', $id)
                    ->first();

        if (!$ticket) {
            abort(404, 'Ticket introuvable.');
        }

        $ticket = (array) $ticket;

        return view('tickets.show', compact('ticket'));
    }

    // Afficher le formulaire de création pour un projet donné
    public function create($projetId)
    {
        $projet = DB::table('projets')
                    ->where('ID', $projetId)
                    ->first();

        if (!$projet) {
            abort(404, 'Projet introuvable.');
        }

        $projet = (array) $projet;

        return view('tickets.create', compact('projet'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'project' => 'required|integer',
        ]);

        $newId = DB::table('ticket')->insertGetId([
            'Nom'          => $request->input('title'),
            'Descritpion'  => $request->input('description'),
            'IDProjet'     => $request->input('project'),
            'Status'       => 'Ouvert',
            'Priorité'     => $request->input('priority'),
            'Type'         => $request->input('type'),
            'Temps_Estime' => $request->input('estimated_time', 0),
        ]);

        return redirect()->route('tickets.show', $newId);
    }

    public function addComment(Request $request, $id)
    {
        return redirect()->route('tickets.show', $id);
    }
}
