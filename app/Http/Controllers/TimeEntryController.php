<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeEntry;
use App\Models\Ticket;
use App\Models\Projet;
use Illuminate\Support\Facades\Auth;

class TimeEntryController extends Controller
{
    public function store(Request $request, $ticketId)
    {
        $request->validate([
            'date'        => 'required|date',
            'duree'       => 'required|numeric|min:0.25',
            'description' => 'nullable|string|max:1000',
            'facturable'  => 'nullable|boolean',
        ]);

        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        // Seuls les collaborateurs peuvent saisir du temps
        if ($user->role !== 'Collaborateur') {
            return back()->with('error', 'Seuls les collaborateurs peuvent enregistrer du temps.');
        }

        // Vérifier que le collaborateur est assigné au projet du ticket
        $ticket = Ticket::findOrFail($ticketId);

        $isAssigned = $user->projets()->where('projets.id', $ticket->projet_id)->exists();

        if (!$isAssigned) {
            return back()->with('error', 'Vous n\'êtes pas assigné à ce projet.');
        }

        TimeEntry::create([
            'ticket_id'   => $ticketId,
            'user_id'     => $user->id,
            'date'        => $request->input('date'),
            'duree'       => $request->input('duree'),
            'description' => $request->input('description'),
            'facturable'  => $request->boolean('facturable', true),
        ]);

        // Après chaque saisie, on vérifie si le contrat est épuisé
        FacturationController::checkAndSwitchAuto($ticket->projet_id);

        return redirect()->route('tickets.show', $ticketId)
                         ->with('success_time', 'Temps enregistré avec succès.');
    }

    public function destroy($id)
    {
        $entry = TimeEntry::findOrFail($id);
        $ticketId = $entry->ticket_id;
        $entry->delete();

        return redirect()->route('tickets.show', $ticketId)
                         ->with('success_time', 'Entrée supprimée.');
    }

    public function projetReport($projetId)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $projet = Projet::with(['tickets.timeEntries.user'])->findOrFail($projetId);

        $entries = TimeEntry::whereHas('ticket', function($q) use ($projetId) {
            $q->where('projet_id', $projetId);
        })->with(['ticket', 'user'])->orderBy('date', 'desc')->get();

        $totalHeures          = $entries->sum('duree');
        $heuresFacturables    = $entries->where('facturable', true)->sum('duree');
        $heuresNonFacturables = $totalHeures - $heuresFacturables;

        $parTicket = [];
        foreach ($entries as $e) {
            $key = $e->ticket_id;
            if (!isset($parTicket[$key])) {
                $parTicket[$key] = ['ticketNom' => $e->ticket->nom, 'total' => 0, 'facturable' => 0];
            }
            $parTicket[$key]['total']      += $e->duree;
            $parTicket[$key]['facturable'] += $e->facturable ? $e->duree : 0;
        }

        $contratHeures  = $projet->contrat_heures ?? 0;
        
        $heuresIncluses = TimeEntry::whereHas('ticket', function($q) use ($projetId) {
            $q->where('projet_id', $projetId)
              ->where('mode_facturation', 'inclus');
        })->sum('duree');
        
        $soldeContrat = max(0, $contratHeures - $heuresIncluses);

        return view('projets.time-report', [
            'projet' => $projet,
            'entries' => $entries,
            'totalHeures' => $totalHeures,
            'heuresFacturables' => $heuresFacturables,
            'heuresNonFacturables' => $heuresNonFacturables,
            'parTicket' => $parTicket,
            'contratHeures' => $contratHeures,
            'heuresIncluses' => $heuresIncluses,
            'soldeContrat' => $soldeContrat
        ]);
    }
}

