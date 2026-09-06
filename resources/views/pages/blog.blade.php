@extends('layouts.app')

@section('title', 'Blog - Actualités et articles | AFRIYOL')

@section('content')

    <section class="page-hero">
        <div class="wrap">
            <nav class="breadcrumbs" aria-label="Fil d'Ariane">
                <a href="{{ route('home') }}">Accueil</a>
                <span class="sep">/</span>
                <span>Blog</span>
            </nav>
            <h1 class="h-page">Le blog d'AFRIYOL</h1>
            <p>Actualités, retours d'expérience et coulisses de nos actions sur le terrain.</p>
        </div>
    </section>

    <section class="pad-section">
        <div class="wrap">

            @if ($posts->isEmpty())
                <p class="lead">Les premiers articles arrivent bientôt. Revenez nous lire.</p>
            @else
                @php $featured = $posts->first(); $rest = $posts->skip(1); @endphp

                <article class="rule-card" style="margin-bottom: 32px;">
                    <a href="{{ route('blog.show', $featured) }}" style="display: grid; grid-template-columns: 1.1fr 1fr; gap: clamp(20px, 4vw, 40px); align-items: center;">
                        @if ($featured->main_image)
                            <img src="{{ asset($featured->main_image) }}" alt="{{ $featured->title }}" loading="lazy" style="width: 100%; aspect-ratio: 16/10; object-fit: cover; border: 1px solid var(--border);">
                        @endif
                        <div>
                            <span class="kicker">{{ $featured->category ?: 'À la une' }}</span>
                            <h2 class="h-section">{{ $featured->title }}</h2>
                            @if ($featured->excerpt)
                                <p class="muted" style="margin-top: 10px;">{{ $featured->excerpt }}</p>
                            @endif
                            <p class="small muted" style="margin-top: 14px;">
                                {{ $featured->formattedDate() }} &middot; {{ $featured->readingTime() }} min de lecture
                            </p>
                        </div>
                    </a>
                </article>

                @if ($rest->isNotEmpty())
                    <div class="grid-3">
                        @foreach ($rest as $post)
                            <article class="rule-card {{ $loop->odd ? '' : 'green-top' }}">
                                <a href="{{ route('blog.show', $post) }}" style="display: block;">
                                    @if ($post->main_image)
                                        <img src="{{ asset($post->main_image) }}" alt="{{ $post->title }}" loading="lazy" style="width: 100%; aspect-ratio: 16/10; object-fit: cover; border: 1px solid var(--border); margin-bottom: 18px;">
                                    @endif
                                    <span class="kicker">{{ $post->category ?: 'Actualité' }}</span>
                                    <h3>{{ $post->title }}</h3>
                                    @if ($post->excerpt)
                                        <p>{{ $post->excerpt }}</p>
                                    @endif
                                    <p class="small muted" style="margin-top: 14px;">
                                        {{ $post->formattedDate() }} &middot; {{ $post->readingTime() }} min de lecture
                                    </p>
                                </a>
                            </article>
                        @endforeach
                    </div>
                @endif
            @endif

        </div>
    </section>

@endsection
