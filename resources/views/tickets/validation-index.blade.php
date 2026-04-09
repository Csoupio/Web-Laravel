@extends('layouts.app')

@section('title', 'Validation facturation')
@section('header-title', 'Validation des tickets facturables')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/validation.css') }}">
@endpush

@section('content')
<div class="corp">

    <div class="tuile mb-12">
        <p class="titre">Tickets soumis à validation</p>
        <p class="text-muted small">
            @if(($role ?? '') === 'Client')
                Consultez chaque ticket, vérifiez le temps passé, puis acceptez ou refusez la facturation.
            @else
                Vue d'ensemble des tickets en attente de validation client.
            @endif
        </p>
    </div>

    @if(session('success_fact'))
        <div class="toast show mb-12" style="position:static;">{{ session('success_fact') }}</div>
    @endif

    <div class="tuile">
        @if(count($tickets) > 0)
            <div class="table-wrapper">
                <table class="Tickets-table validation-table">
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
                            @php $v = $ticket['validation_client']; @endphp
                            <tr class="Tickets-ligne">
                                <td>{{ $ticket['nom'] }}</td>
                                <td>{{ $ticket['projetNom'] }}</td>
                                <td>{{ $ticket['statut'] }}</td>
                                <td>
                                    @if($v === 'en_attente')
                                        <span class="fact-badge fact-badge--attente">En attente</span>
                                    @elseif($v === 'accepte')
                                        <span class="fact-badge fact-badge--accepte">Accepté</span>
                                    @elseif($v === 'refuse')
                                        <span class="fact-badge fact-badge--refuse">Refusé</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('facturation.validation.show', $ticket['id']) }}">
                                        <button class="btn-add-comment" type="button">
                                            {{ (($role ?? '') === 'Client' && $v === 'en_attente') ? 'Valider' : 'Consulter' }}
                                        </button>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted" style="text-align:center; padding:20px;">
                Aucun ticket en attente de validation.
            </p>
        @endif
    </div>

</div>
@endsection
