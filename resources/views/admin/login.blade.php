<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | Administration AFRIYOL</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --navy: #0a3663; --navy-soft: #0c4a80; --green: #008a3c;
            --ink: #16233a; --muted: #5b6b80; --border: #dfe5ec; --white: #fff;
            --font: 'Poppins', system-ui, sans-serif;
        }
        body { font-family: var(--font); color: var(--ink); }
        .login-wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; background: var(--navy); }
        .login-card { width: 100%; max-width: 400px; background: var(--white); border-top: 4px solid var(--green); padding: 36px 32px; }
        .brand-line { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
        .brand-line img { width: 46px; height: 46px; }
        .brand-line strong { color: var(--navy); letter-spacing: .08em; display: block; font-size: 16px; }
        .brand-line span { color: var(--muted); font-size: 11px; letter-spacing: .12em; text-transform: uppercase; }
        h1 { font-size: 18px; margin-bottom: 20px; color: var(--navy); }
        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
        .field input { width: 100%; font-family: inherit; font-size: 14px; padding: 10px 12px; border: 1px solid var(--border); border-radius: 2px; }
        .field input:focus { outline: none; border-color: var(--navy); }
        .error-text { display: block; margin-top: 5px; font-size: 12.5px; color: #b42318; }
        .btn { display: inline-flex; width: 100%; justify-content: center; padding: 11px 20px; font-size: 14px; font-weight: 600; border: 1px solid transparent; cursor: pointer; font-family: inherit; background: var(--navy); color: var(--white); border-radius: 2px; }
        .btn:hover { background: var(--navy-soft); }
        .alert-success { background: #eef7f1; border: 1px solid #bfe3cd; border-left: 3px solid var(--green); color: #14572f; padding: 12px 16px; font-size: 13.5px; margin-bottom: 20px; }
        .back-link { display: block; text-align: center; margin-top: 18px; font-size: 13px; color: rgba(255,255,255,.75); }
        .back-link:hover { color: #fff; }
        .remember { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--muted); margin-bottom: 18px; }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-card">
            <div class="brand-line">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo AFRIYOL">
                <div>
                    <strong>AFRIYOL</strong>
                    <span>Administration</span>
                </div>
            </div>

            <h1>Connexion</h1>

            @if (session('success'))
                <div class="alert-success" role="status">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login.attempt') }}">
                @csrf

                <div class="field">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                    @error('password') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <label class="remember">
                    <input type="checkbox" name="remember"> Se souvenir de moi
                </label>

                <button type="submit" class="btn">Se connecter</button>
            </form>
        </div>
    </div>
    <a class="back-link" href="{{ route('home') }}">Retour au site</a>
</body>
</html>
