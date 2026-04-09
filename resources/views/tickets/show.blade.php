@extends('layouts.app')

@section('title', $ticket->nom)
@section('header-title', 'Détails du ticket')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/validation.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tickets.css') }}">
@endpush

@section('content')

@php
    $priorityClass = match(strtolower($ticket->priorite ?? '')) {
        'haute'   => 'priority-high',
        'moyenne' => 'priority-medium',
        'basse'   => 'priority-low',
        default   => ''
    };
    $statusClass = match(strtolower($ticket->statut ?? '')) {
        'en cours'                   => 'en-cours',
        'terminé'                    => 'priority-low',
        'bloqué'                     => 'priority-high',
        'refusé par client'          => 'priority-high',
        'en attente de validation'   => 'en-attente',
        'facturation acceptée'       => 'priority-low',
        default                      => ''
    };
    $modeFacturation  = $ticket->mode_facturation ?? 'inclus';
    $validationClient = $ticket->validation_client ?? null;
@endphp

<div class="corp">

    {{-- ═══ Colonne gauche ═══ --}}
    <div class="left-panel">

        <div class="tuile ticket-details">
            <p class="titre">{{ $ticket->nom }}</p>
            <p class="text-muted small">Projet : <strong>{{ $ticket->projet->nom ?? 'N/A' }}</strong></p>

            <div class="mb-12">
                @if($modeFacturation === 'facturable')
                    <span class="fact-badge fact-badge--facturable">
                        Facturable en supplément
                        @if($ticket->facturable_auto)
                            <em class="small">(contrat épuisé)</em>
                        @endif
                    </span>
                @else
                    <span class="fact-badge fact-badge--inclus">✅ Inclus dans le contrat</span>
                @endif

                @if($validationClient === 'en_attente')
                    <span class="fact-badge fact-badge--attente">En attente de validation client</span>
                @elseif($validationClient === 'accepte')
                    <span class="fact-badge fact-badge--accepte">Facturation acceptée</span>
                @elseif($validationClient === 'refuse')
                    <span class="fact-badge fact-badge--refuse">Facturation refusée</span>
                @endif
            </div>

            @if($validationClient === 'refuse' && !empty($ticket->commentaire_refus))
                <div class="refusal-motif">
                    <strong>Motif du refus :</strong> {{ $ticket->commentaire_refus }}
                </div>
            @endif

            <div class="description-section">
                <p>{{ nl2br($ticket->description ?? '') }}</p>
            </div>
        </div>

        {{-- ── Bloc facturation (collaborateur / admin uniquement) ── --}}
        @if(in_array($role, ['Collaborateur', 'Administrateur']))
            <div class="tuile">
                <p class="titre">Gestion de la facturation</p>
                <div class="flex-gap-10 mb-12">
                    <form action="{{ route('facturation.mode', $ticket->id) }}" method="POST" class="auth-footer" style="display:flex; gap:8px;">
                        @csrf
                        <select name="mode_facturation" class="textzone" style="width:auto;">
                            <option value="inclus" @selected($modeFacturation === 'inclus')>Inclus dans le contrat</option>
                            <option value="facturable" @selected($modeFacturation === 'facturable')>Facturable en supplément</option>
                        </select>
                        <button type="submit" class="btn-add-comment">Appliquer</button>
                    </form>

                    @if($modeFacturation === 'facturable' && ($validationClient === null || $validationClient === 'refuse'))
                        <form action="{{ route('facturation.soumettre', $ticket->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-add-comment" style="background:#f59e0b;">
                                📤 {{ $validationClient === 'refuse' ? 'Resoumettre' : 'Soumettre validation' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        {{-- ── Bloc validation client (visible pour le rôle Client) ── --}}
        @if($role === 'Client' && $modeFacturation === 'facturable' && $validationClient !== null)
            <div class="tuile {{ $validationClient === 'en_attente' ? 'action-card--pending' : ($validationClient === 'accepte' ? 'action-card--accept' : 'action-card--refuse') }}">
                <p class="titre">Validation de facturation</p>
                <div class="mb-12">
                     <p class="small text-muted">Temps facturable : <strong>{{ number_format($tempsFacturable, 2) }}h</strong></p>
                </div>

                @if($validationClient === 'en_attente')
                    <form action="{{ route('facturation.accepter', $ticket->id) }}" method="POST" class="mb-12">
                        @csrf
                        <button type="submit" class="btn-add-comment w-full" style="background:#22c55e;">Accepter la facturation</button>
                    </form>

                    <details>
                        <summary class="btn-danger w-full" style="cursor:pointer; text-align:center;">Refuser la facturation</summary>
                        <form action="{{ route('facturation.refuser', $ticket->id) }}" method="POST" class="mt-10 flex-col" data-confirm="Confirmer le refus de facturation ?">
                            @csrf
                            <label class="bold small">Motif du refus (optionnel)</label>
                            <textarea name="commentaire_refus" class="textzone w-full" rows="3"></textarea>
                            <button type="submit" class="btn-danger w-full">Confirmer le refus</button>
                        </form>
                    </details>
                @elseif($validationClient === 'refuse' && !empty($ticket->commentaire_refus))
                    <div class="refusal-motif">
                        <strong>Votre motif de refus :</strong> {{ $ticket->commentaire_refus }}
                    </div>
                @endif
            </div>
        @endif

        {{-- Commentaires --}}
        <div class="chat tuile">
            <p class="titre">Commentaires</p>
            <div class="chat-log">
                @forelse($commentaires ?? [] as $comment)
                    <p class="comment">{{ $comment }}</p>
                @empty
                    <p class="text-muted small">Aucun commentaire.</p>
                @endforelse
            </div>
            <form action="{{ route('tickets.comment', $ticket->id) }}" method="POST">
                @csrf
                <textarea class="new-comment" name="commentaire" placeholder="Ajouter un commentaire..." rows="3"></textarea>
                <button type="submit" class="btn-add-comment">Ajouter</button>
            </form>
        </div>

        {{-- Suivi du temps --}}
        <div class="tuile">
            <p class="titre">Temps passé</p>
            <div class="flex-gap-10 mb-12">
                <div class="time-badge time-badge--total"><span class="time-badge__label">Total</span><span class="time-badge__value">{{ number_format($totalTemps, 2) }}h</span></div>
                <div class="time-badge time-badge--billable"><span class="time-badge__label">Fact.</span><span class="time-badge__value">{{ number_format($tempsFacturable, 2) }}h</span></div>
                @if($ticket->temps_estime > 0)
                    <div class="time-badge time-badge--estimate"><span class="time-badge__label">Estimé</span><span class="time-badge__value">{{ $ticket->temps_estime }}h</span></div>
                @endif
            </div>

            @if($role === 'Collaborateur')
                <details class="mb-12">
                    <summary class="btn-add-comment" style="cursor:pointer;">+ Enregistrer du temps</summary>
                    <form action="{{ route('time.store', $ticket->id) }}" method="POST" class="mt-10 flex-col">
                        @csrf
                        <div class="form-row">
                            <div class="form-col"><label class="bold small">Date</label><input type="date" name="date" class="textzone w-full" value="{{ date('Y-m-d') }}" required></div>
                            <div class="form-col"><label class="bold small">Durée (h)</label><input type="number" name="duree" class="textzone w-full" min="0.25" step="0.25" required></div>
                            <div class="form-col" style="justify-content:center; display:flex;"><label class="bold small"><input type="checkbox" name="facturable" value="1" checked> Facturable</label></div>
                        </div>
                        <textarea name="description" class="textzone w-full" placeholder="Commentaire..." rows="2"></textarea>
                        <button type="submit" class="btn-add-comment">Enregistrer</button>
                    </form>
                </details>
            @endif

            @if($timeEntries->count() > 0)
                <div class="table-wrapper">
                    <table class="Tickets-table validation-table">
                        <thead class="Tickets-head">
                            <tr><th>Date</th><th>Collab</th><th>Durée</th><th>Fact.</th>@if(in_array($role,['Collaborateur','Administrateur']))<th>Action</th>@endif</tr>
                        </thead>
                        <tbody>
                            @foreach($timeEntries as $entry)
                                <tr class="Tickets-ligne">
                                    <td>{{ \Carbon\Carbon::parse($entry->date)->format('d/m/Y') }}</td>
                                    <td>{{ $entry->user->name ?? 'N/A' }}</td>
                                    <td class="bold">{{ number_format($entry->duree, 2) }}h</td>
                                    <td>{!! $entry->facturable ? '<span class="text-success">✓</span>' : '<span class="text-muted">✗</span>' !!}</td>
                                    @if(in_array($role,['Collaborateur','Administrateur']))
                                        <td>
                                            <form action="{{ route('time.destroy', $entry->id) }}" method="POST" data-confirm="Supprimer cette entrée ?">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-danger small" style="padding:2px 8px;">Suppr.</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══ Colonne droite ═══ --}}
    <div class="right-panel">
        <div class="status tuile">
            <p class="titre">Statut :</p><p class="{{ $statusClass }} bold">{{ $ticket->statut }}</p>
            <p class="titre">Priorité :</p><p class="{{ $priorityClass }} bold">{{ $ticket->priorite ?? '-' }}</p>
        </div>

        <div class="temps tuile">
            <p class="titre">Temps :</p>
            <p class="text-muted small">Estimé : {{ $ticket->temps_estime ?? 0 }}h</p>
            <p class="bold" style="font-size:22px; color:#6c6cff;">{{ number_format($totalTemps, 2) }}h</p>
            @if($ticket->temps_estime > 0)
                <div class="progress-bar-bg mt-10"><div class="progress-bar-fill" style="width:{{ min(100, (int)($totalTemps/($ticket->temps_estime?:1)*100)) }}%; background:#6c6cff;"></div></div>
            @endif
        </div>

        <div class="flex-col" style="gap:10px;">
            <a href="{{ route('projets.show', $ticket->projet_id) }}" class="w-full">
                <button type="button" class="btn-staff w-full">← Retour au projet</button>
            </a>
        </div>
    </div>
</div>
@endsection
