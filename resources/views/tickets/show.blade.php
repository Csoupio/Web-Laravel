@extends('layouts.app')

@section('title', $ticket['Nom'])
@section('header-title', 'Détails du ticket')

@section('content')

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
    $modeFacturation  = $ticket['mode_facturation'] ?? 'inclus';
    $validationClient = $ticket['validation_client'] ?? null;
@endphp

<div class="corp">

    {{-- ═══ Colonne gauche ═══ --}}
    <div class="left-panel">

        <div class="ticket-details tuile">
            <p class="titre">{{ $ticket['Nom'] }}</p>
            <p style="color:#666; font-size:13px; margin-bottom:8px;">
                Projet : <strong>{{ $ticket['projetNom'] }}</strong>
            </p>

            {{-- Badge mode facturation --}}
            <div style="margin-bottom:10px;">
                @if($modeFacturation === 'facturable')
                    <span class="fact-badge fact-badge--facturable">
                        💰 Facturable en supplément
                        @if($ticket['facturable_auto'] ?? false)
                            <em style="font-size:11px;">(contrat épuisé)</em>
                        @endif
                    </span>
                @else
                    <span class="fact-badge fact-badge--inclus">✅ Inclus dans le contrat</span>
                @endif

                @if($validationClient === 'en_attente')
                    <span class="fact-badge fact-badge--attente">⏳ En attente de validation client</span>
                @elseif($validationClient === 'accepte')
                    <span class="fact-badge fact-badge--accepte">✔ Facturation acceptée</span>
                @elseif($validationClient === 'refuse')
                    <span class="fact-badge fact-badge--refuse">✘ Facturation refusée</span>
                @endif
            </div>

            @if($validationClient === 'refuse' && !empty($ticket['commentaire_refus']))
                <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:10px; margin-bottom:10px; font-size:13px;">
                    <strong>Motif du refus :</strong> {{ $ticket['commentaire_refus'] }}
                </div>
            @endif

            <div class="desc">
                <p>{{ nl2br($ticket['Descritpion'] ?? '') }}</p>
            </div>
        </div>

        {{-- ── Bloc facturation (collaborateur / admin uniquement) ── --}}
        @if(in_array($role, ['Collaborateur', 'Administrateur']))
        @if(session('success_fact'))
            <div class="toast show" style="position:static; margin:0 10px 12px;">{{ session('success_fact') }}</div>
        @endif

        <div class="tuile">
            <p class="titre">💰 Facturation</p>

            {{-- Changement manuel du mode --}}
            <form action="{{ route('facturation.mode', $ticket['ID']) }}" method="POST"
                  style="display:flex; gap:10px; align-items:center; margin-bottom:14px;">
                @csrf
                <select name="mode_facturation" class="textzone" style="height:36px; width:auto;">
                    <option value="inclus"    @selected($modeFacturation === 'inclus')>Inclus dans le contrat</option>
                    <option value="facturable" @selected($modeFacturation === 'facturable')>Facturable en supplément</option>
                </select>
                <button type="submit" class="btn-add-comment">Appliquer</button>
            </form>

            {{-- Soumettre à validation si facturable et pas encore soumis --}}
            @if($modeFacturation === 'facturable' && $validationClient === null)
                <form action="{{ route('facturation.soumettre', $ticket['ID']) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-add-comment" style="background:#f59e0b;">
                        📤 Soumettre à la validation client
                    </button>
                </form>
            @endif
        </div>
        @endif

        {{-- Commentaires --}}
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
                <textarea class="new-comment" name="commentaire"
                          placeholder="Ajouter un commentaire..." rows="4"></textarea>
                <button type="submit" class="btn-add-comment">Ajouter</button>
            </form>
        </div>

        {{-- ── Suivi du temps ── --}}
        <div class="tuile" id="time-tracking">
            <p class="titre">⏱ Suivi du temps</p>

            @if(session('success_time'))
                <div class="toast show" style="position:static; margin-bottom:12px;">{{ session('success_time') }}</div>
            @endif

            <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:16px;">
                <div class="time-badge time-badge--total">
                    <span class="time-badge__label">Temps total</span>
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
                @if(($ticket['Temps_Estime'] ?? 0) > 0)
                    <div class="time-badge time-badge--estimate">
                        <span class="time-badge__label">Estimé</span>
                        <span class="time-badge__value">{{ $ticket['Temps_Estime'] }}h</span>
                    </div>
                @endif
            </div>

            @if(($ticket['Temps_Estime'] ?? 0) > 0)
                @php
                    $pct = min(100, round(($totalTemps / $ticket['Temps_Estime']) * 100));
                    $barColor = $pct >= 100 ? '#ef4444' : ($pct >= 80 ? '#f59e0b' : '#22c55e');
                @endphp
                <div style="margin-bottom:16px;">
                    <div style="display:flex; justify-content:space-between; font-size:12px; color:#666; margin-bottom:4px;">
                        <span>Avancement temps</span><span>{{ $pct }}%</span>
                    </div>
                    <div style="background:#e5e7eb; border-radius:4px; height:10px; overflow:hidden;">
                        <div style="width:{{ $pct }}%; background:{{ $barColor }}; height:100%; border-radius:4px;"></div>
                    </div>
                </div>
            @endif

            @if($role === 'Collaborateur')
            <details style="margin-bottom:16px;">
                <summary class="btn-add-comment" style="cursor:pointer; display:inline-block; margin-bottom:8px;">
                    + Enregistrer du temps
                </summary>
                <form action="{{ route('time.store', $ticket['ID']) }}" method="POST"
                      style="margin-top:12px; display:flex; flex-direction:column; gap:10px;">
                    @csrf
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <div style="flex:1; min-width:140px;">
                            <label style="font-weight:700; font-size:13px;">Date</label>
                            <input type="date" name="date" class="textzone"
                                   value="{{ date('Y-m-d') }}" required style="height:36px; padding:6px 10px; width:100%;">
                        </div>
                        <div style="flex:1; min-width:120px;">
                            <label style="font-weight:700; font-size:13px;">Durée (h)</label>
                            <input type="number" name="duree" class="textzone"
                                   placeholder="Ex : 1.5" min="0.25" step="0.25" required
                                   style="height:36px; padding:6px 10px; width:100%;">
                        </div>
                        <div style="flex:1; min-width:120px; display:flex; flex-direction:column; justify-content:flex-end;">
                            <label style="font-weight:700; font-size:13px;">
                                <input type="checkbox" name="facturable" value="1" checked> Facturable
                            </label>
                        </div>
                    </div>
                    <div>
                        <label style="font-weight:700; font-size:13px;">Commentaire (optionnel)</label>
                        <textarea name="commentaire" class="textzone" placeholder="Ce que vous avez fait..."
                                  rows="2" style="height:auto; padding:8px; width:100%; resize:vertical;"></textarea>
                    </div>
                    <button type="submit" class="btn-add-comment">Enregistrer</button>
                </form>
            </details>
            @endif

            @if(count($timeEntries) > 0)
                <div class="table-wrapper">
                    <table class="Tickets-table" style="font-size:13px;">
                        <thead class="Tickets-head">
                            <tr>
                                <th>Date</th><th>Collaborateur</th><th>Durée</th>
                                <th>Facturable</th><th>Commentaire</th>@if($role === 'Collaborateur' || $role === 'Administrateur')<th>Action</th>@endif
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
                                            <span style="color:green; font-weight:700;">✓ Oui</span>
                                        @else
                                            <span style="color:#999;">✗ Non</span>
                                        @endif
                                    </td>
                                    <td style="max-width:200px; white-space:normal;">{{ $entry['commentaire'] ?? '—' }}</td>
                                    @if($role === 'Collaborateur' || $role === 'Administrateur')
                                    <td>
                                        <form action="{{ route('time.destroy', $entry['ID']) }}" method="POST"
                                              onsubmit="return confirm('Supprimer ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-danger" style="padding:4px 8px; font-size:12px;">
                                                Supprimer
                                            </button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color:#aaa; font-size:13px;">Aucune entrée de temps enregistrée.</p>
            @endif
        </div>

    </div>

    {{-- ═══ Colonne droite ═══ --}}
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
            <p style="font-size:22px; font-weight:700; color:#6c6cff;">{{ number_format($totalTemps, 2) }}h</p>
            @if(($ticket['Temps_Estime'] ?? 0) > 0)
                <progress max="{{ (int)$ticket['Temps_Estime'] }}" value="{{ $totalTemps }}" style="width:100%;"></progress>
            @endif
        </div>

        <div class="collaborateur tuile">
            <p class="titre">Collaborateurs :</p>
            <div class="avatar-stack">
                <div class="avatar" title="?">?</div>
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:12px;">
            <a href="{{ route('projets.time-report', $ticket['IDProjet']) }}">
                <button type="button" class="btn-add-comment" style="width:100%;">📊 Rapport temps projet</button>
            </a>
        </div>

        <a href="{{ route('projets.show', $ticket['IDProjet']) }}">
            <button type="button" class="btn-staff">← Retour au projet</button>
        </a>
    </div>

</div>
@endsection
