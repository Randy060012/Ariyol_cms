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

            @if ($categories->isNotEmpty())
                <div class="blog-filters">
                    <a href="{{ route('blog.index') }}"
                       class="filter-chip {{ $activeCategory ? '' : 'is-active' }}">
                        Tous les articles
                    </a>
                    @foreach ($categories as $category)
                        <a href="{{ route('blog.index', ['categorie' => $category]) }}"
                           class="filter-chip {{ $activeCategory === $category ? 'is-active' : '' }}">
                            {{ $category }}
                        </a>
                    @endforeach
                </div>
            @endif

            @if ($posts->isEmpty())
                <p class="lead">
                    @if ($activeCategory)
                        Aucun article dans la catégorie « {{ $activeCategory }} » pour le moment.
                    @else
                        Les premiers articles arrivent bientôt. Revenez nous lire.
                    @endif
                </p>
            @else
                @php $featured = $posts->first(); $rest = $posts->skip(1); @endphp

                <article class="rule-card" style="margin-bottom: 32px;">
                    <a href="{{ route('blog.show', $featured) }}" style="display: grid; grid-template-columns: 1.1fr 1fr; gap: clamp(20px, 4vw, 40px); align-items: center;">
                        @if ($featured->main_image)
                            <img src="{{ asset($featured->main_image) }}" alt="{{ $featured->title }}" loading="lazy" data-lightbox style="width: 100%; aspect-ratio: 16/10; object-fit: cover; border: 1px solid var(--border);">
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
                                        <img src="{{ asset($post->main_image) }}" alt="{{ $post->title }}" loading="lazy" data-lightbox style="width: 100%; aspect-ratio: 16/10; object-fit: cover; border: 1px solid var(--border); margin-bottom: 18px;">
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

                @if ($posts->hasPages())
                    <div class="blog-pagination">
                        {{ $posts->links() }}
                    </div>
                @endif
            @endif

        </div>
    </section>

@endsection

@push('styles')
    <style>
        /* Filtres par catégorie : puces alignées sur la charte */
        .blog-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: clamp(28px, 4vw, 44px);
        }
        .filter-chip {
            display: inline-flex;
            align-items: center;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .04em;
            color: var(--muted);
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 8px 18px;
            transition: color .2s ease, border-color .2s ease, background-color .2s ease;
        }
        .filter-chip:hover { color: var(--navy); border-color: var(--navy); }
        .filter-chip.is-active {
            color: var(--white);
            background: var(--navy);
            border-color: var(--navy);
        }

        /* Pagination : liens sobres, accent vert sur la page courante */
        .blog-pagination { margin-top: clamp(36px, 5vw, 56px); }
        .blog-pagination nav { display: flex; justify-content: center; }
        .blog-pagination .pagination { display: flex; gap: 6px; list-style: none; }
        .blog-pagination .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            padding: 9px 14px;
            font-size: 14px;
            font-weight: 600;
            color: var(--navy);
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 2px;
            transition: background-color .2s ease, color .2s ease, border-color .2s ease;
        }
        .blog-pagination .page-link:hover { border-color: var(--navy); }
        .blog-pagination .active .page-link {
            color: var(--white);
            background: var(--green);
            border-color: var(--green);
        }
        .blog-pagination .disabled .page-link {
            color: var(--muted);
            opacity: .55;
            cursor: not-allowed;
        }
        .blog-pagination .svg-inline { display: none; }

        @media (max-width: 640px) {
            .blog-filters { gap: 8px; }
            .filter-chip { padding: 7px 14px; font-size: 12.5px; }
        }
    </style>
@endpush
