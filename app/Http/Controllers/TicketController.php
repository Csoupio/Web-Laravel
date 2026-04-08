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

        // ── Entrées de temps ──────────────────────────────────────────────
        $timeEntries = DB::table('time_entries')
            ->join('users', 'time_entries.IDUser', '=', 'users.id')
            ->select('time_entries.*', 'users.name as userName')
            ->where('time_entries.IDTicket', $id)
            ->orderBy('time_entries.date', 'desc')
            ->get()
            ->map(fn($i) => (array) $i)
            ->toArray();

        $totalTemps      = array_sum(array_column($timeEntries, 'duree'));
        $tempsFacturable = array_sum(
            array_map(fn($e) => $e['facturable'] ? $e['duree'] : 0, $timeEntries)
        );

        return view('tickets.show', compact('ticket', 'timeEntries', 'totalTemps', 'tempsFacturable'));
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
