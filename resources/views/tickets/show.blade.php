@extends('layouts.app')

@section('title', $ticket['Nom'])
@section('header-title', 'Détails du ticket')

@section('content')

{{-- Calcul des classes CSS selon priorité et statut --}}
@php
    $priorityClass = match(strtolower($ticket['Priorité'] ?? '')) {
        'haute'   => 'priority-high',
        'moyenne' => 'priority-medium',
        'basse'   => 'priority-low',
        default   => ''
    };
    $statusClass = match(strtolower($ticket['Status'] ?? '')) {
        'en cours' => 'en-cours',
        'terminé'  => 'priority-low',
        'bloqué'   => 'priority-high',
        default    => ''
    };
@endphp

<div class="corp">

    {{-- Colonne gauche --}}
    <div class="left-panel">

        <div class="ticket-details tuile">
            <p class="titre">{{ $ticket['Nom'] }}</p>
            <p style="color:#666; font-size:13px; margin-bottom:8px;">
                Projet : <strong>{{ $ticket['projetNom'] }}</strong>
            </p>
            <div class="desc">
                <p>{{ nl2br($ticket['Descritpion'] ?? '') }}</p>
            </div>
        </div>

        <div class="chat tuile">
            <h3>Commentaires</h3>
            <div class="chat-log">
                @forelse($commentaires ?? [] as $comment)
                    <p class="comment">{{ $comment }}</p>
                @empty
                    <p style="color:#aaa; font-size:13px;">Aucun commentaire pour l'instant.</p>
                @endforelse
            </div>
            <form action="{{ route('tickets.comment', $ticket['ID']) }}" method="POST">
                @csrf
                <textarea class="new-comment"
                          name="commentaire"
                          placeholder="Ajouter un commentaire..."
                          rows="4"></textarea>
                <button type="submit" class="btn-add-comment">Ajouter</button>
            </form>
        </div>

    </div>

    {{-- Colonne droite --}}
    <div class="right-panel">

        <div class="status tuile">
            <p class="titre">Statut :</p>
            <p class="{{ $statusClass }}">{{ $ticket['Status'] }}</p>

            <p class="titre">Priorité :</p>
            <p class="{{ $priorityClass }}">{{ $ticket['Priorité'] ?? '-' }}</p>

            <p class="titre">Type de ticket :</p>
            <p>{{ $ticket['Type'] ?? '-' }}</p>
        </div>

        <div class="temps tuile">
            <p class="titre">Temps estimé :</p>
            <p class="temp-estimee">{{ $ticket['Temps_Estime'] ?? '0' }}h</p>
            <p class="titre">Temps réel passé :</p>
            <progress max="{{ (int)($ticket['Temps_Estime'] ?? 0) }}" value="0"></progress>
        </div>

        
        
        <a href="{{ route('projets.show', $ticket['IDProjet']) }}">
            <button type="button" class="btn-staff tuile">← Retour au projet</button>
        </a>

    </div>

</div>
@endsection
