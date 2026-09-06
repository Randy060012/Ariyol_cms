@extends('layouts.app')

@section('title', $metaTitle)

@section('content')

    @php
        $heroImage = $page->hero_image;
        $isHome = $page->page_key === 'home';
        $isContact = $page->page_key === 'contact';
    @endphp

    @if ($isHome)
        <section class="hero" @if ($heroImage) style="background-image: url('{{ asset($heroImage) }}');" @endif>
            <div class="wrap">
                @if ($page->hero_kicker)
                    <span class="hero-kicker">{{ $page->hero_kicker }}</span>
                @endif
                <h1 class="h-display">{{ $page->hero_title ?: $page->title }}</h1>
                @if ($page->hero_subtitle)
                    <p class="lead">{{ $page->hero_subtitle }}</p>
                @endif
                <div class="hero-actions">
                    <a href="{{ route('about') }}" class="btn btn-light">Découvrir notre vision</a>
                    <a href="{{ route('contact') }}" class="btn btn-ghost-light">Devenir bénévole</a>
                </div>
            </div>
        </section>
    @else
        <section class="page-hero" @if ($heroImage) style="background-image: url('{{ asset($heroImage) }}');" @endif>
            <div class="wrap">
                <nav class="breadcrumbs" aria-label="Fil d'Ariane">
                    <a href="{{ route('home') }}">Accueil</a>
                <span class="sep">/</span>
                    <span>{{ $page->title }}</span>
                </nav>
                <h1 class="h-page">{{ $page->hero_title ?: $page->title }}</h1>
                @if ($page->hero_subtitle)
                    <p>{{ $page->hero_subtitle }}</p>
                @endif
            </div>
        </section>
    @endif

    @forelse ($sections as $section)
        @include('pages.sections', ['section' => $section])
    @empty
        <section class="pad-section">
            <div class="wrap">
                <p class="lead">Cette page est en cours de préparation. Revenez bientôt.</p>
            </div>
        </section>
    @endforelse

    @if ($isContact)
        <section class="pad-section paper" id="formulaire">
            <div class="wrap">
                <div class="grid-2">
                    <div>
                        <span class="kicker">Coordonnées</span>
                        <h2 class="h-section">Nous joindre directement</h2>
                        <div class="fact-list" style="margin-top: 20px;">
                            <div class="fact">
                                <strong>Siège social</strong>
                                <span>{{ \App\Models\Setting::get('contact_address', 'Tsévié, Daviémodji (Togo)') }}</span>
                            </div>
                            <div class="fact">
                                <strong>Email</strong>
                                <span><a href="mailto:{{ \App\Models\Setting::get('contact_email', 'contact@afriyol.org') }}">{{ \App\Models\Setting::get('contact_email', 'contact@afriyol.org') }}</a></span>
                            </div>
                            <div class="fact">
                                <strong>Réseaux sociaux officiels</strong>
                                <span style="display: flex; flex-wrap: wrap; gap: 18px; margin-top: 4px;">
                                    @if (\App\Models\Setting::get('social_linkedin'))
                                        <a href="{{ \App\Models\Setting::get('social_linkedin') }}" target="_blank" rel="noopener" class="text-link">LinkedIn</a>
                                    @endif
                                    @if (\App\Models\Setting::get('social_facebook'))
                                        <a href="{{ \App\Models\Setting::get('social_facebook') }}" target="_blank" rel="noopener" class="text-link">Facebook</a>
                                    @endif
                                    @if (\App\Models\Setting::get('social_twitter'))
                                        <a href="{{ \App\Models\Setting::get('social_twitter') }}" target="_blank" rel="noopener" class="text-link">X (Twitter)</a>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        @if (session('success'))
                            <div class="alert alert-success" role="status">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-error" role="alert">Le formulaire contient des erreurs. Merci de vérifier les champs indiqués.</div>
                        @endif

                        <form action="{{ route('contact.submit') }}" method="POST" novalidate>
                            @csrf
                            <h3 style="margin-bottom: 22px;">Formulaire d'engagement / contact</h3>

                            <div class="field">
                                <label for="name">Nom complet <span class="req">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            <div class="field">
                                <label for="email">Adresse email <span class="req">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            <div class="field">
                                <label for="subject">Objet de votre démarche <span class="req">*</span></label>
                                <select id="subject" name="subject" required>
                                    <option value="">Sélectionnez une option</option>
                                    <option value="benevole" @selected(old('subject') === 'benevole')>Devenir bénévole / Membre</option>
                                    <option value="partenariat" @selected(old('subject') === 'partenariat')>Proposition de partenariat</option>
                                    <option value="information" @selected(old('subject') === 'information')>Demande d'information</option>
                                </select>
                                @error('subject') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            <div class="field">
                                <label for="message">Votre message <span class="req">*</span></label>
                                <textarea id="message" name="message" required>{{ old('message') }}</textarea>
                                @error('message') <span class="error-text">{{ $message }}</span> @enderror
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Envoyer le message</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    @endif

@endsection
