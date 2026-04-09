<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Projet;
use App\Models\TimeEntry;
use Illuminate\Support\Facades\Auth;
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

        $ticket = Ticket::findOrFail($ticketId);
        $ticket->update([
            'mode_facturation' => $mode,
            'facturable_auto'  => false,
            // Si on repasse en inclus, on annule la validation éventuelle
            'validation_client'  => $mode === 'inclus' ? null : $ticket->validation_client,
            'commentaire_refus'  => $mode === 'inclus' ? null : $ticket->commentaire_refus,
        ]);

        return back()->with('success_fact', 'Mode de facturation mis à jour.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PASSAGE AUTOMATIQUE en facturable quand le contrat est épuisé
    // Appelé depuis TicketController::show() ou TimeEntryController::store()
    // ─────────────────────────────────────────────────────────────────────────
    public static function checkAndSwitchAuto(int $projetId): void
    {
        $projet = Projet::find($projetId);

        if (!$projet || $projet->contrat_heures <= 0) {
            return; // Pas de contrat défini, rien à faire
        }

        // Heures consommées sur les tickets INCLUS du projet
        $heuresConsommees = TimeEntry::whereHas('ticket', function($q) use ($projetId) {
            $q->where('projet_id', $projetId)
              ->where('mode_facturation', 'inclus');
        })->sum('duree');

        if ($heuresConsommees < $projet->contrat_heures) {
            return; // Contrat pas encore épuisé
        }

        // Le contrat est épuisé : passe tous les tickets ouverts/en cours en facturable auto
        Ticket::where('projet_id', $projetId)
            ->where('mode_facturation', 'inclus')
            ->whereIn('statut', ['Ouvert', 'En cours'])
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
        $user = Auth::user();
        if (!$user) return redirect()->route('login');
        
        if (!in_array($user->role, ['Collaborateur', 'Administrateur'])) {
            return back()->with('error', 'Action non autorisée.');
        }

        $ticket = Ticket::findOrFail($ticketId);

        if ($ticket->mode_facturation !== 'facturable') {
            return back()->with('error', 'Ce ticket n\'est pas facturable.');
        }

        $ticket->update([
            'validation_client' => 'en_attente',
            'commentaire_refus' => null,
            'statut'            => 'En attente de validation',
        ]);

        return back()->with('success_fact', 'Ticket soumis à la validation client.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VALIDATION CLIENT : accepter
    // ─────────────────────────────────────────────────────────────────────────
    public function accepter($ticketId)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        if ($user->role !== 'Client') {
            return back()->with('error', 'Seul le client peut valider la facturation.');
        }

        $ticket = Ticket::findOrFail($ticketId);

        if ($ticket->validation_client !== 'en_attente') {
            return back()->with('error', 'Ce ticket n\'est pas en attente de validation.');
        }

        // Vérifier que le client a accès à ce ticket
        if (!$this->clientOwnsTicket($user, $ticket->projet_id)) {
            abort(403, 'Vous n\'avez pas accès à ce ticket.');
        }

        $ticket->update([
            'validation_client' => 'accepte',
            'commentaire_refus' => null,
            'statut'            => 'Facturation acceptée',
        ]);

        return back()->with('success_fact', 'Facturation acceptée.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VALIDATION CLIENT : refuser
    // ─────────────────────────────────────────────────────────────────────────
    public function refuser(Request $request, $ticketId)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        if ($user->role !== 'Client') {
            return back()->with('error', 'Seul le client peut refuser la facturation.');
        }

        $ticket = Ticket::findOrFail($ticketId);

        if ($ticket->validation_client !== 'en_attente') {
            return back()->with('error', 'Ce ticket n\'est pas en attente de validation.');
        }

        // Vérifier que le client a accès à ce ticket
        if (!$this->clientOwnsTicket($user, $ticket->projet_id)) {
            abort(403, 'Vous n\'avez pas accès à ce ticket.');
        }

        $ticket->update([
            'validation_client' => 'refuse',
            'commentaire_refus' => $request->input('commentaire_refus'),
            'statut'            => 'Refusé par client',
        ]);

        return back()->with('success_fact', 'Facturation refusée. Le ticket est repassé en statut « Refusé par client ».');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PAGE : liste des tickets en attente de validation (vue client)
    // ─────────────────────────────────────────────────────────────────────────
    public function validationIndex()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $query = Ticket::with('projet')
            ->whereIn('validation_client', ['en_attente', 'accepte', 'refuse']);

        // Le client ne voit que ses propres tickets
        if ($user->role === 'Client') {
            $client = $user->client;
            if (!$client) {
                return view('tickets.validation-index', ['tickets' => [], 'role' => $user->role]);
            }
            $query->whereHas('projet', function($q) use ($client) {
                $q->where('client_id', $client->id);
            });
        }

        $tickets = $query
            ->orderByRaw("CASE validation_client WHEN 'en_attente' THEN 1 WHEN 'refuse' THEN 2 WHEN 'accepte' THEN 3 END")
            ->get()->map(function($t) {
                $arr = $t->toArray();
                $arr['projetNom'] = $t->projet->nom ?? 'N/A';
                return $arr;
            })->toArray();

        return view('tickets.validation-index', [
            'tickets' => $tickets,
            'role' => $user->role
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PAGE : détail d'un ticket pour le client
    // ─────────────────────────────────────────────────────────────────────────
    public function validationShow($ticketId)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $ticket = Ticket::with(['projet', 'timeEntries.user'])->find($ticketId);

        if (!$ticket) abort(404);

        // Vérifier l'accès
        if ($user->role === 'Client' && !$this->clientOwnsTicket($user, $ticket->projet_id)) {
            abort(403, 'Vous n\'avez pas accès à ce ticket.');
        }

        $timeEntries = $ticket->timeEntries;
        $totalTemps      = $timeEntries->sum('duree');
        $tempsFacturable = $timeEntries->where('facturable', true)->sum('duree');

        // On convertit pour la vue
        $ticketArr = $ticket->toArray();
        $ticketArr['projetNom'] = $ticket->projet->nom ?? 'N/A';

        return view('tickets.validation-show', [
            'ticket' => $ticketArr,
            'timeEntries' => $timeEntries,
            'totalTemps' => $totalTemps,
            'tempsFacturable' => $tempsFacturable,
            'role' => $user->role
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Utilitaire : vérifie qu'un user (client) possède le projet du ticket
    // ─────────────────────────────────────────────────────────────────────────
    private function clientOwnsTicket($user, int $projetId): bool
    {
        $client = $user->client;
        if (!$client) return false;
        
        return $client->projets()->where('id', $projetId)->exists();
    }
}
