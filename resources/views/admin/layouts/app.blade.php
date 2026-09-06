<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Administration') | AFRIYOL</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --navy: #0a3663;
            --navy-soft: #0c4a80;
            --green: #008a3c;
            --ink: #16233a;
            --muted: #5b6b80;
            --border: #dfe5ec;
            --paper: #f5f7f9;
            --white: #ffffff;
            --font: 'Poppins', system-ui, sans-serif;
        }

        body {
            font-family: var(--font);
            font-size: 15px;
            line-height: 1.6;
            color: var(--ink);
            background: var(--paper);
            -webkit-font-smoothing: antialiased;
        }

        a { color: inherit; text-decoration: none; }
        ul { list-style: none; }

        .admin-shell { display: flex; min-height: 100vh; }

        /* Sidebar */
        .admin-sidebar {
            width: 250px;
            flex-shrink: 0;
            background: var(--navy);
            color: #b9c8dc;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 22px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, .12);
        }
        .sidebar-brand img { width: 42px; height: 42px; }
        .sidebar-brand strong { display: block; color: var(--white); font-size: 15px; letter-spacing: .08em; }
        .sidebar-brand span { display: block; font-size: 11px; letter-spacing: .1em; text-transform: uppercase; }

        .sidebar-nav { flex: 1; padding: 18px 12px; display: grid; gap: 4px; }
        .sidebar-nav a {
            display: block;
            padding: 10px 14px;
            border-radius: 2px;
            font-size: 14px;
            font-weight: 500;
            transition: background-color .15s ease, color .15s ease;
        }
        .sidebar-nav a:hover { background: rgba(255, 255, 255, .08); color: var(--white); }
        .sidebar-nav a.is-active {
            background: var(--white);
            color: var(--navy);
            font-weight: 600;
        }
        .sidebar-nav .nav-group-label {
            font-size: 11px;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #7d93ac;
            padding: 16px 14px 6px;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255, 255, 255, .12);
            font-size: 12.5px;
            display: grid;
            gap: 10px;
        }
        .sidebar-footer a { color: var(--white); font-weight: 500; }
        .sidebar-footer a:hover { text-decoration: underline; }

        /* Content */
        .admin-main { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .admin-topbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .admin-topbar h1 { font-size: 19px; font-weight: 600; color: var(--navy); }
        .admin-topbar .user { font-size: 13px; color: var(--muted); }

        .admin-content { padding: 28px; width: 100%; }

        /* Cards / panels */
        .panel {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 2px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .panel h2 { font-size: 16px; font-weight: 600; color: var(--navy); margin-bottom: 16px; }
        .panel .panel-sub { font-size: 13px; color: var(--muted); margin: -10px 0 16px; }

        /* Forms */
        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
        .field .hint { display: block; font-weight: 400; color: var(--muted); font-size: 12px; margin-top: 2px; }
        .field input[type="text"],
        .field input[type="email"],
        .field input[type="password"],
        .field input[type="url"],
        .field input[type="number"],
        .field input[type="file"],
        .field select,
        .field textarea {
            width: 100%;
            font-family: inherit;
            font-size: 14px;
            color: var(--ink);
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 2px;
            padding: 10px 12px;
        }
        .field input:focus, .field select:focus, .field textarea:focus {
            outline: none;
            border-color: var(--navy);
        }
        .field textarea { resize: vertical; min-height: 90px; }
        .error-text { display: block; margin-top: 5px; font-size: 12.5px; color: #b42318; }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 13.5px;
            font-weight: 600;
            border-radius: 2px;
            border: 1px solid transparent;
            cursor: pointer;
            font-family: inherit;
            transition: background-color .15s ease, color .15s ease, border-color .15s ease;
        }
        .btn-primary { background: var(--navy); color: var(--white); }
        .btn-primary:hover { background: var(--navy-soft); }
        .btn-green { background: var(--green); color: var(--white); }
        .btn-green:hover { background: #00732f; }
        .btn-ghost { background: transparent; border-color: var(--navy); color: var(--navy); }
        .btn-ghost:hover { background: var(--navy); color: var(--white); }
        .btn-danger { background: transparent; border-color: #b42318; color: #b42318; }
        .btn-danger:hover { background: #b42318; color: var(--white); }
        .btn-sm { padding: 6px 12px; font-size: 12.5px; }

        /* Tables */
        .table-wrap { overflow-x: auto; }
        table.admin-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .admin-table th {
            text-align: left;
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
            border-bottom: 2px solid var(--border);
            padding: 10px 12px;
        }
        .admin-table td { padding: 12px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        .admin-table tr:hover td { background: #f8fafc; }

        .badge {
            display: inline-block;
            font-size: 11.5px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 999px;
            border: 1px solid var(--border);
            color: var(--muted);
            background: var(--white);
        }
        .badge-green { border-color: #bfe3cd; background: #eef7f1; color: #14572f; }

        /* Alerts */
        .alert {
            border: 1px solid transparent;
            border-left-width: 3px;
            border-radius: 2px;
            padding: 12px 16px;
            font-size: 13.5px;
            margin-bottom: 20px;
        }
        .alert-success { background: #eef7f1; border-color: #bfe3cd; border-left-color: var(--green); color: #14572f; }
        .alert-error { background: #fdf1f0; border-color: #f2c6c2; border-left-color: #b42318; color: #8a1c13; }

        /* Stat cards */
        .stat-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-top: 3px solid var(--navy);
            padding: 18px 20px;
        }
        .stat-card strong { display: block; font-size: 28px; font-weight: 600; color: var(--navy); }
        .stat-card span { font-size: 13px; color: var(--muted); }
        .stat-card.green-top { border-top-color: var(--green); }

        /* Login page */
        .login-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: var(--navy);
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            background: var(--white);
            border-top: 4px solid var(--green);
            padding: 36px 32px;
        }
        .login-card .brand-line { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
        .login-card .brand-line img { width: 46px; height: 46px; }
        .login-card .brand-line strong { color: var(--navy); letter-spacing: .08em; display: block; }
        .login-card .brand-line span { color: var(--muted); font-size: 11px; letter-spacing: .12em; text-transform: uppercase; }

        /* Responsive */
        @media (max-width: 900px) {
            .admin-shell { flex-direction: column; }
            .admin-sidebar { width: 100%; height: auto; position: static; }
            .sidebar-nav { display: flex; flex-wrap: wrap; padding: 10px; }
            .sidebar-nav .nav-group-label { display: none; }
            .admin-content { padding: 20px 16px; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="admin-shell">

        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo AFRIYOL">
                <div>
                    <strong>AFRIYOL</strong>
                    <span>Administration</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">Tableau de bord</a>

                <span class="nav-group-label">Contenu</span>
                <a href="{{ route('admin.pages.index') }}" class="{{ request()->routeIs('admin.pages.*') ? 'is-active' : '' }}">Pages</a>
                <a href="{{ route('admin.posts.index') }}" class="{{ request()->routeIs('admin.posts.*') ? 'is-active' : '' }}">Articles du blog</a>
                <a href="{{ route('admin.settings.edit') }}" class="{{ request()->routeIs('admin.settings.*') ? 'is-active' : '' }}">Paramètres du site</a>

                <span class="group-label nav-group-label">Interactions</span>
                <a href="{{ route('admin.messages.index') }}" class="{{ request()->routeIs('admin.messages.*') ? 'is-active' : '' }}">Messages reçus</a>
            </nav>

            <div class="sidebar-footer">
                <a href="{{ route('home') }}" target="_blank">Voir le site</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm" style="width: 100%; justify-content: center;">Déconnexion</button>
                </form>
                <span>{{ auth()->user()->email }}</span>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <h1>@yield('title', 'Administration')</h1>
                <span class="user">{{ auth()->user()->name }}</span>
            </header>

            <div class="admin-content">
                @if (session('success'))
                    <div class="alert alert-success" role="status">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-error" role="alert">{{ session('error') }}</div>
                @endif

                @yield('content')
            </div>
        </div>

    </div>
    @stack('scripts')
</body>
</html>
