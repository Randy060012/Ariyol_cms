@extends('layouts.app')

@section('title', 'Accès refusé (erreur 403)')

@section('content')

    <section class="error-hero">
        <div class="wrap">
            <span class="hero-kicker">Erreur 403</span>
            <h1 class="h-display">Accès refusé</h1>
            <p class="lead" style="margin-top: 18px;">
                Vous n'avez pas les droits nécessaires pour accéder à cette page.
            </p>
            <div class="hero-actions">
                <a href="{{ route('home') }}" class="btn btn-light">Retour à l'accueil</a>
            </div>
        </div>
    </section>

@endsection
