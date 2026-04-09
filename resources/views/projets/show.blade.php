@extends('layouts.app')

@section('title', $projet['nom'])
@section('header-title', 'Détail du projet')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/tickets.css') }}">
    <link rel="stylesheet" href="{{ asset('css/validation.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modals.css') }}">
@endpush

@section('content')
<div class="corp">

    {{-- ── MODAL CRÉATION TICKET ── --}}
    <div id="modalCreateTicket" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2>Créer un ticket</h2>
                <button type="button" class="modal-close" onclick="closeModal('modalCreateTicket')">×</button>
            </div>
            <div class="modal-body">
                <form id="formApiCreateTicket" class="form-creation">
                    <input type="hidden" name="project_id" value="{{ $projet['id'] }}">
                    
                    <div class="form-group">
                        <label>Titre du ticket</label>
                        <input type="text" name="title" class="textzone w-full" placeholder="Ex: Problème de connexion..." required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="textzone w-full" rows="4"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <label>Priorité</label>
                            <select name="priority" class="textzone w-full">
                                <option value="Basse">Basse</option>
                                <option value="Moyenne" selected>Moyenne</option>
                                <option value="Haute">Haute</option>
                            </select>
                        </div>
                        <div class="form-col">
                            <label>Type</label>
                            <select name="type" class="textzone w-full">
                                <option value="Bug">Bug</option>
                                <option value="Évolution">Évolution</option>
                                <option value="Support" selected>Support</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-staff" style="background:#64748b;" onclick="closeModal('modalCreateTicket')">Annuler</button>
                <button type="button" class="btn-add-comment" id="btnSubmitTicket">Créer le ticket</button>
            </div>
        </div>
    </div>

    {{-- Infos projet --}}
    <div class="left-panel">
        <div class="tuile">
            <p class="titre">{{ $projet['nom'] }}</p>
            <p class="text-muted small">Client : <strong>{{ $projet['clientNom'] }}</strong></p>
            <p class="mt-10">{{ $projet['description'] ?? 'Aucune description.' }}</p>
        </div>

        {{-- Liste des tickets du projet --}}
        <div class="tuile">
            <p class="titre">Tickets du projet</p>
            <div class="table-wrapper">
                <table class="Tickets-table validation-table" id="tableTickets">
                    <thead class="Tickets-head">
                        <tr><th>ID</th><th>Nom</th><th>Statut</th><th>Priorité</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr class="Tickets-ligne">
                                <td>#{{ $ticket['id'] }}</td>
                                <td class="bold">{{ $ticket['nom'] }}</td>
                                <td><span class="fact-badge fact-badge--inclus">{{ $ticket['statut'] }}</span></td>
                                <td>{{ $ticket['priorite'] ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('tickets.show', $ticket['id']) }}">
                                        <button class="btn-add-comment small" type="button">Voir</button>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr id="noTicketsRow"><td colspan="5" class="text-muted" style="text-align:center; padding:20px;">Aucun ticket.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Panneau droit --}}
    <div class="right-panel">
        <div class="tuile">
            <p class="titre">Informations</p>
            <div class="flex-col" style="gap:4px;">
                <p class="small"><strong>ID :</strong> {{ $projet['id'] }}</p>
                <p class="small"><strong>Client :</strong> {{ $projet['clientNom'] }}</p>
                <p class="small"><strong>Tickets :</strong> <span id="ticketCount">{{ count($tickets) }}</span> active(s)</p>
            </div>
        </div>

        <div class="flex-col" style="gap:10px; margin-bottom:12px;">
            <button type="button" class="btn-add-comment w-full" onclick="openModal('modalCreateTicket')">+ Nouveau ticket</button>
            
            <a href="{{ route('projets.time-report', $projet['id']) }}" class="w-full">
                <button type="button" class="btn-add-comment w-full" style="background:#22c55e;">Rapport temps</button>
            </a>
            <a href="{{ route('projets.index') }}" class="w-full">
                <button type="button" class="btn-staff w-full">← Retour aux projets</button>
            </a>
        </div>
    </div>

</div>
@endsection
