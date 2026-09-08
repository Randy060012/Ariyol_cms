@extends('layouts.app')

@section('title', 'Page introuvable (erreur 404)')

@section('content')

    <section class="error-hero">
        <div class="wrap">
            <span class="hero-kicker">Erreur 404</span>
            <h1 class="h-display">Cette page n'existe pas</h1>
            <p class="lead" style="margin-top: 18px;">
                Le lien est peut-être erroné ou la page a été déplacée.
                Vérifiez l'adresse ou revenez à l'accueil pour poursuivre votre visite.
            </p>
            <div class="hero-actions">
                <a href="{{ route('home') }}" class="btn btn-light">Retour à l'accueil</a>
                <a href="{{ route('contact') }}" class="btn btn-ghost-light">Nous contacter</a>
            </div>
        </div>
    </section>

@endsection
