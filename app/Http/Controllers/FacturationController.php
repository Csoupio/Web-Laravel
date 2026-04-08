<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacturationController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // CHANGEMENT MANUEL du mode (inclus ↔ facturable)
    // Accessible à un collaborateur ou un administrateur
    // ─────────────────────────────────────────────────────────────────────────
    public function setMode(Request $request, $ticketId)
    {
        $mode = $request->input('mode_facturation');

        if (!in_array($mode, ['inclus', 'facturable'])) {
            return back()->with('error', 'Mode de facturation invalide.');
        }

        DB::table('ticket')->where('ID', $ticketId)->update([
            'mode_facturation' => $mode,
            'facturable_auto'  => false,
            // Si on repasse en inclus, on annule la validation éventuelle
            'validation_client'  => $mode === 'inclus' ? null : DB::raw('validation_client'),
            'commentaire_refus'  => $mode === 'inclus' ? null : DB::raw('commentaire_refus'),
        ]);

        return back()->with('success_fact', 'Mode de facturation mis à jour.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PASSAGE AUTOMATIQUE en facturable quand le contrat est épuisé
    // Appelé depuis TicketController::show() ou TimeEntryController::store()
    // ─────────────────────────────────────────────────────────────────────────
    public static function checkAndSwitchAuto(int $projetId): void
    {
        $projet = DB::table('projets')->where('ID', $projetId)->first();

        if (!$projet || $projet->contrat_heures <= 0) {
            return; // Pas de contrat défini, rien à faire
        }

        // Heures consommées sur les tickets INCLUS du projet
        $heuresConsommees = DB::table('time_entries')
            ->join('ticket', 'time_entries.IDTicket', '=', 'ticket.ID')
            ->where('ticket.IDProjet', $projetId)
            ->where('ticket.mode_facturation', 'inclus')
            ->sum('time_entries.duree');

        if ($heuresConsommees < $projet->contrat_heures) {
            return; // Contrat pas encore épuisé
        }

        // Le contrat est épuisé : passe tous les tickets ouverts/en cours en facturable auto
        DB::table('ticket')
            ->where('IDProjet', $projetId)
            ->where('mode_facturation', 'inclus')
            ->whereIn('Status', ['Ouvert', 'En cours'])
            ->update([
                'mode_facturation' => 'facturable',
                'facturable_auto'  => true,
            ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SOUMETTRE un ticket facturable à la validation du client
    // ─────────────────────────────────────────────────────────────────────────
    public function soumettre($ticketId)
    {
        $ticket = DB::table('ticket')->where('ID', $ticketId)->first();

        if (!$ticket || $ticket->mode_facturation !== 'facturable') {
            return back()->with('error', 'Ce ticket n\'est pas facturable.');
        }

        DB::table('ticket')->where('ID', $ticketId)->update([
            'validation_client' => 'en_attente',
            'commentaire_refus' => null,
        ]);

        return back()->with('success_fact', 'Ticket soumis à la validation client.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VALIDATION CLIENT : accepter
    // ─────────────────────────────────────────────────────────────────────────
    public function accepter($ticketId)
    {
        $ticket = DB::table('ticket')->where('ID', $ticketId)->first();

        if (!$ticket || $ticket->validation_client !== 'en_attente') {
            return back()->with('error', 'Ce ticket n\'est pas en attente de validation.');
        }

        DB::table('ticket')->where('ID', $ticketId)->update([
            'validation_client' => 'accepte',
            'commentaire_refus' => null,
        ]);

        return back()->with('success_fact', 'Facturation acceptée.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VALIDATION CLIENT : refuser
    // ─────────────────────────────────────────────────────────────────────────
    public function refuser(Request $request, $ticketId)
    {
        $ticket = DB::table('ticket')->where('ID', $ticketId)->first();

        if (!$ticket || $ticket->validation_client !== 'en_attente') {
            return back()->with('error', 'Ce ticket n\'est pas en attente de validation.');
        }

        DB::table('ticket')->where('ID', $ticketId)->update([
            'validation_client' => 'refuse',
            'commentaire_refus' => $request->input('commentaire_refus'),
            // Repasse en inclus pour être retraité
            'mode_facturation'  => 'inclus',
            'facturable_auto'   => false,
            'Status'            => 'En cours',
        ]);

        return back()->with('success_fact', 'Facturation refusée. Le ticket est repassé en cours.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PAGE : liste des tickets en attente de validation (vue client)
    // ─────────────────────────────────────────────────────────────────────────
    public function validationIndex()
    {
        $tickets = DB::table('ticket')
            ->join('projets', 'ticket.IDProjet', '=', 'projets.ID')
            ->select('ticket.*', 'projets.Nom as projetNom')
            ->where('ticket.mode_facturation', 'facturable')
            ->whereIn('ticket.validation_client', ['en_attente', 'accepte', 'refuse'])
            ->orderByRaw("CASE ticket.validation_client WHEN 'en_attente' THEN 1 WHEN 'refuse' THEN 2 WHEN 'accepte' THEN 3 END")
            ->get()->map(fn($i) => (array) $i)->toArray();

        return view('tickets.validation-index', compact('tickets'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PAGE : détail d'un ticket pour le client (lecture + boutons valider/refuser)
    // ─────────────────────────────────────────────────────────────────────────
    public function validationShow($ticketId)
    {
        $ticket = DB::table('ticket')
            ->join('projets', 'ticket.IDProjet', '=', 'projets.ID')
            ->select('ticket.*', 'projets.Nom as projetNom')
            ->where('ticket.ID', $ticketId)
            ->first();

        if (!$ticket) abort(404);
        $ticket = (array) $ticket;

        // Entrées de temps du ticket
        $timeEntries = DB::table('time_entries')
            ->join('users', 'time_entries.IDUser', '=', 'users.id')
            ->select('time_entries.*', 'users.name as userName')
            ->where('time_entries.IDTicket', $ticketId)
            ->orderBy('time_entries.date', 'desc')
            ->get()->map(fn($i) => (array) $i)->toArray();

        $totalTemps      = array_sum(array_column($timeEntries, 'duree'));
        $tempsFacturable = array_sum(
            array_map(fn($e) => $e['facturable'] ? $e['duree'] : 0, $timeEntries)
        );

        return view('tickets.validation-show', compact(
            'ticket', 'timeEntries', 'totalTemps', 'tempsFacturable'
        ));
    }
}
