<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Projet;
use App\Models\TimeEntry;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function show($id)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $ticket = Ticket::with(['projet', 'timeEntries.user'])->find($id);

        if (!$ticket) {
            abort(404, 'Ticket introuvable.');
        }

        // Vérification des droits d'accès
        if ($user->role === 'Collaborateur') {
            $hasAccess = $user->projets()->where('projets.id', $ticket->projet_id)->exists();
            if (!$hasAccess) abort(403, 'Accès refusé.');
        } elseif ($user->role === 'Client') {
            $client = $user->client;
            if (!$client || !$client->projets->contains($ticket->projet_id)) {
                abort(403, 'Accès refusé.');
            }
        }

        $timeEntries = $ticket->timeEntries;
        $totalTemps      = $timeEntries->sum('duree');
        $tempsFacturable = $timeEntries->where('facturable', true)->sum('duree');

        return view('tickets.show', [
            'ticket' => $ticket,
            'timeEntries' => $timeEntries,
            'totalTemps' => $totalTemps,
            'tempsFacturable' => $tempsFacturable,
            'role' => $user->role
        ]);
    }

    // Afficher le formulaire de création pour un projet donné
    public function create($projetId)
    {
        $projet = Projet::findOrFail($projetId);
        return view('tickets.create', compact('projet'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'project' => 'required|integer',
        ]);

        $ticket = Ticket::create([
            'nom'               => $request->input('title'),
            'description'       => $request->input('description'),
            'projet_id'         => $request->input('project'),
            'statut'            => 'Ouvert',
            'priorite'          => $request->input('priority'),
            'type'              => $request->input('type'),
            'temps_estime'      => $request->input('estimated_time', 0),
        ]);

        return redirect()->route('tickets.show', $ticket->id);
    }

    public function addComment(Request $request, $id)
    {
        return redirect()->route('tickets.show', $id);
    }

    /**
     * API: Store a new ticket and return JSON
     */
    public function storeApi(Request $request)
    {
        try {
            $request->validate([
                'title'       => 'required|string|max:255',
                'description' => 'nullable|string',
                'project_id'  => 'required|integer',
                'priority'    => 'nullable|string',
                'type'        => 'nullable|string',
            ]);

            $ticket = Ticket::create([
                'nom'               => $request->input('title'),
                'description'       => $request->input('description'),
                'projet_id'         => $request->input('project_id'),
                'statut'            => 'Ouvert',
                'priorite'          => $request->input('priority'),
                'type'              => $request->input('type'),
                'temps_estime'      => $request->input('estimated_time', 0),
            ]);

            return response()->json([
                'success' => true,
                'ticket'  => [
                    'id'       => $ticket->id,
                    'nom'      => $ticket->nom,
                    'status'   => $ticket->statut,
                    'priorite' => $ticket->priorite,
                    'url'      => route('tickets.show', $ticket->id)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
