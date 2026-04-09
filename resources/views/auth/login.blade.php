@extends('layouts.guest')

@section('title', 'Connexion')

@section('content')
    <form class="card" action="{{ route('login') }}" method="POST" id="accountLogin">
        @csrf
        <h2>Login</h2>

        {{-- Message d'erreur après échec de connexion --}}
        @if(session('error'))
            <div class="error-text" style="margin-bottom:12px;">
                {{ session('error') }}
            </div>
        @endif

        <div class="login-box" id="login">
            <input type="text"
                   class="textzone"
                   name="login"
                   placeholder="Email"
                   id="login-textzone"
                   value="{{ old('login') }}">
            <div id="login_error" class="error-text titanic">
                Veuillez renseigner votre login.
            </div>
            <br><br>

            <input type="password"
                   class="textzone"
                   name="password"
                   placeholder="Mot de passe"
                   id="password">
            <div id="password_error" class="error-text titanic">
                Veuillez renseigner votre mot de passe.
            </div>
            <br><br>

            <button type="button" class="reset" id="resetPswd">
                <p>Mot de passe oublié ?</p>
            </button>
        </div>

        <div class="NewPswd-block" id="NewPswd">
            <input type="password"
                   class="textzone"
                   name="nouveauPswd"
                   placeholder="Nouveau mot de passe"
                   id="nouveauPswd">
            <div id="newPswd_error" class="error-text titanic">
                Veuillez renseigner votre nouveau mot de passe.
            </div>
            <br><br>

            <input type="password"
                   class="textzone"
                   name="Confirmerpassword"
                   placeholder="Confirmer le mot de passe"
                   id="confirmPswd">
            <div id="confirmPswd_error" class="error-text titanic">
                Veuillez confirmer votre mot de passe.
            </div>
            <br><br>
        </div>

        <button type="submit" class="enter">Se connecter</button>
    </form>
@endsection
