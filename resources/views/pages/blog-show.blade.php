@extends('layouts.app')

@section('title', $post->title.' | Blog AFRIYOL')

@section('content')

    <section class="page-hero">
        <div class="wrap">
            <nav class="breadcrumbs" aria-label="Fil d'Ariane">
                <a href="{{ route('home') }}">Accueil</a>
                <span class="sep">/</span>
                <a href="{{ route('blog.index') }}">Blog</a>
                <span class="sep">/</span>
                <span>{{ Str::limit($post->title, 40) }}</span>
            </nav>
            <h1 class="h-page">{{ $post->title }}</h1>
            <p>
                Publié le {{ $post->formattedDate() }}@if($post->author) par {{ $post->author }} @endif
                &middot; {{ $post->readingTime() }} min de lecture
                @if ($post->category)
                    &middot; <strong style="color: #7fd4a4;">{{ $post->category }}</strong>
                @endif
            </p>
        </div>
    </section>

    <section class="pad-section">
        <div class="wrap" style="max-width: 860px;">

            @if ($post->excerpt)
                <p class="lead">{{ $post->excerpt }}</p>
                <div style="height: 24px;"></div>
            @endif

            @if ($post->main_image)
                <img src="{{ asset($post->main_image) }}" alt="{{ $post->title }}" loading="lazy" style="width: 100%; aspect-ratio: 21/9; object-fit: cover; border: 1px solid var(--border); margin-bottom: 36px;">
            @endif

            <div class="article-prose">
                @forelse ($post->paragraphs() as $paragraph)
                    <p>{{ $paragraph }}</p>
                @empty
                    <p class="muted">Cet article n'a pas encore de contenu.</p>
                @endforelse
            </div>

            @if ($post->galleryImages())
                <div style="margin-top: clamp(36px, 5vw, 56px);">
                    <h2 class="h-section" style="font-size: 22px; margin-bottom: 24px;">Galerie</h2>
                    <div class="grid-3" style="gap: 16px;">
                        @foreach ($post->galleryImages() as $image)
                            <figure class="media-item">
                                <img src="{{ asset($image) }}" alt="Illustration de l'article" loading="lazy" style="aspect-ratio: 4/3;">
                            </figure>
                        @endforeach
                    </div>
                </div>
            @endif

            <div style="margin-top: clamp(40px, 6vw, 64px); padding-top: 28px; border-top: 1px solid var(--border); display: flex; flex-wrap: wrap; justify-content: space-between; gap: 16px;">
                <a href="{{ route('blog.index') }}" class="btn btn-ghost">Retour au blog</a>
                <a href="{{ route('contact') }}" class="btn btn-primary">Nous contacter</a>
            </div>

        </div>
    </section>

@endsection

@push('styles')
    <style>
        .article-prose p {
            font-size: 16.5px;
            line-height: 1.8;
            color: var(--ink);
            margin-bottom: 22px;
        }
        .article-prose p:first-child {
            font-size: 18px;
            color: var(--navy);
        }
    </style>
@endpush
