<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimeEntryController extends Controller
{
    public function store(Request $request, $ticketId)
    {
        $request->validate([
            'date'        => 'required|date',
            'duree'       => 'required|numeric|min:0.25',
            'commentaire' => 'nullable|string|max:1000',
            'facturable'  => 'nullable|boolean',
        ]);

        $sessionUser = session('user');
        if (!$sessionUser) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }

        $userId = is_array($sessionUser) ? $sessionUser['id'] : $sessionUser->id;
        $role   = is_array($sessionUser) ? $sessionUser['role'] : $sessionUser->role;

        // Seuls les collaborateurs peuvent saisir du temps
        if ($role !== 'Collaborateur') {
            return back()->with('error', 'Seuls les collaborateurs peuvent enregistrer du temps.');
        }

        // Vérifier que le collaborateur est assigné au projet du ticket
        $ticket = DB::table('ticket')->where('ID', $ticketId)->first();
        if (!$ticket) abort(404);

        $isAssigned = DB::table('projet_user')
            ->where('projet_id', $ticket->IDProjet)
            ->where('user_id', $userId)
            ->exists();

        if (!$isAssigned) {
            return back()->with('error', 'Vous n\'êtes pas assigné à ce projet.');
        }

        DB::table('time_entries')->insert([
            'IDTicket'    => $ticketId,
            'IDUser'      => $userId,
            'date'        => $request->input('date'),
            'duree'       => $request->input('duree'),
            'commentaire' => $request->input('commentaire'),
            'facturable'  => $request->boolean('facturable', true) ? 1 : 0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Après chaque saisie, on vérifie si le contrat est épuisé
        $ticket = DB::table('ticket')->where('ID', $ticketId)->first();
        if ($ticket) {
            FacturationController::checkAndSwitchAuto($ticket->IDProjet);
        }

        return redirect()->route('tickets.show', $ticketId)
                         ->with('success_time', 'Temps enregistré avec succès.');
    }

    public function destroy($id)
    {
        $entry = DB::table('time_entries')->where('ID', $id)->first();
        if (!$entry) abort(404);

        $ticketId = $entry->IDTicket;
        DB::table('time_entries')->where('ID', $id)->delete();

        return redirect()->route('tickets.show', $ticketId)
                         ->with('success_time', 'Entrée supprimée.');
    }

    public function projetReport($projetId)
    {
        $projet = DB::table('projets')->where('ID', $projetId)->first();
        if (!$projet) abort(404);

        $entries = DB::table('time_entries')
            ->join('ticket', 'time_entries.IDTicket', '=', 'ticket.ID')
            ->join('users', 'time_entries.IDUser', '=', 'users.id')
            ->select('time_entries.*', 'ticket.Nom as ticketNom', 'users.name as userName')
            ->where('ticket.IDProjet', $projetId)
            ->orderBy('time_entries.date', 'desc')
            ->get()->map(fn($i) => (array) $i)->toArray();

        $totalHeures          = array_sum(array_column($entries, 'duree'));
        $heuresFacturables    = array_sum(array_map(fn($e) => $e['facturable'] ? $e['duree'] : 0, $entries));
        $heuresNonFacturables = $totalHeures - $heuresFacturables;

        $parTicket = [];
        foreach ($entries as $e) {
            $key = $e['IDTicket'];
            if (!isset($parTicket[$key])) {
                $parTicket[$key] = ['ticketNom' => $e['ticketNom'], 'total' => 0, 'facturable' => 0];
            }
            $parTicket[$key]['total']      += $e['duree'];
            $parTicket[$key]['facturable'] += $e['facturable'] ? $e['duree'] : 0;
        }

        $contratHeures  = $projet->contrat_heures ?? 0;
        $heuresIncluses = DB::table('time_entries')
            ->join('ticket', 'time_entries.IDTicket', '=', 'ticket.ID')
            ->where('ticket.IDProjet', $projetId)
            ->where('ticket.mode_facturation', 'inclus')
            ->sum('time_entries.duree');
        $soldeContrat = max(0, $contratHeures - $heuresIncluses);

        return view('projets.time-report', compact(
            'projet', 'entries', 'totalHeures', 'heuresFacturables',
            'heuresNonFacturables', 'parTicket',
            'contratHeures', 'heuresIncluses', 'soldeContrat'
        ));
    }
}
