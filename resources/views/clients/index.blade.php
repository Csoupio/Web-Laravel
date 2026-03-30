@extends('layouts.app')

@section('title', 'Dashboard client')
@section('header-title', 'Votre espace clients')

@section('content')
<div style="padding: 15px; width: 100%; box-sizing: border-box;">

    {{-- Dashboard avec les tuiles de stats --}}
    <div class="tuile" style="margin-bottom: 20px;">
        <h1 style="margin: 0 0 15px 0;">Votre dashboard</h1>
        <div class="cards">
            <div class="card">
                <h3 class="card-title">Tickets ouverts</h3>
                <p class="card-value">{{ $ticketsOuverts }}</p>
            </div>
            <div class="card">
                <h3 class="card-title">Projets actifs</h3>
                <p class="card-value">{{ $projetsActifs }}</p>
            </div>
        </div>
    </div>

    {{-- Tableau des tickets --}}
    <div class="tuile">
        <h2 style="margin: 0 0 15px 0;">Aperçu de vos Tickets</h2>

        <section id="filtre" style="margin-bottom: 15px;">
            <div id="status" class="status" style="margin-bottom: 8px;">
                <label>Filtrer par statut :</label>
                <button value="" class="filter-btn">Tous</button>
                <button value="ouvert" class="filter-btn">Ouvert</button>
                <button value="en-cours" class="filter-btn">En cours</button>
                <button value="termine" class="filter-btn">Terminé</button>
            </div>
            <div id="priority" class="status">
                <label>Filtrer par priorité :</label>
                <button class="filter-btn-Statut">Tous</button>
                <button class="filter-btn-Statut">Haute</button>
                <button class="filter-btn-Statut">Moyenne</button>
                <button class="filter-btn-Statut">Basse</button>
            </div>
        </section>

        <div class="table-wrapper">
            <table class="Tickets-table">
                <thead class="Tickets-head">
                    <tr>
                        <th>ID</th>
                        <th>Projet</th>
                        <th>Nom du ticket</th>
                        <th>Statut</th>
                        <th>Priorité</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr class="Tickets-ligne"
                            onclick="window.location.href='{{ route('tickets.show', $ticket['ID']) }}'"
                            style="cursor:pointer;">
                            <td>{{ $ticket['ID'] }}</td>
                            <td>{{ $ticket['projetNom'] }}</td>
                            <td>{{ $ticket['Nom'] }}</td>
                            <td class="status-cell">{{ $ticket['Status'] }}</td>
                            <td class="priority-cell">{{ $ticket['Priorité'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:20px;">
                                Aucun ticket trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
