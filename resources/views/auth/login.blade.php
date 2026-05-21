<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Wedding Invitation</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #0a0a0f;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .login-card {
            background: #1a1a2e;
            border: 1px solid rgba(201,168,76,0.25);
            border-radius: 20px;
            padding: 40px 36px;
            width: 100%;
            max-width: 380px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-logo h1 {
            font-family: 'Playfair Display', serif;
            color: #c9a84c;
            font-size: 24px;
        }
        .login-logo p {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: #c9a84c;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            padding: 11px 14px;
            background: #0f0f1a;
            border: 1px solid rgba(201,168,76,0.25);
            border-radius: 8px;
            color: #e8e8f0;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: border-color .2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #c9a84c;
            box-shadow: 0 0 0 2px rgba(201,168,76,0.12);
        }
        .error-msg {
            font-size: 12px;
            color: #fca5a5;
            margin-top: 4px;
        }
        .btn-login {
            width: 100%;
            padding: 13px;
            background: #c9a84c;
            color: #0a0a0f;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 8px;
            transition: background .2s;
            letter-spacing: .02em;
        }
        .btn-login:hover { background: #e8c97a; }
        .status-msg {
            padding: 10px 14px;
            background: rgba(34,197,94,0.1);
            border: 1px solid rgba(34,197,94,0.3);
            border-radius: 8px;
            color: #86efac;
            font-size: 13px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <svg viewBox="0 0 80 30" fill="none" style="width:80px;margin:0 auto 12px;display:block">
                <line x1="0" y1="15" x2="28" y2="15" stroke="#c9a84c" stroke-width="0.5" stroke-dasharray="3 3"/>
                <circle cx="40" cy="15" r="6" fill="none" stroke="#c9a84c" stroke-width="0.8"/>
                <circle cx="40" cy="15" r="2" fill="#c9a84c"/>
                <line x1="52" y1="15" x2="80" y2="15" stroke="#c9a84c" stroke-width="0.5" stroke-dasharray="3 3"/>
            </svg>
            <h1>Wedding Admin</h1>
            <p>Sign in to manage your invitation</p>
        </div>

        @if (session('status'))
            <div class="status-msg">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input id="email" type="email" name="email" class="form-control"
                    value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input id="password" type="password" name="password" class="form-control"
                    required autocomplete="current-password">
                @error('password') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>
    </div>
</body>
</html>
