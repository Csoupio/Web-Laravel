@extends('layouts.app')

@section('title', 'Espace administrateur')
@section('header-title', 'Espace administrateur')

@section('content')

{{-- Toast succès --}}
@if(session('success'))
    <div class="toast show">
        Opération effectuée avec succès !
    </div>
@endif

<div class="corp">

    {{-- ===== GESTION UTILISATEURS ===== --}}
    <div class="tuile" id="users">
        <h3>Gérer les utilisateurs</h3>

        <form class="admin-form" action="{{ route('admin.users.store') }}" method="POST" id="formAdminUser">
            @csrf

            <label>Nom</label>
            <input class="textzone" type="text" placeholder="Prénom Nom"
                   id="adminUserPrenom" name="nom" value="{{ old('nom') }}">
            <div id="adminUserPrenomError" class="error-text titanic">
                Veuillez renseigner le nom de l'utilisateur.
            </div>

            <label>Email</label>
            <input class="textzone" type="email" placeholder="utilisateur@exemple.com"
                   id="adminUserEmail" name="email" value="{{ old('email') }}">
            <div id="adminUserEmailError" class="error-text titanic">
                Veuillez renseigner un email valide.
            </div>

            <label>Mot de passe</label>
            <input class="textzone" type="password" placeholder="Mot de passe"
                   id="adminUserPassword" name="password">
            <div id="adminUserPasswordError" class="error-text titanic">
                Veuillez renseigner un mot de passe.
            </div>

            <label>Rôle</label>
            <select id="adminUserRole" name="role" class="textzone">
                <option value="Client">Client</option>
                <option value="Collaborateur">Collaborateur</option>
                <option value="Administrateur">Administrateur</option>
            </select>

            <div class="admin-actions" style="margin-top:10px;">
                <button class="btn-add-comment" type="submit">Créer</button>
                <button class="btn-danger" type="button">Supprimer</button>
            </div>
        </form>

        <hr>
        <h4>Liste utilisateurs</h4>
        <div class="table-wrapper">
            <table class="Tickets-table">
                <thead class="Tickets-head">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td>{{ $u['id'] }}</td>
                            <td>{{ $u['name'] }}</td>
                            <td>{{ $u['email'] }}</td>
                            <td>{{ $u['role'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== GESTION CLIENTS ===== --}}
    <div class="tuile" id="clients">
        <h3>Gérer les clients</h3>

        <form class="admin-form" action="{{ route('admin.clients.store') }}" method="POST" id="formAdminClient">
            @csrf

            <label>Nom du client</label>
            <input class="textzone" type="text" placeholder="Nom client"
                   name="nom_client" id="adminClientName" value="{{ old('nom_client') }}">
            <div id="adminClientNameError" class="error-text titanic">
                Veuillez renseigner le nom du client.
            </div>

            <label>Contact (email)</label>
            <input class="textzone" type="email" placeholder="contact@client.com"
                   name="email_client" id="adminClientEmail" value="{{ old('email_client') }}">
            <div id="adminClientEmailError" class="error-text titanic">
                Veuillez renseigner un email valide.
            </div>

            <input type="hidden" name="client_id" id="adminClientId">

            <div class="admin-actions" style="margin-top:10px;">
                <button class="btn-add-comment" type="submit">Ajouter</button>
                <button class="btn-c" type="button" id="cancelEditClient">Annuler</button>
            </div>
        </form>

        <hr>
        <h4>Liste clients</h4>
        <div class="table-wrapper">
            <table class="Tickets-table">
                <thead class="Tickets-head">
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Contact</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                        <tr>
                            <td>{{ $client['ID'] }}</td>
                            <td>{{ $client['Nom'] }}</td>
                            <td>{{ $client['email'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== GESTION PROJETS ===== --}}
    <div class="tuile" id="projects">
        <h3>Créer / modifier projet</h3>

        <form class="admin-form" action="{{ route('admin.projets.store') }}" method="POST" id="formAdminProject">
            @csrf

            <label>Nom du projet</label>
            <input class="textzone" type="text" placeholder="Nom du projet"
                   name="nom_projet" id="adminProjectName" value="{{ old('nom_projet') }}">
            <div id="adminProjectNameError" class="error-text titanic">
                Veuillez renseigner le nom du projet.
            </div>

            <label>Client</label>
            <select id="ClientSelect" name="id_client" class="textzone">
                @foreach($clients as $client)
                    <option value="{{ $client['ID'] }}">{{ $client['Nom'] }}</option>
                @endforeach
            </select>

            <label>Description</label>
            <textarea class="new-comment" rows="3" name="desc"
                      placeholder="Courte description"
                      id="adminProjectDescription">{{ old('desc') }}</textarea>
            <div id="adminProjectDescriptionError" class="error-text titanic">
                Veuillez renseigner une description.
            </div>

            <div class="admin-actions" style="margin-top:10px;">
                <button class="btn-add-comment" type="submit">Créer</button>
                <button class="btn-c" type="button">Annuler</button>
            </div>
        </form>

        <hr>
        <h4>Liste des projets</h4>
        <div class="table-wrapper">
            <table class="Tickets-table">
                <thead class="Tickets-head">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Client</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projets as $projet)
                        <tr>
                            <td>{{ $projet['ID'] }}</td>
                            <td>{{ $projet['Nom'] }}</td>
                            <td>{{ $projet['ClientsNom'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== TICKETS / FORCER STATUTS ===== --}}
    <div class="tuile" id="tickets">
        <h3>Tickets &amp; forcer statuts</h3>
        <div class="table-wrapper">
            <table class="Tickets-table">
                <thead class="Tickets-head">
                    <tr>
                        <th>ID</th>
                        <th>Projet</th>
                        <th>Nom</th>
                        <th>Statut actuel</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket['ID'] }}</td>
                            <td>{{ $ticket['projetNom'] }}</td>
                            <td>{{ $ticket['Nom'] }}</td>
                            <td>{{ $ticket['Status'] }}</td>
                            <td class="force-status">
                                <form action="{{ route('admin.tickets.status', $ticket['ID']) }}"
                                      method="POST"
                                      style="display:flex; gap:6px; align-items:center;">
                                    @csrf
                                    @method('PATCH')
                                    <select name="new_status" class="textzone" style="height:36px; width:auto;">
                                        <option value="Ouvert"   @selected($ticket['Status'] === 'Ouvert')>Ouvert</option>
                                        <option value="En cours" @selected($ticket['Status'] === 'En cours')>En cours</option>
                                        <option value="Terminé"  @selected($ticket['Status'] === 'Terminé')>Terminé</option>
                                        <option value="Bloqué"   @selected($ticket['Status'] === 'Bloqué')>Bloqué</option>
                                    </select>
                                    <button class="btn-add-comment" type="submit">Forcer</button>
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
