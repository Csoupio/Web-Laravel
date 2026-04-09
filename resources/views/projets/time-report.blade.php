@extends('layouts.app')

@section('title', 'Rapport de temps — ' . $projet->nom)
@section('header-title', 'Rapport de temps')

@section('content')
<div style="padding:15px; width:100%; box-sizing:border-box;">

    {{-- En-tête projet --}}
    <div class="tuile" style="margin-bottom:16px;">
        <p class="titre">Rapport de temps — {{ $projet->nom }}</p>
        <p style="color:#666; font-size:13px;">
            Suivi complet du temps passé et des heures facturables
        </p>
    </div>

    {{-- Cartes de synthèse --}}
    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px;">
        <div class="card" style="position:static; transform:none; top:auto; left:auto; min-width:160px;">
            <p class="card-title">Total heures</p>
            <p class="card-value" style="color:#6c6cff;">{{ number_format($totalHeures, 2) }}h</p>
        </div>
        <div class="card" style="position:static; transform:none; top:auto; left:auto; min-width:160px;">
            <p class="card-title">Heures facturables</p>
            <p class="card-value" style="color:#22c55e;">{{ number_format($heuresFacturables, 2) }}h</p>
        </div>
        <div class="card" style="position:static; transform:none; top:auto; left:auto; min-width:160px;">
            <p class="card-title">Non facturables</p>
            <p class="card-value" style="color:#ef4444;">{{ number_format($heuresNonFacturables, 2) }}h</p>
        </div>
        @if($totalHeures > 0)
        <div class="card" style="position:static; transform:none; top:auto; left:auto; min-width:160px;">
            <p class="card-title">% facturable</p>
            <p class="card-value" style="color:#f59e0b;">
                {{ number_format(($heuresFacturables / $totalHeures) * 100, 0) }}%
            </p>
        </div>
        @endif
    </div>

    {{-- Synthèse par ticket --}}
    @if(count($parTicket) > 0)
    <div class="tuile" style="margin-bottom:16px;">
        <p class="titre">Temps par ticket</p>
        <div class="table-wrapper">
            <table class="Tickets-table" style="font-size:13px;">
                <thead class="Tickets-head">
                    <tr>
                        <th>Ticket</th>
                        <th>Total passé</th>
                        <th>Facturable</th>
                        <th>Non facturable</th>
                        <th>% facturable</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parTicket as $ticketId => $data)
                        @php
                            $nonFact = $data['total'] - $data['facturable'];
                            $pct = $data['total'] > 0
                                ? round(($data['facturable'] / $data['total']) * 100)
                                : 0;
                        @endphp
                        <tr class="Tickets-ligne">
                            <td>
                                <a href="{{ route('tickets.show', $ticketId) }}"
                                   style="color:#6c6cff; text-decoration:none;">
                                    {{ $data['ticketNom'] }}
                                </a>
                            </td>
                            <td><strong>{{ number_format($data['total'], 2) }}h</strong></td>
                            <td style="color:#22c55e; font-weight:700;">
                                {{ number_format($data['facturable'], 2) }}h
                            </td>
                            <td style="color:#ef4444;">
                                {{ number_format($nonFact, 2) }}h
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="flex:1; background:#e5e7eb; border-radius:4px; height:8px;">
                                        <div style="width:{{ $pct }}%; background:#22c55e; height:100%; border-radius:4px;"></div>
                                    </div>
                                    <span style="font-size:12px; color:#555;">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Détail de toutes les entrées --}}
    <div class="tuile">
        <p class="titre">Toutes les entrées de temps</p>

        @if(count($entries) > 0)
            <div class="table-wrapper">
                <table class="Tickets-table" style="font-size:13px;">
                    <thead class="Tickets-head">
                        <tr>
                            <th>Date</th>
                            <th>Ticket</th>
                            <th>Collaborateur</th>
                            <th>Durée</th>
                            <th>Facturable</th>
                            <th>Commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entries as $entry)
                            <tr class="Tickets-ligne">
                                <td>{{ \Carbon\Carbon::parse($entry->date)->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('tickets.show', $entry->ticket_id) }}"
                                       style="color:#6c6cff; text-decoration:none;">
                                        {{ $entry->ticket->nom ?? 'Ticket supprimé' }}
                                    </a>
                                </td>
                                <td>{{ $entry->user->name ?? 'Anonyme' }}</td>
                                <td><strong>{{ number_format($entry->duree, 2) }}h</strong></td>
                                <td>
                                    @if($entry->facturable)
                                        <span style="color:green; font-weight:700;">✓ Facturable</span>
                                    @else
                                        <span style="color:#999;">✗ Non fact.</span>
                                    @endif
                                </td>
                                <td style="max-width:250px; white-space:normal;">
                                    {{ $entry->description ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:#f0f0f0; font-weight:700;">
                            <td colspan="3" style="padding:10px; border:2px solid #333;">TOTAL</td>
                            <td style="padding:10px; border:2px solid #333;">
                                {{ number_format($totalHeures, 2) }}h
                            </td>
                            <td style="padding:10px; border:2px solid #333; color:#22c55e;">
                                {{ number_format($heuresFacturables, 2) }}h fact.
                            </td>
                            <td style="border:2px solid #333;"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <p style="color:#aaa; font-size:13px;">Aucune entrée de temps enregistrée sur ce projet.</p>
        @endif

        <div style="margin-top:16px;">
            <a href="{{ route('projets.show', $projet->id) }}">
                <button type="button" class="btn-staff">← Retour au projet</button>
            </a>
        </div>
    </div>

</div>
@endsection
