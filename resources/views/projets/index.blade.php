@extends('layouts.app')

@section('title', 'Mes projets')
@section('header-title', 'Mes projets')

@section('content')
<div class="corp">
    <div class="tuile">
        <p class="titre">Liste des projets</p>

        <div class="table-wrapper">
            <table class="Tickets-table validation-table">
                <thead class="Tickets-head">
                    <tr>
                        <th>ID</th>
                        <th>Nom du projet</th>
                        <th>Client</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projets as $projet)
                        <tr class="Tickets-ligne">
                            <td>#{{ $projet['id'] }}</td>
                            <td class="bold">{{ $projet['nom'] }}</td>
                            <td>{{ $projet['clientNom'] }}</td>
                            <td>
                                <a href="{{ route('projets.show', $projet['id']) }}">
                                    <button class="btn-add-comment small" type="button">Voir</button>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted" style="text-align:center; padding:20px;">Aucun projet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
