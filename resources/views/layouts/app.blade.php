<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        /*
         | SEO & partage social : meta description, canonical, OpenGraph,
         | Twitter Cards et JSON-LD (schema.org/NGO).
         |
         | Chaque page peut surcharger ces sections : og_title, og_description,
         | og_image, og_type, og_published_time. Sans surcharge, les valeurs
         | par défaut du site sont utilisées.
         |
         | NB : les sections sont lues via $__env->yieldContent() car les
         | directives Blade ne sont pas compilées à l'intérieur d'un @php.
         */
        $defaultDescription = "AFRIYOL - African Young Leaders : organisation non gouvernementale dédiée aux Droits Humains, à la Paix, à l'Environnement et au Leadership des Jeunes au Togo.";

        $ogSiteName = $siteHeader['brand_name'] ?? 'AFRIYOL';

        /*
         | Les sections inline sont échappées (e()) au moment de leur capture :
         | on les affiche donc telles quelles avec {!! !!} dans les balises meta,
         | et on n'échappe (e()) que les valeurs par défaut calculées ici.
         */
        $seoDescription = trim((string) $__env->yieldContent('og_description'))
            ?: e($defaultDescription);

        $ogTitle = trim((string) $__env->yieldContent('og_title'))
            ?: trim((string) $__env->yieldContent('title', e($ogSiteName.' - African Young Leaders')));

        // Image de partage par défaut : raster (les plateformes ignorent le SVG).
        $ogImage = trim((string) $__env->yieldContent('og_image'))
            ?: e(asset('images/afriyol.png'));

        $ogType = trim((string) $__env->yieldContent('og_type')) ?: 'website';
        $ogPublishedTime = trim((string) $__env->yieldContent('og_published_time'));
        $seoUrl = url()->current();

        // @twitter:site déduit du lien X (Twitter) des réglages du site.
        $twitterHandle = '';
        $twitterPath = parse_url((string) ($siteFooter['twitter'] ?? ''), PHP_URL_PATH);
        if (is_string($twitterPath) && trim($twitterPath, '/') !== '') {
            $twitterHandle = '@'.trim($twitterPath, '/');
        }

        // Identité de l'organisation (schema.org/NGO), alimentée par les réglages.
        $logoPath = (string) ($siteHeader['logo'] ?? 'images/logo.svg');
        $orgSchema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'NGO',
            'name' => $ogSiteName,
            'alternateName' => $siteHeader['brand_tagline'] ?? null,
            'url' => url('/'),
            'logo' => str_ends_with($logoPath, '.svg') ? asset('images/afriyol.png') : asset($logoPath),
            'description' => $siteFooter['description'] ?? null,
            'email' => $siteFooter['email'] ?? null,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $siteFooter['address'] ?? null,
                'addressCountry' => 'TG',
            ],
            'sameAs' => array_values(array_filter([
                $siteFooter['linkedin'] ?? null,
                $siteFooter['facebook'] ?? null,
                $siteFooter['twitter'] ?? null,
            ])),
        ], fn ($value) => $value !== null && $value !== []);

        // JSON inséré dans un <script> : échapper < > & ' " pour éviter toute injection.
        $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP;
    @endphp

    <meta name="description" content="{!! $seoDescription !!}">
    <link rel="canonical" href="{{ $seoUrl }}">
    <title>@yield('title', 'AFRIYOL - African Young Leaders')</title>

    {{-- OpenGraph --}}
    <meta property="og:site_name" content="{{ $ogSiteName }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{!! $ogTitle !!}">
    <meta property="og:description" content="{!! $seoDescription !!}">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:locale" content="fr_FR">
    @if ($ogType === 'article' && $ogPublishedTime !== '')
        <meta property="article:published_time" content="{!! $ogPublishedTime !!}">
    @endif

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    @if ($twitterHandle !== '')
        <meta name="twitter:site" content="{{ $twitterHandle }}">
    @endif
    <meta name="twitter:title" content="{!! $ogTitle !!}">
    <meta name="twitter:description" content="{!! $seoDescription !!}">
    <meta name="twitter:image" content="{!! $ogImage !!}">

    {{-- JSON-LD : identité de l'organisation (NGO) --}}
    <script type="application/ld+json">@json($orgSchema, $jsonFlags)</script>

    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ============ RESET & BASE ============ */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            /* Charte extraite du logo */
            --navy: #0a3663;
            --navy-soft: #0c4a80;
            --green: #008a3c;
            --black: #111111;

            /* Structure */
            --ink: #16233a;
            --muted: #5b6b80;
            --border: #dfe5ec;
            --paper: #f5f7f9;
            --white: #ffffff;

            --font: 'Poppins', system-ui, -apple-system, sans-serif;
            --wrap: 1160px;
            --pad: 24px;
            --gap: clamp(48px, 8vw, 112px);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font);
            font-size: 16px;
            line-height: 1.65;
            color: var(--ink);
            background: var(--white);
            -webkit-font-smoothing: antialiased;
        }

        a { color: inherit; text-decoration: none; }
        ul { list-style: none; }
        img, svg { max-width: 100%; display: block; }

        .wrap { max-width: var(--wrap); margin: 0 auto; padding: 0 var(--pad); }
        .pad-section { padding-block: var(--gap); }
        .paper { background: var(--paper); border-block: 1px solid var(--border); }
        .on-dark { background: var(--navy); color: var(--white); }

        /* ============ TYPOGRAPHIE ============ */
        h1, h2, h3, h4 { line-height: 1.22; font-weight: 600; color: inherit; }

        .kicker {
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--green);
            margin-bottom: 14px;
        }
        .on-dark .kicker { color: #7fd4a4; }

        .h-display { font-size: clamp(30px, 4.4vw, 50px); font-weight: 600; letter-spacing: -0.02em; }
        .h-page    { font-size: clamp(26px, 3.2vw, 38px); font-weight: 600; letter-spacing: -0.015em; }
        .h-section { font-size: clamp(22px, 2.6vw, 30px); font-weight: 600; letter-spacing: -0.01em; }

        .section-head { max-width: 640px; margin-bottom: clamp(32px, 5vw, 56px); }
        .section-head.center { margin-inline: auto; text-align: center; }
        .section-head p { color: var(--muted); font-size: 15px; margin-top: 12px; }
        .on-dark .section-head p { color: #b9c8dc; }

        .lead { font-size: 17px; color: var(--muted); max-width: 62ch; }
        .on-dark .lead { color: #b9c8dc; }
        .muted { color: var(--muted); }
        .small { font-size: 14px; }

        /* ============ BOUTONS ============ */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 13px 26px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 2px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background-color .2s ease, color .2s ease, border-color .2s ease;
        }
        .btn-primary { background: var(--navy); color: var(--white); }
        .btn-primary:hover { background: var(--navy-soft); }
        .btn-green { background: var(--green); color: var(--white); }
        .btn-green:hover { background: #00732f; }
        .btn-ghost { border-color: var(--navy); color: var(--navy); background: transparent; }
        .btn-ghost:hover { background: var(--navy); color: var(--white); }
        .btn-ghost-light { border-color: rgba(255,255,255,.55); color: var(--white); background: transparent; }
        .btn-ghost-light:hover { background: var(--white); color: var(--navy); }
        .btn-light { background: var(--white); color: var(--navy); }
        .btn-light:hover { background: #e8eef5; }
        .btn-block { width: 100%; justify-content: center; }

        .text-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: var(--navy);
            border-bottom: 1px solid var(--navy);
            padding-bottom: 2px;
            transition: color .2s ease, border-color .2s ease;
        }
        .text-link:hover { color: var(--green); border-color: var(--green); }

        /* ============ NAVIGATION ============ */
        .site-header {
            position: sticky;
            top: 0;
            z-index: 60;
            background: rgba(255, 255, 255, .96);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--border);
        }

        .nav-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            height: 74px;
        }

        .brand { display: flex; align-items: center; gap: 14px; }
        .brand img { width: 48px; height: 48px; object-fit: contain; }
        .brand-name { line-height: 1.15; }
        .brand-name strong { display: block; font-size: 16px; font-weight: 700; letter-spacing: .08em; color: var(--navy); }
        .brand-name span { display: block; font-size: 10px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); }

        .nav-links { display: flex; align-items: center; gap: 28px; }

        .nav-links a {
            font-size: 13px;
            font-weight: 500;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--muted);
            padding-block: 6px;
            border-bottom: 2px solid transparent;
            transition: color .2s ease, border-color .2s ease;
        }
        .nav-links a:hover { color: var(--navy); }
        .nav-links a.is-active { color: var(--navy); border-bottom-color: var(--green); }

        .nav-cta { display: inline-flex; }

        .nav-toggle {
            display: none;
            background: none;
            border: 1px solid var(--border);
            border-radius: 2px;
            padding: 9px 11px;
            cursor: pointer;
        }
        .nav-toggle span {
            display: block;
            width: 20px;
            height: 2px;
            background: var(--navy);
            margin-block: 4px;
        }

        /* ============ HERO & EN-TETES DE PAGE ============ */
        .hero {
            background: var(--navy);
            position: relative;
            color: var(--white);
            padding: clamp(96px, 14vw, 160px) 0 clamp(64px, 9vw, 110px);
        }
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(100deg, rgba(7, 30, 54, .93) 30%, rgba(7, 30, 54, .72));
        }
        .hero .wrap { position: relative; }
        .hero-kicker {
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: #7fd4a4;
            border: 1px solid rgba(127, 212, 164, .45);
            border-radius: 999px;
            padding: 7px 16px;
            margin-bottom: 24px;
        }
        .hero p.lead { margin-top: 18px; }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 34px; }

        .page-hero {
            background: var(--navy) center / cover no-repeat;
            color: var(--white);
            padding: clamp(72px, 10vw, 112px) 0;
            border-bottom: 4px solid var(--green);
        }
        .page-hero p { color: #b9c8dc; margin-top: 12px; max-width: 60ch; }

        /* Pages d'erreur : même traitement visuel que le hero, sans image de fond. */
        .error-hero {
            background: var(--navy);
            color: var(--white);
            padding: clamp(96px, 14vw, 160px) 0 clamp(64px, 9vw, 110px);
        }
        .error-hero p.lead { color: #b9c8dc; }

        .breadcrumbs { display: flex; flex-wrap: wrap; gap: 8px; font-size: 12.5px; letter-spacing: .04em; color: #8fa5bd; margin-bottom: 18px; }
        .breadcrumbs a:hover { color: var(--white); }
        .breadcrumbs .sep { opacity: .5; }

        /* ============ CARTES & COMPOSANTS ============ */
        .rule-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-top: 3px solid var(--navy);
            border-radius: 2px;
            padding: 30px 28px;
        }
        .rule-card.green-top { border-top-color: var(--green); }
        .rule-card h3 { font-size: 18px; margin-bottom: 10px; }
        .rule-card p { font-size: 14px; color: var(--muted); }

        /* Photo d'une carte : elle occupe la largeur de la carte et remplace
           son padding haut, pour que l'image affleure les bords. */
        .card-media {
            margin: -30px -28px 22px;
            border-bottom: 1px solid var(--border);
            background: var(--paper);
        }
        .card-media img { width: 100%; aspect-ratio: 16 / 10; object-fit: cover; }

        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: clamp(32px, 5vw, 64px); align-items: start; }

        .num-marker {
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1em;
            color: var(--green);
            margin-bottom: 12px;
        }

        .fact-list { display: grid; gap: 16px; }
        .fact {
            background: var(--white);
            border: 1px solid var(--border);
            border-left: 3px solid var(--green);
            padding: 20px 24px;
            border-radius: 2px;
        }
        .fact strong { display: block; font-size: 24px; font-weight: 600; color: var(--navy); line-height: 1.2; }
        .fact span { font-size: 13.5px; color: var(--muted); }

        .check-list { display: grid; gap: 10px; margin-top: 14px; }
        .check-list li {
            position: relative;
            padding-left: 24px;
            font-size: 14px;
            color: var(--muted);
        }
        .check-list li::before {
            content: '';
            position: absolute;
            left: 0;
            top: 9px;
            width: 9px;
            height: 9px;
            border: 2px solid var(--green);
            border-radius: 50%;
        }

        .media-strip {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-top: clamp(32px, 5vw, 56px);
        }
        .media-item { border: 1px solid var(--border); border-radius: 2px; overflow: hidden; background: var(--white); }
        .media-item img { width: 100%; aspect-ratio: 16 / 10; object-fit: cover; }
        .media-item figcaption { padding: 16px 18px; font-size: 13.5px; color: var(--muted); border-top: 1px solid var(--border); }

        /* ============ PARTENAIRES (LOGOS DÉFILANTS) ============ */
        .logo-marquee {
            overflow: hidden;
            position: relative;
            border-block: 1px solid var(--border);
            padding-block: 28px;
            -webkit-mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
            mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
        }
        .logo-track {
            display: flex;
            align-items: center;
            width: max-content;
            animation: logo-scroll 32s linear infinite;
        }
        .logo-group {
            display: flex;
            align-items: center;
            gap: clamp(40px, 6vw, 88px);
            padding-right: clamp(40px, 6vw, 88px);
        }
        .logo-marquee:hover .logo-track { animation-play-state: paused; }
        .logo-item {
            flex: none;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 2px;
            padding: 16px 30px;
        }
        .logo-item img { height: 56px; width: auto; max-width: 170px; object-fit: contain; }
        @keyframes logo-scroll {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }
        @media (prefers-reduced-motion: reduce) {
            .logo-track { animation: none; flex-wrap: wrap; justify-content: center; width: auto; }
        }

        /* ============ REALISATIONS (PHOTO A GAUCHE, TEXTE A DROITE) ============ */
        .realisation-list { display: grid; gap: clamp(28px, 4vw, 44px); }
        .realisation-row {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(0, 3fr);
            align-items: stretch;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 2px;
            overflow: hidden;
        }
        .realisation-media { position: relative; min-height: 260px; background: var(--navy); }
        .realisation-media > img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .realisation-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--navy);
        }
        .realisation-placeholder img { width: 88px; height: auto; opacity: .9; }
        .realisation-body { padding: clamp(22px, 3vw, 38px) clamp(22px, 3.5vw, 44px); align-self: center; }
        .realisation-body h3 { font-size: 21px; margin-bottom: 10px; color: var(--navy); }
        .realisation-body p { font-size: 15px; color: var(--muted); }

        @media (max-width: 860px) {
            .realisation-row { grid-template-columns: 1fr; }
            /* Mobile : la photo passe au-dessus du texte. */
            .realisation-media { min-height: 0; aspect-ratio: 16 / 9; }
        }

        /* ============ GALERIES D'IMAGES DE SECTION ============ */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-top: clamp(32px, 5vw, 56px);
        }
        .gallery-item {
            border: 1px solid var(--border);
            border-radius: 2px;
            overflow: hidden;
            background: var(--white);
        }
        .gallery-item img { width: 100%; aspect-ratio: 4 / 3; object-fit: cover; display: block; }

        /* ============ GALERIE DE L'EN-TÊTE DE PAGE ============ */
        .hero-gallery { background: var(--white); border-bottom: 1px solid var(--border); }
        .hero-gallery-track {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: var(--border);
            border-inline: 1px solid var(--border);
        }
        .hero-gallery-item { margin: 0; background: var(--white); }
        .hero-gallery-item img { width: 100%; aspect-ratio: 16 / 10; object-fit: cover; }

        /* ============ LIGHTBOX ============ */
        .lightbox {
            border: none;
            padding: 0;
            background: rgba(7, 30, 54, .92);
            max-width: none;
            max-height: none;
            width: 100%;
            height: 100%;
            outline: none;
        }
        .lightbox::backdrop { background: rgba(7, 30, 54, .92); }
        .lightbox img {
            display: block;
            max-width: min(92vw, 1200px);
            max-height: 84vh;
            margin: auto;
            object-fit: contain;
            border: 1px solid rgba(255, 255, 255, .2);
        }
        .lightbox-close {
            position: fixed;
            top: 18px;
            right: 22px;
            background: none;
            border: 1px solid rgba(255, 255, 255, .4);
            border-radius: 2px;
            color: var(--white);
            font-size: 26px;
            line-height: 1;
            padding: 8px 14px;
            cursor: pointer;
        }
        .lightbox-close:hover { background: rgba(255, 255, 255, .12); }

        /* Les images zoomables montrent qu'elles sont cliquables. */
        img[data-lightbox] { cursor: zoom-in; }

        /* ============ FORMULAIRES ============ */
        .field { margin-bottom: 18px; }
        .field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 7px;
        }
        .field label .req { color: var(--green); }

        .field input,
        .field select,
        .field textarea {
            width: 100%;
            font-family: inherit;
            font-size: 14px;
            color: var(--ink);
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 2px;
            padding: 12px 14px;
            transition: border-color .2s ease;
        }
        .field input:focus,
        .field select:focus,
        .field textarea:focus { outline: none; border-color: var(--navy); }
        .field textarea { min-height: 150px; resize: vertical; }

        .error-text { display: block; margin-top: 6px; font-size: 12.5px; color: #b42318; }

        .alert {
            border: 1px solid transparent;
            border-left-width: 3px;
            border-radius: 2px;
            padding: 14px 18px;
            font-size: 14px;
            margin-bottom: 24px;
        }
        .alert-success { background: #eef7f1; border-color: #bfe3cd; border-left-color: var(--green); color: #14572f; }
        .alert-error   { background: #fdf1f0; border-color: #f2c6c2; border-left-color: #b42318; color: #8a1c13; }

        /* ============ PIED DE PAGE ============ */
        .site-footer { background: var(--navy); color: #b9c8dc; font-size: 14px; }
        .footer-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr 1.3fr;
            gap: clamp(32px, 4vw, 56px);
            padding-block: clamp(48px, 7vw, 72px);
        }
        .site-footer h4 {
            color: var(--white);
            font-size: 12.5px;
            font-weight: 600;
            letter-spacing: .14em;
            text-transform: uppercase;
            margin-bottom: 18px;
        }
        .footer-links { display: grid; gap: 10px; }
        .footer-links a:hover { color: var(--white); }
        .footer-brand img { width: 52px; height: 52px; margin-bottom: 16px; }
        .footer-brand strong { display: block; font-size: 17px; letter-spacing: .1em; color: var(--white); margin-bottom: 10px; }
        .footer-brand p { max-width: 34ch; }
        .footer-meta {
            display: grid;
            gap: 8px;
        }
        .footer-meta span { display: block; }
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, .14);
            padding-block: 22px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 10px;
            font-size: 12.5px;
            color: #8fa5bd;
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 1024px) {
            .grid-3 { grid-template-columns: repeat(2, 1fr); }
            .media-strip, .gallery-grid, .hero-gallery-track { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 860px) {
            .grid-2 { grid-template-columns: 1fr; }

            .nav-cta { display: none; }

            .nav-toggle { display: block; }

            .nav-links {
                display: none;
                position: absolute;
                top: 74px;
                left: 0;
                right: 0;
                flex-direction: column;
                align-items: stretch;
                gap: 0;
                background: var(--white);
                border-bottom: 1px solid var(--border);
                box-shadow: 0 16px 30px rgba(10, 54, 99, .08);
            }
            .nav-links.is-open { display: flex; }
            .nav-links li { border-top: 1px solid var(--border); }
            .nav-links a {
                display: block;
                padding: 14px var(--pad);
                border-bottom: none;
            }
            .nav-links a.is-active { box-shadow: inset 3px 0 0 var(--green); }
        }

        @media (max-width: 640px) {
            .grid-3, .media-strip, .gallery-grid, .hero-gallery-track { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
            .hero-actions .btn { width: 100%; justify-content: center; }
            .logo-item img { height: 44px; }
        }
    </style>
    @stack('styles')
    <style>
        :root {
            --navy:#0a3663; --navy-soft:#0c4a80; --green:#008a3c;
            --ink:#17283b; --muted:#617184; --border:#e4e9e8; --paper:#f5f8f5;
            --white:#fff; --wrap:1200px; --pad:clamp(18px,4vw,32px); --gap:clamp(54px,8vw,100px);
        }
        html { scroll-padding-top:90px; }
        body { background:#fff; color:var(--ink); }
        h1,h2,h3,h4 { letter-spacing:-.025em; }
        .site-header { position:sticky; top:0; z-index:50; background:rgba(255,255,255,.94); border-bottom:1px solid rgba(10,54,99,.08); backdrop-filter:blur(16px); }
        .nav-bar { min-height:78px; }
        .brand { gap:11px; }
        .brand img { width:46px; height:46px; }
        .brand-name strong { color:var(--navy); font-size:15px; letter-spacing:.055em; }
        .brand-name span { color:var(--muted); font-size:9px; letter-spacing:.11em; }
        .nav-links { gap:clamp(12px,1.7vw,24px); }
        .nav-links a { font-size:12px; font-weight:500; text-transform:none; }
        .nav-links a.is-active { color:var(--green); }
        .btn { min-height:44px; border-radius:999px; padding:11px 20px; font-size:13px; font-weight:600; transition:transform .2s ease,background .2s ease,box-shadow .2s ease; }
        .btn:hover { transform:translateY(-2px); }
        .btn-primary { background:var(--green); box-shadow:0 8px 20px rgba(0,138,60,.17); }
        .btn-primary:hover { background:#006f31; }
        .btn-ghost { border-radius:999px; }
        .kicker { font-size:11px; letter-spacing:.14em; }
        .h-display { max-width:720px; font-size:clamp(38px,5.7vw,72px); line-height:1.02; letter-spacing:-.045em; }
        .h-page { font-size:clamp(34px,4.5vw,56px); line-height:1.06; }
        .h-section { font-size:clamp(26px,3.2vw,39px); line-height:1.12; }
        .lead { font-size:clamp(16px,1.5vw,19px); line-height:1.75; }
        .ngo-hero { position:relative; isolation:isolate; overflow:hidden; padding:clamp(48px,7vw,88px) 0; background:radial-gradient(ellipse at 79% 8%,rgba(0,138,60,.09),transparent 34%),linear-gradient(115deg,#f6f9f5 0%,#fff 58%,#f4f8f5 100%); }
        .ngo-hero:before { position:absolute; z-index:-1; inset:auto -100px -190px auto; width:440px; height:440px; border:1px solid rgba(0,138,60,.13); border-radius:50%; box-shadow:0 0 0 46px rgba(0,138,60,.025),0 0 0 92px rgba(0,138,60,.02); content:''; }
        .ngo-hero-grid { display:grid; grid-template-columns:minmax(0,1fr) minmax(320px,.92fr); align-items:center; gap:clamp(34px,6vw,84px); }
        .ngo-hero-copy { position:relative; z-index:1; }
        .ngo-hero-copy .kicker { margin-bottom:17px; }
        .ngo-hero-copy .lead { max-width:58ch; margin-top:21px; color:var(--muted); }
        .hero-actions { gap:12px; margin-top:30px; }
        .ngo-hero-visual { position:relative; margin:0; min-width:0; }
        .ngo-hero-visual:after { position:absolute; z-index:-1; right:-14px; bottom:-14px; width:70%; height:70%; border-radius:0 0 28px 0; background:var(--green); opacity:.13; content:''; }
        .ngo-hero-visual img { width:100%; height:clamp(300px,37vw,470px); object-fit:cover; border-radius:180px 180px 22px 22px; box-shadow:0 24px 60px rgba(23,40,59,.16); }
        .ngo-hero-visual figcaption { margin-top:10px; color:var(--muted); font-size:11px; }
        .ngo-hero-art { position:relative; min-height:360px; border-radius:180px 180px 24px 24px; background:linear-gradient(150deg,rgba(0,138,60,.13),rgba(10,54,99,.08)); }
        .ngo-hero-art span { position:absolute; width:72px; height:72px; border:1px solid rgba(0,138,60,.28); border-radius:50%; }
        .ngo-hero-art span:nth-child(1) { top:18%; left:17%; box-shadow:0 0 0 16px rgba(0,138,60,.05); }
        .ngo-hero-art span:nth-child(2) { right:18%; top:37%; width:112px; height:112px; background:rgba(0,138,60,.12); }
        .ngo-hero-art span:nth-child(3) { bottom:13%; left:31%; width:42px; height:42px; background:rgba(10,54,99,.12); }
        .ngo-breadcrumbs { margin-bottom:20px; color:var(--muted); font-size:12px; }
        .ngo-breadcrumbs a:hover { color:var(--green); }
        .pad-section { padding-block:var(--gap); }
        .paper { background:var(--paper); border:0; }
        .section-head { max-width:760px; margin-bottom:clamp(28px,4vw,46px); }
        .section-head.center { margin-inline:auto; }
        .section-head p { margin-top:13px; line-height:1.75; }
        .ngo-axis-layout { display:grid; grid-template-columns:minmax(280px,.92fr) minmax(0,1.08fr); grid-template-areas:"photo copy"; align-items:center; gap:clamp(30px,6vw,78px); }
        .ngo-axis-layout.is-reversed { grid-template-columns:minmax(0,1.08fr) minmax(280px,.92fr); grid-template-areas:"copy photo"; }
        .ngo-axis-photo { grid-area:photo; margin:0; }
        .ngo-axis-photo img { width:100%; height:clamp(280px,34vw,430px); object-fit:cover; border-radius:22px 112px 22px 22px; box-shadow:0 22px 48px rgba(23,40,59,.14); }
        .ngo-axis-layout.is-reversed .ngo-axis-photo img { border-radius:112px 22px 22px 22px; }
        .ngo-axis-copy { grid-area:copy; }
        .ngo-axis-copy .h-section { margin-bottom:14px; }
        .ngo-axis-copy .lead { margin-bottom:22px; }
        .ngo-axis-copy .check-list { display:grid; gap:12px; padding:0; background:transparent; border:0; box-shadow:none; }
        .ngo-axis-copy .check-list li { padding:0 0 12px 29px; border-bottom:1px solid var(--border); }
        .ngo-axis-copy .check-list li:last-child { border-bottom:0; }
        .ngo-axis-action { margin-top:22px; }
        .president-message { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1.05fr); align-items:center; gap:clamp(30px,5vw,68px); }
        .president-message.no-visual { grid-template-columns:1fr; }
        .president-message.no-visual .president-message-visual { display:none; }
        .president-message-visual { position:relative; min-width:0; }
        .president-message-photo { width:100%; height:clamp(290px,34vw,420px); object-fit:cover; border-radius:22px 100px 22px 22px; box-shadow:0 22px 48px rgba(23,40,59,.14); }
        .president-portrait { position:absolute; right:-20px; bottom:18px; width:clamp(96px,12vw,150px); margin:0; padding:7px; background:#fff; border-radius:50%; box-shadow:0 12px 32px rgba(23,40,59,.2); }
        .president-portrait img { width:100%; aspect-ratio:1; object-fit:cover; border-radius:50%; }
        .president-message-copy { position:relative; margin:0; padding:clamp(26px,4vw,46px); background:#fff; border:1px solid var(--border); border-left:4px solid var(--green); border-radius:4px 20px 20px 4px; box-shadow:0 14px 38px rgba(23,40,59,.07); }
        .president-message-copy:before { position:absolute; top:10px; right:24px; color:rgba(0,138,60,.12); font:700 110px/1 Georgia,serif; content:'“'; }
        .president-message-copy p { position:relative; margin-top:12px; color:var(--ink); font-size:clamp(17px,1.65vw,20px); font-weight:500; line-height:1.8; }
        .president-message-copy footer { display:grid; gap:3px; margin-top:24px; padding-top:17px; border-top:1px solid var(--border); }
        .president-message-copy footer strong { color:var(--navy); font-size:14px; }
        .president-message-copy footer span { color:var(--muted); font-size:12px; }
        .president-message.portrait-only .president-message-visual { display:flex; min-height:280px; align-items:center; justify-content:center; background:var(--paper); border-radius:22px; }
        .president-message.portrait-only .president-portrait { position:relative; inset:auto; width:clamp(150px,19vw,220px); }
        .grid-2,.grid-3 { gap:20px; }
        .rule-card,.fact,.realisation-row,.media-item,.gallery-item { border-radius:18px; }
        .rule-card { padding:clamp(22px,3vw,30px); border:1px solid var(--border); box-shadow:0 10px 34px rgba(23,40,59,.055); transition:transform .22s ease,box-shadow .22s ease; }
        .rule-card:hover { transform:translateY(-4px); box-shadow:0 18px 42px rgba(23,40,59,.1); }
        .rule-card h3 { font-size:20px; }
        .rule-card p { line-height:1.75; }
        .fact { padding:22px; background:#fff; border:1px solid var(--border); border-left:3px solid var(--green); }
        .fact strong { color:var(--navy); font-size:clamp(23px,2.7vw,32px); }
        .check-list { padding:24px; background:#fff; border:1px solid var(--border); border-radius:18px; }
        .realisation-row { overflow:hidden; border:1px solid var(--border); box-shadow:0 10px 35px rgba(23,40,59,.06); }
        .realisation-body { padding:clamp(24px,4vw,42px); }
        .realisation-body h3 { font-size:clamp(22px,2.8vw,30px); }
        .page-hero { padding:clamp(54px,7vw,82px) 0; background:linear-gradient(120deg,#0a3663,#0c4a80); }
        .page-hero:before { background:radial-gradient(ellipse at 85% 0%,rgba(127,212,164,.18),transparent 38%),linear-gradient(90deg,rgba(8,39,71,.88),rgba(10,54,99,.5)); }
        .page-hero .h-page { font-size:clamp(34px,4.5vw,54px); }
        .page-hero .kicker { color:#7fd4a4; }
        .page-hero-layout { display:grid; grid-template-columns:minmax(0,.95fr) minmax(300px,1.05fr); align-items:center; gap:clamp(30px,5vw,68px); }
        .page-hero-copy .h-page { margin-top:8px; }
        .page-hero p { max-width:62ch; line-height:1.75; }
        .page-hero-visual { position:relative; margin:0; }
        .page-hero-visual img { width:100%; height:clamp(250px,30vw,390px); object-fit:cover; border:5px solid rgba(255,255,255,.16); border-radius:22px 84px 22px 22px; box-shadow:0 22px 48px rgba(0,0,0,.2); }
        .page-hero-visual figcaption { margin-top:9px; color:rgba(255,255,255,.76); font-size:11px; }
        .blog-card-image { width:100%; aspect-ratio:16/10; object-fit:cover; border-radius:12px; margin-bottom:16px; }
        .blog-featured-link { display:grid; align-items:center; gap:clamp(22px,4vw,42px); }
        .article-prose { color:var(--ink); font-size:17px; line-height:1.9; }
        .article-prose p + p { margin-top:1.35em; }
        .realisation-filters { display:flex; flex-wrap:wrap; align-items:end; gap:14px; margin-bottom:30px; padding:20px; background:#fff; border:1px solid var(--border); border-radius:16px; }
        .realisation-filters label { display:grid; gap:7px; min-width:180px; color:var(--ink); font-size:12px; font-weight:600; }
        .realisation-filters select { min-height:46px; padding:9px 12px; background:#fff; border:1px solid var(--border); border-radius:9px; }
        .realisation-cards,.news-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:20px; }
        .realisation-card,.news-story-card { overflow:hidden; background:#fff; border:1px solid var(--border); border-radius:17px; box-shadow:0 8px 26px rgba(23,40,59,.055); transition:transform .2s ease,box-shadow .2s ease; }
        .realisation-card:hover,.news-story-card:hover { transform:translateY(-4px); box-shadow:0 18px 38px rgba(23,40,59,.12); }
        .realisation-card-image,.news-story-image { position:relative; display:block; overflow:hidden; aspect-ratio:16/10; background:var(--paper); }
        .realisation-card-image img,.news-story-image img { width:100%; height:100%; object-fit:cover; transition:transform .35s ease; }
        .realisation-card:hover img,.news-story-card:hover img { transform:scale(1.035); }
        .realisation-card-body,.news-story-body { display:flex; flex-direction:column; align-items:flex-start; min-height:240px; padding:20px; }
        .realisation-card-body h3,.news-story-body h3 { margin:8px 0; color:var(--navy); font-size:20px; }
        .realisation-card-body .text-link,.news-story-body .text-link { margin-top:auto; padding-top:16px; }
        .realisation-tag { position:absolute; top:12px; left:12px; padding:7px 11px; color:var(--navy); background:#fff; border-radius:999px; font-size:10px; font-weight:700; }
        .realisation-impact-callout { display:flex; flex-wrap:wrap; align-items:center; gap:12px; padding:18px 22px; background:#eef7f1; border-left:4px solid var(--green); border-radius:12px; }
        .realisation-impact-callout span { color:var(--green); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; }
        .realisation-impact-callout strong { color:var(--navy); font-size:19px; }
        .section-action { display:flex; justify-content:center; width:100%; margin:clamp(26px,3.5vw,40px) auto 0; }
        .section-action .btn { display:inline-flex; align-items:center; justify-content:center; gap:9px; min-width:190px; }
        .section-action .btn span { transition:transform .2s ease; }
        .section-action .btn:hover span { transform:translateX(3px); }
        .impact-timeline { position:relative; display:grid; gap:0; max-width:880px; margin:0 auto; padding:0; list-style:none; }
        .impact-timeline:before { position:absolute; top:10px; bottom:20px; left:90px; width:2px; background:linear-gradient(var(--green),rgba(0,138,60,.08)); content:''; }
        .impact-timeline li { position:relative; display:grid; grid-template-columns:72px 1fr; gap:38px; padding-bottom:26px; }
        .impact-timeline li>div { position:relative; padding:20px 22px; background:#fff; border:1px solid var(--border); border-radius:14px; }
        .impact-timeline li>div:before { position:absolute; top:22px; left:-29px; width:14px; height:14px; border:4px solid #fff; border-radius:50%; background:var(--green); box-shadow:0 0 0 1px var(--green); content:''; }
        .timeline-date { color:var(--green); font-size:12px; font-weight:700; }
        .timeline-banner,.section-illustration { max-width:960px; margin:26px auto 0; }
        .timeline-banner img,.section-illustration img { width:100%; max-height:380px; aspect-ratio:21/8; object-fit:cover; border-radius:16px; }
        .section-illustration figcaption { margin-top:8px; color:var(--muted); font-size:11px; }
        .card-media { margin:-30px -30px 20px; overflow:hidden; border-radius:14px 14px 0 0; }
        .card-media img { width:100%; aspect-ratio:16/9; object-fit:cover; }
        .site-footer { position:relative; margin-top:10px; }
        .site-footer:before { display:block; height:5px; background:linear-gradient(90deg,var(--green) 0 35%,var(--navy) 35% 100%); content:''; }
        .site-footer h4 { letter-spacing:.08em; }
        .footer-links a,.footer-meta a { transition:color .18s ease; }
        .footer-links a:hover,.footer-meta a:hover { color:#fff; }
        .field input,.field select,.field textarea { min-height:48px; border-radius:10px; border-color:var(--border); transition:border-color .2s ease,box-shadow .2s ease; }
        .field input:focus,.field select:focus,.field textarea:focus { border-color:var(--green); box-shadow:0 0 0 3px rgba(0,138,60,.1); }
        :focus-visible { outline:3px solid #f0b34f; outline-offset:3px; }
        img[data-lightbox] { border-radius:14px; }
        @media (max-width:860px) {
            .ngo-hero-grid { grid-template-columns:1fr; gap:30px; }
            .ngo-hero-visual { max-width:620px; }
            .ngo-hero-visual img { height:clamp(270px,55vw,390px); border-radius:130px 130px 20px 20px; }
            .ngo-hero-art { min-height:280px; }
            .ngo-axis-layout,.ngo-axis-layout.is-reversed { grid-template-columns:1fr; grid-template-areas:"photo" "copy"; gap:25px; }
            .ngo-axis-photo img,.ngo-axis-layout.is-reversed .ngo-axis-photo img { height:clamp(250px,58vw,390px); border-radius:18px 90px 18px 18px; }
            .president-message { grid-template-columns:1fr; gap:28px; }
            .president-message-photo { height:clamp(250px,58vw,370px); }
            .president-portrait { right:10px; bottom:-14px; }
            .page-hero-layout { grid-template-columns:1fr; }
            .page-hero-visual { max-width:650px; }
            .realisation-cards,.news-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
        }
        @media (max-width:640px) {
            .nav-bar { min-height:68px; }
            .ngo-hero { padding-block:42px 50px; }
            .h-display { font-size:clamp(37px,12vw,52px); }
            .hero-actions .btn { width:auto; }
            .ngo-hero-visual img { height:280px; }
            .grid-2,.grid-3 { gap:14px; }
            .realisation-cards,.news-grid { grid-template-columns:1fr; }
            .blog-featured-link { grid-template-columns:1fr !important; }
            .impact-timeline:before { left:7px; }
            .impact-timeline li { grid-template-columns:1fr; gap:8px; padding-left:28px; }
            .impact-timeline li>div:before { left:-28px; }
        }
        @media (prefers-reduced-motion:reduce) { *,*::before,*::after { scroll-behavior:auto !important; transition-duration:.01ms !important; } }
    </style>
</head>
<body>

    @include('layouts.nav')

    <main>
        @yield('content')
    </main>

    @include('layouts.footer')

    {{-- Lightbox : boîte de dialogue native, activée sur toute image marquée data-lightbox. --}}
    <dialog class="lightbox" id="lightbox" aria-label="Aperçu de l'image">
        <button type="button" class="lightbox-close" aria-label="Fermer l'aperçu">&times;</button>
        <img src="" alt="">
    </dialog>

    <script>
        document.querySelector('.nav-toggle').addEventListener('click', function () {
            document.querySelector('.nav-links').classList.toggle('is-open');
        });

        (function () {
            var lightbox = document.getElementById('lightbox');
            if (! lightbox) { return; }

            var target = lightbox.querySelector('img');

            document.addEventListener('click', function (event) {
                var img = event.target.closest('img[data-lightbox]');

                if (! img) { return; }

                target.src = img.currentSrc || img.src;
                target.alt = img.alt || '';
                lightbox.showModal();
            });

            lightbox.querySelector('.lightbox-close').addEventListener('click', function () {
                lightbox.close();
            });

            lightbox.addEventListener('click', function (event) {
                if (event.target === lightbox) { lightbox.close(); }
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
