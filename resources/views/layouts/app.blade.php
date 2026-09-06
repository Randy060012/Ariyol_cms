<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AFRIYOL - African Young Leaders : organisation non gouvernementale dédiée aux Droits Humains, à la Paix, à l'Environnement et au Leadership des Jeunes au Togo.">
    <title>@yield('title', 'AFRIYOL - African Young Leaders')</title>

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
            background: var(--navy) url('https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1920&q=80') center / cover no-repeat;
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

        .page-hero { background: var(--navy); color: var(--white); padding: clamp(72px, 10vw, 112px) 0; border-bottom: 4px solid var(--green); }
        .page-hero p { color: #b9c8dc; margin-top: 12px; max-width: 60ch; }

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
            .media-strip { grid-template-columns: repeat(2, 1fr); }
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
            .grid-3, .media-strip { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
            .hero-actions .btn { width: 100%; justify-content: center; }
        }
    </style>
    @stack('styles')
</head>
<body>

    @include('layouts.nav')

    <main>
        @yield('content')
    </main>

    @include('layouts.footer')

    <script>
        document.querySelector('.nav-toggle').addEventListener('click', function () {
            document.querySelector('.nav-links').classList.toggle('is-open');
        });
    </script>

    @stack('scripts')
</body>
</html>
