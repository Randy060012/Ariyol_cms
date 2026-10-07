@extends('layouts.app')

@section('title', $realisation->title.' | Réalisations AFRIYOL')
@section('og_type', 'article')
@section('og_title', $realisation->title)
@section('og_description', $metaDescription ?? '')
@if ($realisation->image && !\App\Support\Media::isBrowserCapture($realisation->image))
    @section('og_image', \App\Support\Media::url($realisation->image))
@endif

@section('content')
    @php
        $gallery = array_values(array_filter($realisation->galleryImages(), fn ($image) => !\App\Support\Media::isBrowserCapture($image)));
        $hasImage = $realisation->image && !\App\Support\Media::isBrowserCapture($realisation->image);
    @endphp

    <section class="page-hero page-hero-with-image">
        <div class="wrap page-hero-layout">
            <div class="page-hero-copy">
                <nav class="breadcrumbs" aria-label="Fil d'Ariane">
                    <a href="{{ route('home') }}">Accueil</a><span class="sep">/</span>
                    <a href="{{ route('realisations') }}">Réalisations</a><span class="sep">/</span>
                    <span>{{ Str::limit($realisation->title, 40) }}</span>
                </nav>
                <p class="kicker">{{ \App\Models\Setting::get('realisation_detail_kicker', 'Action de terrain') }}</p>
                <h1 class="h-page">{{ $realisation->title }}</h1>
                <p>{{ $realisation->formattedDate() }}@if($realisation->formattedDate() && $realisation->location) · @endif{{ $realisation->location }}@if($realisation->category) · {{ $realisation->category }}@endif</p>
            </div>
            @if ($hasImage)<figure class="page-hero-visual"><img src="{{ \App\Support\Media::url($realisation->image) }}" alt="{{ $realisation->title }}" fetchpriority="high"></figure>@endif
        </div>
    </section>

    <section class="pad-section">
        <div class="wrap" style="max-width: 900px;">
            @if ($realisation->description)
                <p class="lead">{{ $realisation->description }}</p><div style="height:24px"></div>
            @endif
            @if ($realisation->impact)
                <p class="realisation-impact-callout"><span>{{ \App\Models\Setting::get('realisation_detail_result_label', 'Résultat clé') }}</span><strong>{{ $realisation->impact }}</strong></p>
            @endif
            <div class="article-prose">
                @forelse ($realisation->paragraphs() as $paragraph)
                    <p>{{ $paragraph }}</p>
                @empty
                    <p class="muted">Le récit de cette action sera bientôt disponible.</p>
                @endforelse
            </div>
            @if ($gallery)
                <div style="margin-top:42px">
                    <h2 class="h-section" style="font-size:24px;margin-bottom:22px">{{ \App\Models\Setting::get('realisation_gallery_heading', 'Photos de l’action') }}</h2>
                    <div class="grid-3" style="gap:16px">
                        @foreach ($gallery as $image)
                            <figure class="media-item"><img src="{{ \App\Support\Media::url($image) }}" alt="Photo de {{ $realisation->title }}" loading="lazy" data-lightbox style="aspect-ratio:4/3"></figure>
                        @endforeach
                    </div>
                </div>
            @endif
            <div style="margin-top:42px;padding-top:25px;border-top:1px solid var(--border);display:flex;justify-content:space-between;gap:14px;flex-wrap:wrap">
                <a href="{{ route('realisations') }}" class="btn btn-ghost">{{ \App\Models\Setting::get('realisation_back_button', 'Toutes les réalisations') }}</a>
                <a href="{{ route('contact') }}" class="btn btn-primary">{{ \App\Models\Setting::get('realisation_contact_button', 'Soutenir nos actions') }}</a>
            </div>
        </div>
    </section>
@endsection
