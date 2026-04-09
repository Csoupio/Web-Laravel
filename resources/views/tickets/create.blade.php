@extends('layouts.app')

@section('title', 'Créer un ticket')
@section('header-title', 'Nouveau ticket')

@section('content')
<div class="corp">
    <div class="tuile admin-form-container">
        <p class="titre">Créer un nouveau ticket</p>
        <p class="text-muted small mb-12">Projet : <strong>{{ $projet->nom }}</strong></p>

        <form class="form-creation" action="{{ route('tickets.store') }}" method="POST" id="ticketForm">
            @csrf
            <input type="hidden" name="project" value="{{ $projet->id }}">

            <div class="form-group">
                <label for="title">Titre du ticket</label>
                <input class="textzone w-full" type="text" id="title" name="title" placeholder="Ex : Bug sur la page de connexion" value="{{ old('title') }}">
                <div id="title_error" class="error-text titanic">Veuillez renseigner un titre.</div>
            </div>

            <div class="form-group">
                <label for="description">Description détaillée</label>
                <textarea class="textzone w-full" id="description" name="description" placeholder="Décrivez le problème..." rows="5">{{ old('description') }}</textarea>
                <div id="description_error" class="error-text titanic">Veuillez renseigner une description.</div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label for="priority">Priorité</label>
                    <select class="textzone w-full" id="priority" name="priority">
                        <option value="">-- Choisir --</option>
                        <option value="Haute"   @selected(old('priority') === 'Haute')>Haute</option>
                        <option value="Moyenne" @selected(old('priority') === 'Moyenne')>Moyenne</option>
                        <option value="Basse"   @selected(old('priority') === 'Basse')>Basse</option>
                    </select>
                </div>
                <div class="form-col">
                    <label for="type">Type</label>
                    <select class="textzone w-full" id="type" name="type">
                        <option value="">-- Choisir --</option>
                        <option value="Bug"       @selected(old('type') === 'Bug')>Bug</option>
                        <option value="Évolution" @selected(old('type') === 'Évolution')>Évolution</option>
                        <option value="Support"   @selected(old('type') === 'Support')>Support</option>
                    </select>
                </div>
            </div>

            <div class="form-group mt-10">
                <label for="estimated_time">Temps estimé (heures)</label>
                <input class="textzone w-full" type="number" id="estimated_time" name="estimated_time" placeholder="Ex : 3" min="0" step="0.5" value="{{ old('estimated_time') }}">
            </div>

            <div class="flex-gap-10 mt-10" style="justify-content:flex-end;">
                <a href="{{ route('projets.show', $projet->id) }}">
                    <button type="button" class="btn-staff" style="background:#64748b;">Annuler</button>
                </a>
                <button type="submit" class="btn-add-comment">Créer le ticket</button>
            </div>
        </form>
    </div>
</div>
@endsection
