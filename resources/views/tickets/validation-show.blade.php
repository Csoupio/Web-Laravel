@extends('layouts.app')

@section('title', 'Valider — ' . $ticket['nom'])
@section('header-title', 'Validation de facturation')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/validation.css') }}">
@endpush

@section('content')
<div class="corp">

    {{-- ═══ Colonne gauche : détail du ticket ═══ --}}
    <div class="left-panel">

        <div class="tuile">
            <p class="titre">{{ $ticket['nom'] }}</p>
            <p class="text-muted">Projet : <strong>{{ $ticket['projetNom'] }}</strong></p>

            <div class="mb-12">
                @php $v = $ticket['validation_client']; @endphp
                @if($v === 'en_attente')
                    <span class="fact-badge fact-badge--attente">En attente de validation</span>
                @elseif($v === 'accepte')
                    <span class="fact-badge fact-badge--accepte">Facturation acceptée</span>
                @elseif($v === 'refuse')
                    <span class="fact-badge fact-badge--refuse">Facturation refusée</span>
                @endif
                <span class="fact-badge fact-badge--facturable">Facturable en supplément</span>
            </div>

            <div class="mb-10">
                <strong>Statut du ticket :</strong> {{ $ticket['statut'] }}
            </div>

            @if($v === 'refuse' && !empty($ticket['commentaire_refus']))
                <div class="refusal-motif">
                    <strong>Motif du refus :</strong> {{ $ticket['commentaire_refus'] }}
                </div>
            @endif

            <div class="description-section">
                <strong>Description :</strong>
                <p>{{ $ticket['description'] ?? 'Aucune description.' }}</p>
            </div>
        </div>

        {{-- Temps passé --}}
        <div class="tuile">
            <p class="titre">Temps passé</p>

            <div style="display:flex; gap:12px; flex-wrap:wrap;" class="mb-12">
                <div class="time-badge time-badge--total">
                    <span class="time-badge__label">Total</span>
                    <span class="time-badge__value">{{ number_format($totalTemps, 2) }}h</span>
                </div>
                <div class="time-badge time-badge--billable">
                    <span class="time-badge__label">Facturable</span>
                    <span class="time-badge__value">{{ number_format($tempsFacturable, 2) }}h</span>
                </div>
                <div class="time-badge time-badge--non-billable">
                    <span class="time-badge__label">Non fact.</span>
                    <span class="time-badge__value">{{ number_format($totalTemps - $tempsFacturable, 2) }}h</span>
                </div>
                @if(($ticket['temps_estime'] ?? 0) > 0)
                    <div class="time-badge time-badge--estimate">
                        <span class="time-badge__label">Estimé</span>
                        <span class="time-badge__value">{{ $ticket['temps_estime'] }}h</span>
                    </div>
                @endif
            </div>

            @if(count($timeEntries) > 0)
                <div class="table-wrapper">
                    <table class="Tickets-table validation-table">
                        <thead class="Tickets-head">
                            <tr>
                                <th>Date</th><th>Collab</th><th>Durée</th><th>Fact.</th><th>Commentaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timeEntries as $entry)
                                <tr class="Tickets-ligne">
                                    <td>{{ \Carbon\Carbon::parse($entry->date)->format('d/m/Y') }}</td>
                                    <td>{{ $entry->user->name ?? 'N/A' }}</td>
                                    <td><strong>{{ number_format($entry->duree, 2) }}h</strong></td>
                                    <td>{!! $entry->facturable ? '<span class="text-success">✓</span>' : '<span class="text-muted">✗</span>' !!}</td>
                                    <td style="white-space:normal;">{{ $entry->description ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">TOTAL</td>
                                <td>{{ number_format($totalTemps, 2) }}h</td>
                                <td class="text-success">{{ number_format($tempsFacturable, 2) }}h fact.</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="text-muted">Aucune entrée de temps.</p>
            @endif
        </div>
    </div>

    {{-- ═══ Colonne droite : actions ═══ --}}
    <div class="right-panel">

        @if(session('success_fact'))
            <div class="toast show mb-12" style="position:static;">{{ session('success_fact') }}</div>
        @endif

        @if(($role ?? '') === 'Client' && $v === 'en_attente')

            <div class="tuile action-card--accept">
                <p class="titre text-success">Accepter la facturation</p>
                <p class="text-muted small">En acceptant, vous confirmez que {{ number_format($tempsFacturable, 2) }}h peuvent être facturées.</p>
                <form action="{{ route('facturation.accepter', $ticket['id']) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-add-comment w-full" style="background:#22c55e;">Accepter</button>
                </form>
            </div>

            <div class="tuile action-card--refuse">
                <p class="titre text-danger">Refuser la facturation</p>
                <form action="{{ route('facturation.refuser', $ticket['id']) }}" method="POST" class="form-creation" data-confirm="Confirmer le refus de facturation ?">
                    @csrf
                    <div class="form-group">
                        <label>Motif du refus (optionnel)</label>
                        <textarea name="commentaire_refus" class="textzone w-full" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn-danger w-full">Refuser</button>
                </form>
            </div>

        @elseif($v === 'accepte')
            <div class="tuile action-card--accept">
                <p class="titre text-success">Facturation acceptée</p>
                <p class="text-muted small">Le temps facturable est de <strong>{{ number_format($tempsFacturable, 2) }}h</strong>.</p>
            </div>

        @elseif($v === 'refuse')
            <div class="tuile action-card--refuse">
                <p class="titre text-danger">Facturation refusée</p>
                <p class="text-muted small">Le ticket est repassé en statut « Refusé par client ».</p>
            </div>

        @elseif($v === 'en_attente')
            <div class="tuile action-card--pending">
                <p class="titre text-warning">En attente de validation</p>
                <p class="text-muted small">Seul le client peut accepter ou refuser la facturation.</p>
            </div>
        @endif

        <a href="{{ route('facturation.validation.index') }}">
            <button type="button" class="btn-staff w-full mt-10">← Retour à la liste</button>
        </a>
    </div>
</div>
@endsection
