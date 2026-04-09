@extends('layouts.app')

@section('title', 'Espace administrateur')
@section('header-title', 'Espace administrateur')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')

@if(session('success'))
    <div class="toast show">Opération effectuée avec succès !</div>
@endif

@if(session('error'))
    <div class="toast show" style="background:#dc2626;">{{ session('error') }}</div>
@endif

<div class="corp flex-gap-10">

    {{-- ===== UTILISATEURS ===== --}}
    <div class="tuile" id="users" style="flex: 1 1 400px;">
        <p class="titre">Gestion des utilisateurs</p>

        <form class="form-creation" action="{{ route('admin.users.store') }}" method="POST" id="formAdminUser">
            @csrf
            <div class="form-group">
                <label>Nom Complet</label>
                <input class="textzone w-full" type="text" placeholder="Prénom Nom" id="adminUserPrenom" name="nom" value="{{ old('nom') }}">
                <div id="adminUserPrenomError" class="error-text titanic">Veuillez renseigner le nom.</div>
            </div>

            <div class="form-group">
                <label>Email professionnel</label>
                <input class="textzone w-full" type="email" placeholder="utilisateur@exemple.com" id="adminUserEmail" name="email" value="{{ old('email') }}">
                <div id="adminUserEmailError" class="error-text titanic">Veuillez renseigner un email valide.</div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label>Mot de passe</label>
                    <input class="textzone w-full" type="password" placeholder="Mot de passe" id="adminUserPassword" name="password">
                </div>
                <div class="form-col">
                    <label>Rôle</label>
                    <select id="adminUserRole" name="role" class="textzone w-full">
                        <option value="Client">Client</option>
                        <option value="Collaborateur">Collaborateur</option>
                        <option value="Administrateur">Administrateur</option>
                    </select>
                </div>
            </div>

            <button class="btn-add-comment w-full mt-10" type="submit">Créer l'utilisateur</button>
        </form>

        <hr class="mt-10 mb-12">
        
        <div class="table-wrapper">
            <table class="Tickets-table admin-table">
                <thead class="Tickets-head">
                    <tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td>{{ $u['name'] }}</td>
                            <td>{{ $u['email'] }}</td>
                            <td><span class="badge badge--role">{{ $u['role'] }}</span></td>
                            <td>
                                <form action="{{ route('admin.users.destroy', $u['id']) }}" method="POST" data-confirm="Supprimer l'utilisateur {{ $u['name'] }} ? Ses entrées de temps seront aussi supprimées.">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger small">Suppr.</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== CLIENTS ===== --}}
    <div class="tuile" id="clients" style="flex: 1 1 400px;">
        <p class="titre">Gestion des clients</p>

        <form class="form-creation" action="{{ route('admin.clients.store') }}" method="POST" id="formAdminClient">
            @csrf
            <div class="form-group">
                <label>Raison sociale</label>
                <input class="textzone w-full" type="text" placeholder="Nom du client" name="nom_client" id="adminClientName" value="{{ old('nom_client') }}">
            </div>

            <div class="form-group">
                <label>Email de contact</label>
                <input class="textzone w-full" type="email" placeholder="contact@client.com" name="email_client" id="adminClientEmail" value="{{ old('email_client') }}">
            </div>

            <button class="btn-add-comment w-full mt-10" type="submit">Ajouter le client</button>
        </form>

        <hr class="mt-10 mb-12">

        <div class="table-wrapper">
            <table class="Tickets-table admin-table">
                <thead class="Tickets-head">
                    <tr><th>Client</th><th>Email</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                        <tr>
                            <td>{{ $client['nom'] }}</td>
                            <td>{{ $client['email'] }}</td>
                            <td>
                                <form action="{{ route('admin.clients.destroy', $client['id']) }}" method="POST" data-confirm="Supprimer le client {{ $client['nom'] }} ?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger small">Suppr.</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== PROJETS ===== --}}
    <div class="tuile" id="projects" style="flex: 1 1 400px;">
        <p class="titre">Projets & Assignations</p>

        <form class="form-creation" action="{{ route('admin.projets.store') }}" method="POST" id="formAdminProject">
            @csrf
            <div class="form-row">
                <div class="form-col">
                    <label>Nom du projet</label>
                    <input class="textzone w-full" type="text" name="nom_projet" id="adminProjectName">
                </div>
                <div class="form-col">
                    <label>Client</label>
                    <select id="ClientSelect" name="id_client" class="textzone w-full">
                        @foreach($clients as $client)
                            <option value="{{ $client['id'] }}">{{ $client['nom'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button class="btn-add-comment w-full mt-10" type="submit">Créer projet</button>
        </form>

        <hr class="mt-10 mb-12">

        @foreach($projets as $projet)
            <div class="mb-12 p-10" style="background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span class="bold">{{ $projet['nom'] }} <span class="text-muted small">— {{ $projet['clientNom'] }}</span></span>
                    <form action="{{ route('admin.projets.destroy', $projet['id']) }}" method="POST" data-confirm="Supprimer le projet {{ $projet['nom'] }} ?">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger small" style="padding:2px 6px;">x</button>
                    </form>
                </div>

                {{-- Collabs --}}
                <div class="flex-gap-10 mt-10">
                    @forelse($assignations[$projet['id']] ?? [] as $collab)
                        <span class="fact-badge fact-badge--inclus" style="display:flex; align-items:center; gap:4px;">
                            {{ $collab['name'] }}
                            <form action="{{ route('admin.projets.collaborateurs.remove', [$projet['id'], $collab['user_id']]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer;" class="bold">×</button>
                            </form>
                        </span>
                    @empty
                        <span class="text-muted small">Aucun collaborateur.</span>
                    @endforelse
                </div>

                <form action="{{ route('admin.projets.collaborateurs.assign', $projet['id']) }}" method="POST" class="mt-10 flex-gap-10">
                    @csrf
                    <select name="user_id" class="textzone" style="flex:1;">
                        @foreach($collaborateurs as $collab)
                            <option value="{{ $collab['id'] }}">{{ $collab['name'] }}</option>
                        @endforeach
                    </select>
                    <button class="btn-add-comment small" type="submit">Ajouter</button>
                </form>
            </div>
        @endforeach
    </div>

    {{-- ===== TICKETS / FORCER STATUTS ===== --}}
    <div class="tuile" id="tickets" style="flex: 1 1 100%;">
        <p class="titre">Maintenance des Tickets</p>
        <div class="table-wrapper">
            <table class="Tickets-table admin-table">
                <thead class="Tickets-head">
                    <tr><th>Projet</th><th>Ticket</th><th>Statut</th><th>Changer</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                        <tr>
                            <td class="small">{{ $ticket['projetNom'] }}</td>
                            <td class="bold">{{ $ticket['nom'] }}</td>
                            <td><span class="fact-badge fact-badge--inclus">{{ $ticket['statut'] }}</span></td>
                            <td>
                                <form action="{{ route('admin.tickets.status', $ticket['id']) }}" method="POST" style="display:flex; gap:4px;">
                                    @csrf @method('PATCH')
                                    <select name="new_status" class="textzone small" style="width:auto;">
                                        <option value="Ouvert"   @selected($ticket['statut'] === 'Ouvert')>Ouvert</option>
                                        <option value="En cours" @selected($ticket['statut'] === 'En cours')>En cours</option>
                                        <option value="Terminé"  @selected($ticket['statut'] === 'Terminé')>Terminé</option>
                                        <option value="Bloqué"   @selected($ticket['statut'] === 'Bloqué')>Bloqué</option>
                                        <option value="Refusé par client" @selected($ticket['statut'] === 'Refusé par client')>Refusé</option>
                                        <option value="Facturation acceptée" @selected($ticket['statut'] === 'Facturation acceptée')>Validé</option>
                                    </select>
                                    <button class="btn-add-comment small" type="submit">OK</button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('admin.tickets.destroy', $ticket['id']) }}" method="POST" data-confirm="Supprimer le ticket #{{ $ticket['id'] }} ?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger small">Suppr.</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
