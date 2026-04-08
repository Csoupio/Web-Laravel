@extends('layouts.app')

@section('title', 'Valider — ' . $ticket['Nom'])
@section('header-title', 'Validation de facturation')

@section('content')
<div class="corp">

    {{-- ═══ Colonne gauche : détail du ticket ═══ --}}
    <div class="left-panel">

        <div class="tuile">
            <p class="titre">{{ $ticket['Nom'] }}</p>
            <p style="color:#666; font-size:13px; margin-bottom:8px;">
                Projet : <strong>{{ $ticket['projetNom'] }}</strong>
            </p>

            @php $v = $ticket['validation_client']; @endphp

            <div style="margin-bottom:12px;">
                @if($v === 'en_attente')
                    <span class="fact-badge fact-badge--attente">⏳ En attente de votre validation</span>
                @elseif($v === 'accepte')
                    <span class="fact-badge fact-badge--accepte">✔ Vous avez accepté cette facturation</span>
                @elseif($v === 'refuse')
                    <span class="fact-badge fact-badge--refuse">✘ Vous avez refusé cette facturation</span>
                @endif
            </div>

            @if($v === 'refuse' && !empty($ticket['commentaire_refus']))
                <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px;
                            padding:10px; margin-bottom:10px; font-size:13px;">
                    <strong>Votre motif de refus :</strong> {{ $ticket['commentaire_refus'] }}
                </div>
            @endif

            <p style="margin-top:8px;">{{ $ticket['Descritpion'] ?? 'Aucune description.' }}</p>
        </div>

        {{-- Temps passé --}}
        <div class="tuile">
            <p class="titre">⏱ Temps passé sur ce ticket</p>

            <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:16px;">
                <div class="time-badge time-badge--total">
                    <span class="time-badge__label">Total</span>
                    <span class="time-badge__value">{{ number_format($totalTemps, 2) }}h</span>
                </div>
                <div class="time-badge time-badge--billable">
                    <span class="time-badge__label">Facturable</span>
                    <span class="time-badge__value">{{ number_format($tempsFacturable, 2) }}h</span>
                </div>
                @if(($ticket['Temps_Estime'] ?? 0) > 0)
                    <div class="time-badge time-badge--estimate">
                        <span class="time-badge__label">Estimé</span>
                        <span class="time-badge__value">{{ $ticket['Temps_Estime'] }}h</span>
                    </div>
                @endif
            </div>

            @if(count($timeEntries) > 0)
                <div class="table-wrapper">
                    <table class="Tickets-table" style="font-size:13px;">
                        <thead class="Tickets-head">
                            <tr>
                                <th>Date</th>
                                <th>Collaborateur</th>
                                <th>Durée</th>
                                <th>Facturable</th>
                                <th>Commentaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timeEntries as $entry)
                                <tr class="Tickets-ligne">
                                    <td>{{ \Carbon\Carbon::parse($entry['date'])->format('d/m/Y') }}</td>
                                    <td>{{ $entry['userName'] }}</td>
                                    <td><strong>{{ number_format($entry['duree'], 2) }}h</strong></td>
                                    <td>
                                        @if($entry['facturable'])
                                            <span style="color:green; font-weight:700;">✓</span>
                                        @else
                                            <span style="color:#999;">✗</span>
                                        @endif
                                    </td>
                                    <td style="max-width:220px; white-space:normal;">
                                        {{ $entry['commentaire'] ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:#f0f0f0; font-weight:700;">
                                <td colspan="2" style="padding:8px; border:2px solid #333;">TOTAL</td>
                                <td style="padding:8px; border:2px solid #333;">{{ number_format($totalTemps, 2) }}h</td>
                                <td style="padding:8px; border:2px solid #333; color:#22c55e;">
                                    {{ number_format($tempsFacturable, 2) }}h fact.
                                </td>
                                <td style="border:2px solid #333;"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p style="color:#aaa; font-size:13px;">Aucune entrée de temps.</p>
            @endif
        </div>

    </div>

    {{-- ═══ Colonne droite : actions client ═══ --}}
    <div class="right-panel">

        @if(session('success_fact'))
            <div class="toast show" style="position:static; margin-bottom:12px;">{{ session('success_fact') }}</div>
        @endif

        @if($v === 'en_attente')

            {{-- ACCEPTER --}}
            <div class="tuile" style="border:2px solid #22c55e;">
                <p class="titre" style="color:#16a34a;">✔ Accepter la facturation</p>
                <p style="font-size:13px; color:#555; margin-bottom:12px;">
                    En acceptant, vous confirmez que le temps passé ({{ number_format($tempsFacturable, 2) }}h)
                    peut être facturé en supplément du contrat.
                </p>
                <form action="{{ route('facturation.accepter', $ticket['ID']) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-add-comment"
                            style="background:#22c55e; width:100%;">
                        ✔ Accepter la facturation
                    </button>
                </form>
            </div>

            {{-- REFUSER --}}
            <div class="tuile" style="border:2px solid #ef4444; margin-top:0;">
                <p class="titre" style="color:#dc2626;">✘ Refuser la facturation</p>
                <p style="font-size:13px; color:#555; margin-bottom:12px;">
                    En refusant, le ticket repassera en statut <em>En cours</em> et sera traité à nouveau.
                </p>
                <form action="{{ route('facturation.refuser', $ticket['ID']) }}" method="POST"
                      style="display:flex; flex-direction:column; gap:10px;">
                    @csrf
                    <div>
                        <label style="font-weight:700; font-size:13px;">Motif du refus (optionnel)</label>
                        <textarea name="commentaire_refus" class="textzone"
                                  placeholder="Expliquez pourquoi vous refusez..."
                                  rows="3"
                                  style="height:auto; padding:8px; width:100%; resize:vertical; margin-top:6px;"></textarea>
                    </div>
                    <button type="submit" class="btn-danger" style="width:100%;"
                            onclick="return confirm('Confirmer le refus de facturation ?')">
                        ✘ Refuser la facturation
                    </button>
                </form>
            </div>

        @elseif($v === 'accepte')
            <div class="tuile" style="border:2px solid #22c55e;">
                <p class="titre" style="color:#16a34a;">✔ Facturation acceptée</p>
                <p style="font-size:13px; color:#555;">
                    Vous avez validé la facturation de ce ticket.
                    Le temps facturable est de <strong>{{ number_format($tempsFacturable, 2) }}h</strong>.
                </p>
            </div>

        @elseif($v === 'refuse')
            <div class="tuile" style="border:2px solid #ef4444;">
                <p class="titre" style="color:#dc2626;">✘ Facturation refusée</p>
                <p style="font-size:13px; color:#555;">
                    Vous avez refusé la facturation. Le ticket a été repassé en cours de traitement.
                </p>
                @if(!empty($ticket['commentaire_refus']))
                    <p style="font-size:13px; margin-top:8px;">
                        <strong>Motif :</strong> {{ $ticket['commentaire_refus'] }}
                    </p>
                @endif
            </div>
        @endif

        <a href="{{ route('facturation.validation.index') }}">
            <button type="button" class="btn-staff" style="margin-top:10px;">
                ← Retour à la liste
            </button>
        </a>

    </div>

</div>
@endsection
