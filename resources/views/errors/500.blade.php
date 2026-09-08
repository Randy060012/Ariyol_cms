@extends('layouts.app')

@section('title', 'Erreur serveur (erreur 500)')

@section('content')

    <section class="error-hero">
        <div class="wrap">
            <span class="hero-kicker">Erreur 500</span>
            <h1 class="h-display">Une erreur est survenue</h1>
            <p class="lead" style="margin-top: 18px;">
                Nos équipes ont été informées et le problème sera réglé rapidement.
                Merci de réessayer dans quelques instants.
            </p>
            <div class="hero-actions">
                <a href="{{ route('home') }}" class="btn btn-light">Retour à l'accueil</a>
                <a href="{{ route('contact') }}" class="btn btn-ghost-light">Nous contacter</a>
            </div>
        </div>
    </section>

@endsection
