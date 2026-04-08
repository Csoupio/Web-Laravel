@extends('layouts.app')

@section('title', 'Validation facturation')
@section('header-title', 'Validation des tickets facturables')

@section('content')
<div style="padding:15px; width:100%; box-sizing:border-box;">

    <div class="tuile" style="margin-bottom:16px;">
        <p class="titre">📋 Tickets soumis à votre validation</p>
        <p style="color:#666; font-size:13px;">
            Consultez chaque ticket, vérifiez le temps passé, puis acceptez ou refusez la facturation.
        </p>
    </div>

    @if(session('success_fact'))
        <div class="toast show" style="position:static; margin-bottom:12px;">{{ session('success_fact') }}</div>
    @endif

    <div class="tuile">
        @if(count($tickets) > 0)
            <div class="table-wrapper">
                <table class="Tickets-table" style="font-size:13px;">
                    <thead class="Tickets-head">
                        <tr>
                            <th>Ticket</th>
                            <th>Projet</th>
                            <th>Statut</th>
                            <th>Validation</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            @php
                                $v = $ticket['validation_client'];
                            @endphp
                            <tr class="Tickets-ligne">
                                <td>{{ $ticket['Nom'] }}</td>
                                <td>{{ $ticket['projetNom'] }}</td>
                                <td>{{ $ticket['Status'] }}</td>
                                <td>
                                    @if($v === 'en_attente')
                                        <span class="fact-badge fact-badge--attente">⏳ En attente</span>
                                    @elseif($v === 'accepte')
                                        <span class="fact-badge fact-badge--accepte">✔ Accepté</span>
                                    @elseif($v === 'refuse')
                                        <span class="fact-badge fact-badge--refuse">✘ Refusé</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('facturation.validation.show', $ticket['ID']) }}">
                                        <button class="btn-add-comment" type="button">Consulter</button>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="color:#aaa; font-size:14px; text-align:center; padding:20px;">
                Aucun ticket en attente de validation.
            </p>
        @endif
    </div>

</div>
@endsection
