<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimeEntryController extends Controller
{
    /**
     * Enregistrer une nouvelle entrée de temps sur un ticket.
     */
    public function store(Request $request, $ticketId)
    {
        $request->validate([
            'date'        => 'required|date',
            'duree'       => 'required|numeric|min:0.25',
            'commentaire' => 'nullable|string|max:1000',
            'facturable'  => 'nullable|boolean',
        ]);

        // Récupère l'utilisateur depuis la session (auth custom du projet)
        $user = session('user');
        $userId = $user ? $user->id : 1; // fallback pour les tests

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

        return redirect()->route('tickets.show', $ticketId)
                         ->with('success_time', 'Temps enregistré avec succès.');
    }

    /**
     * Supprimer une entrée de temps.
     */
    public function destroy($id)
    {
        $entry = DB::table('time_entries')->where('ID', $id)->first();

        if (!$entry) {
            abort(404);
        }

        $ticketId = $entry->IDTicket;

        DB::table('time_entries')->where('ID', $id)->delete();

        return redirect()->route('tickets.show', $ticketId)
                         ->with('success_time', 'Entrée supprimée.');
    }

    /**
     * Rapport de temps par projet — utilisé par le résumé de contrat.
     */
    public function projetReport($projetId)
    {
        $projet = DB::table('projets')->where('ID', $projetId)->first();

        if (!$projet) {
            abort(404);
        }

        // Toutes les entrées de temps rattachées aux tickets du projet
        $entries = DB::table('time_entries')
            ->join('ticket', 'time_entries.IDTicket', '=', 'ticket.ID')
            ->join('users', 'time_entries.IDUser', '=', 'users.id')
            ->select(
                'time_entries.*',
                'ticket.Nom as ticketNom',
                'users.name as userName'
            )
            ->where('ticket.IDProjet', $projetId)
            ->orderBy('time_entries.date', 'desc')
            ->get()
            ->map(fn($i) => (array) $i)
            ->toArray();

        // Agrégats
        $totalHeures      = array_sum(array_column($entries, 'duree'));
        $heuresFacturables = array_sum(
            array_map(fn($e) => $e['facturable'] ? $e['duree'] : 0, $entries)
        );
        $heuresNonFacturables = $totalHeures - $heuresFacturables;

        // Temps par ticket
        $parTicket = [];
        foreach ($entries as $e) {
            $key = $e['IDTicket'];
            if (!isset($parTicket[$key])) {
                $parTicket[$key] = [
                    'ticketNom'   => $e['ticketNom'],
                    'total'       => 0,
                    'facturable'  => 0,
                ];
            }
            $parTicket[$key]['total']      += $e['duree'];
            $parTicket[$key]['facturable'] += $e['facturable'] ? $e['duree'] : 0;
        }

        return view('projets.time-report', compact(
            'projet',
            'entries',
            'totalHeures',
            'heuresFacturables',
            'heuresNonFacturables',
            'parTicket'
        ));
    }
}
