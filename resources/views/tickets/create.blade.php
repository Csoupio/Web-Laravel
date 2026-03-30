@extends('layouts.app')

@section('title', 'Créer un ticket')
@section('header-title', 'Nouveau ticket')

@section('content')
<div style="padding: 15px; width: 100%; box-sizing: border-box;">
    <div class="tuile" style="max-width: 700px; margin: 0 auto;">
        <p class="titre">Créer un ticket</p>
        <p style="color:#666; font-size:13px; margin-bottom:20px;">
            Projet : <strong>{{ $projet['Nom'] }}</strong>
        </p>

        <form class="form-creation" action="{{ route('tickets.store') }}" method="POST" id="ticketForm">
            @csrf
            {{-- ID du projet passé en hidden --}}
            <input type="hidden" name="project" value="{{ $projet['ID'] }}">

            <div class="form-row">
                <label for="title">Titre du ticket</label>
                <input class="textzone"
                       type="text"
                       id="title"
                       name="title"
                       placeholder="Ex : Bug sur la page de connexion"
                       value="{{ old('title') }}">
                <div id="title_error" class="error-text titanic">
                    Veuillez renseigner un titre.
                </div>
            </div>

            <div class="form-row">
                <label for="description">Description</label>
                <textarea class="textzone"
                          id="description"
                          name="description"
                          placeholder="Décrivez le problème ou la fonctionnalité..."
                          rows="5">{{ old('description') }}</textarea>
                <div id="description_error" class="error-text titanic">
                    Veuillez renseigner une description.
                </div>
            </div>

            <div class="form-row">
                <label for="priority">Priorité</label>
                <select class="textzone" id="priority" name="priority">
                    <option value="">-- Choisir --</option>
                    <option value="Haute"   @selected(old('priority') === 'Haute')>Haute</option>
                    <option value="Moyenne" @selected(old('priority') === 'Moyenne')>Moyenne</option>
                    <option value="Basse"   @selected(old('priority') === 'Basse')>Basse</option>
                </select>
                <div id="priority_error" class="error-text titanic">
                    Veuillez choisir une priorité.
                </div>
            </div>

            <div class="form-row">
                <label for="type">Type</label>
                <select class="textzone" id="type" name="type">
                    <option value="">-- Choisir --</option>
                    <option value="Bug"       @selected(old('type') === 'Bug')>Bug</option>
                    <option value="Évolution" @selected(old('type') === 'Évolution')>Évolution</option>
                    <option value="Support"   @selected(old('type') === 'Support')>Support</option>
                </select>
                <div id="type_error" class="error-text titanic">
                    Veuillez choisir un type.
                </div>
            </div>

            <div class="form-row">
                <label for="estimated_time">Temps estimé (heures)</label>
                <input class="textzone"
                       type="number"
                       id="estimated_time"
                       name="estimated_time"
                       placeholder="Ex : 3"
                       min="0"
                       step="0.5"
                       value="{{ old('estimated_time') }}">
                <div id="estimated_time_error" class="error-text titanic">
                    Veuillez renseigner un temps estimé.
                </div>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:10px;">
                <a href="{{ route('projets.show', $projet['ID']) }}">
                    <button type="button" class="btn-c">Annuler</button>
                </a>
                <button type="submit" class="btn-staff" style="width:auto;">
                    Créer le ticket
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
