@extends('layouts.app')

@section('title', 'Mes projets')
@section('header-title', 'Mes projets')

@section('content')
<div class="corp">
    <div class="tuile" style="width:100%">
        <h2>Liste des projets</h2>

        <div class="table-wrapper">
            <table class="Tickets-table">
                <thead class="Tickets-head">
                    <tr>
                        <th>ID</th>
                        <th>Nom du projet</th>
                        <th>Client</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projets as $projet)
                        <tr class="Tickets-ligne">
                            <td>{{ $projet['ID'] }}</td>
                            <td>{{ $projet['Nom'] }}</td>
                            <td>{{ $projet['clientNom'] }}</td>
                            <td>{{ $projet['Description'] ?? '-' }}</td>
                            <td>
                                <a href="{{ route('projets.show', $projet['ID']) }}">
                                    <button class="btn-add-comment" type="button">Voir</button>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:20px;">
                                Aucun projet trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
