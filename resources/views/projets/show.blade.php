@extends('layouts.app')

@section('title', $projet['Nom'])
@section('header-title', 'Détail du projet')

@section('content')
<div class="corp">

    <div class="left-panel">
        <div class="tuile">
            <p class="titre">{{ $projet['Nom'] }}</p>
            <p style="color:#666; font-size:13px;">
                Client : <strong>{{ $projet['clientNom'] }}</strong>
            </p>
            <p style="margin-top:12px;">{{ $projet['Description'] ?? 'Aucune description.' }}</p>
        </div>

        <div class="tuile">
            <h3>Tickets du projet</h3>
            <div class="table-wrapper">
                <table class="Tickets-table">
                    <thead class="Tickets-head">
                        <tr>
                            <th>ID</th><th>Nom</th><th>Statut</th><th>Priorité</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr class="Tickets-ligne">
                                <td>{{ $ticket['ID'] }}</td>
                                <td>{{ $ticket['Nom'] }}</td>
                                <td>{{ $ticket['Status'] }}</td>
                                <td>{{ $ticket['Priorité'] ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('tickets.show', $ticket['ID']) }}">
                                        <button class="btn-add-comment" type="button">Voir</button>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; padding:20px;">
                                    Aucun ticket pour ce projet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="right-panel">
        <div class="tuile">
            <p class="titre">Informations</p>
            <p><strong>ID projet :</strong> {{ $projet['ID'] }}</p>
            <p><strong>Client :</strong> {{ $projet['clientNom'] }}</p>
            <p><strong>Tickets :</strong> {{ count($tickets) }}</p>
        </div>

        <div class="collaborateur tuile">
            <p class="titre">Collaborateurs :</p>
            <div class="avatar-stack">
                @forelse($collaborateurs as $collab)
                    <div class="avatar" title="{{ $collab['name'] }}">
                        {{ strtoupper(substr($collab['name'], 0, 1)) }}
                    </div>
                @empty
                    <span style="color:#aaa; font-size:13px;">Aucun collaborateur assigné.</span>
                @endforelse
            </div>
        </div>

        @php
            $sessionUser = session('user');
            $role = is_array($sessionUser) ? ($sessionUser['role'] ?? '') : ($sessionUser->role ?? '');
        @endphp

        {{-- Seuls admin et collaborateurs peuvent créer des tickets --}}
        @if($role !== 'Client')
            <div style="display:flex; justify-content:flex-end; margin-bottom:12px;">
                <a href="{{ route('tickets.create', $projet['ID']) }}">
                    <button type="button" class="btn-add-comment">+ Nouveau ticket</button>
                </a>
            </div>
        @endif

        <a href="{{ route('projets.index') }}">
            <button type="button" class="btn-staff">← Retour aux projets</button>
        </a>
    </div>

</div>
@endsection
