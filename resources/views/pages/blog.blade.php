@extends('layouts.app')

@section('title', \App\Models\Setting::get('blog_page_title', 'Blog - Actualités et articles | AFRIYOL'))

@section('og_title', \App\Models\Setting::get('blog_page_heading', "Le blog d'AFRIYOL"))
@section('og_description', \App\Models\Setting::get('blog_page_intro', "Actualités, retours d'expérience et coulisses de nos actions sur le terrain."))

@section('content')

    @php
        $blogHeroHasPhoto = !empty($blogHeroImage) && !\App\Support\Media::isBrowserCapture($blogHeroImage);
        $blogHeroSetting = \App\Models\Setting::get('blog_hero_image');
        $blogHeroPhoto = $blogHeroSetting && !\App\Support\Media::isBrowserCapture($blogHeroSetting)
            ? \App\Support\Media::url($blogHeroSetting)
            : ($blogHeroHasPhoto ? \App\Support\Media::url($blogHeroImage) : null);
    @endphp

    <section class="page-hero page-hero-with-image">
        <div class="wrap page-hero-layout">
            <div class="page-hero-copy">
                <nav class="breadcrumbs" aria-label="Fil d'Ariane">
                    <a href="{{ route('home') }}">Accueil</a>
                    <span class="sep">/</span>
                    <span>{{ \App\Models\Setting::get('blog_page_kicker', 'Blog') }}</span>
                </nav>
                <h1 class="h-page">{{ \App\Models\Setting::get('blog_page_heading', "Le blog d'AFRIYOL") }}</h1>
                <p>{{ \App\Models\Setting::get('blog_page_intro', "Actualités, retours d'expérience et coulisses de nos actions sur le terrain.") }}</p>
            </div>
            @if($blogHeroPhoto)<figure class="page-hero-visual"><img src="{{ $blogHeroPhoto }}" alt="Photo de couverture du blog AFRIYOL" fetchpriority="high"></figure>@endif
        </div>
    </section>

    <section class="pad-section">
        <div class="wrap">

            @if ($categories->isNotEmpty())
                <div class="blog-filters">
                    <a href="{{ route('blog.index') }}"
                       class="filter-chip {{ $activeCategory ? '' : 'is-active' }}">
                        {{ \App\Models\Setting::get('blog_filter_all', 'Tous les articles') }}
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
                @php
                    $featured = $posts->first();
                    $rest = $posts->skip(1);
                    $featuredHasPhoto = $featured->main_image && !\App\Support\Media::isBrowserCapture($featured->main_image);
                    $featuredPhoto = $featuredHasPhoto
                        ? \App\Support\Media::url($featured->main_image)
                        : null;
                @endphp

                <article class="rule-card" style="margin-bottom: 32px;">
                    <a href="{{ route('blog.show', ['post' => $featured->slug]) }}" class="blog-featured-link" style="grid-template-columns: {{ $featuredPhoto ? '1.1fr 1fr' : '1fr' }};">
                        @if($featuredPhoto)<img class="blog-card-image" src="{{ $featuredPhoto }}" alt="{{ $featured->title }}" loading="lazy" data-lightbox>@endif
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
                            @php
                                $postHasPhoto = $post->main_image && !\App\Support\Media::isBrowserCapture($post->main_image);
                                $postPhoto = $postHasPhoto
                                    ? \App\Support\Media::url($post->main_image)
                                    : null;
                            @endphp
                            <article class="rule-card {{ $loop->odd ? '' : 'green-top' }}">
                                <a href="{{ route('blog.show', ['post' => $post->slug]) }}" style="display: block;">
                                    @if($postPhoto)<img class="blog-card-image" src="{{ $postPhoto }}" alt="{{ $post->title }}" loading="lazy" data-lightbox>@endif
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
            border-radius: 999px;
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
        .blog-featured-link { display:grid; gap:clamp(20px,4vw,40px); align-items:center; }
        .blog-card-image { display:block; width:100%; aspect-ratio:16/10; object-fit:cover; border:1px solid var(--border); margin-bottom:18px; }
        .blog-featured-link .blog-card-image { margin:0; }
        .blog-filters .filter-chip { border-color:#e8e3d9; color:#5b6b80; }
        .blog-filters .filter-chip:hover { color:#0a3663; border-color:#0a3663; }
        .blog-filters .filter-chip.is-active { color:#fff; background:#0a3663; border-color:#0a3663; }
        .blog-pagination .active .page-link { background:#0a3663; border-color:#0a3663; }
        .blog-pagination .page-link:hover { border-color:#008a3c; }

        @media (max-width: 640px) {
            .blog-filters { gap: 8px; }
            .filter-chip { padding: 7px 14px; font-size: 12.5px; }
            .blog-featured-link { grid-template-columns:1fr !important; }
        }
    </style>
@endpush
