<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Wedding Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg: #0a0a0f;
            --card: #1a1a2e;
            --gold: #c9a84c;
            --gold-light: #e8c97a;
            --text: #e8e8f0;
            --muted: #6b7280;
            --border: #2a2a42;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--card);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
        }

        .sidebar-logo {
            padding: 24px 20px;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-logo h1 {
            font-family: 'Playfair Display', serif;
            color: var(--gold);
            font-size: 18px;
            font-weight: 700;
        }

        .sidebar-logo p {
            font-size: 11px;
            color: var(--muted);
            margin-top: 2px;
        }

        .sidebar-nav {
            padding: 16px 0;
            flex: 1;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 20px;
            color: var(--muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--gold);
            background: rgba(201, 168, 76, 0.08);
            border-left-color: var(--gold);
        }

        .nav-link svg { width: 18px; height: 18px; flex-shrink: 0; }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--border);
        }

        .user-info { font-size: 12px; color: var(--muted); margin-bottom: 8px; }
        .user-name { color: var(--text); font-size: 13px; font-weight: 500; }

        .btn-logout {
            display: block;
            padding: 8px 12px;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--muted);
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.2s;
            width: 100%;
        }

        .btn-logout:hover { border-color: #ef4444; color: #ef4444; }

        /* Main content */
        .main {
            margin-left: 240px;
            flex: 1;
            padding: 32px;
            min-height: 100vh;
        }

        .page-header {
            margin-bottom: 28px;
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            color: var(--text);
            font-weight: 600;
        }

        .page-subtitle {
            font-size: 13px;
            color: var(--muted);
            margin-top: 4px;
        }

        /* Cards */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
        }

        /* Alerts */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .alert-success { background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: #86efac; }
        .alert-error   { background: rgba(239,68,68,0.1);  border: 1px solid rgba(239,68,68,0.3);  color: #fca5a5; }

        /* Tables */
        .table-wrap { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; font-size: 13px; }

        th {
            text-align: left;
            padding: 10px 14px;
            color: var(--gold);
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
            vertical-align: middle;
        }

        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(201,168,76,0.04); }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-hadir    { background: rgba(34,197,94,0.15);  color: #86efac; }
        .badge-tidak    { background: rgba(239,68,68,0.15);  color: #fca5a5; }
        .badge-ragu     { background: rgba(234,179,8,0.15);  color: #fde68a; }
        .badge-pending  { background: rgba(107,114,128,0.2); color: var(--muted); }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s;
        }

        .btn-gold {
            background: var(--gold);
            color: #0a0a0f;
        }

        .btn-gold:hover { background: var(--gold-light); }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--muted);
        }

        .btn-outline:hover { border-color: var(--gold); color: var(--gold); }

        .btn-danger {
            background: transparent;
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
        }

        .btn-danger:hover { background: rgba(239,68,68,0.1); }

        .btn-sm { padding: 5px 12px; font-size: 12px; }

        /* Forms */
        .form-group { margin-bottom: 20px; }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            background: #0f0f1a;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 2px rgba(201,168,76,0.15);
        }

        textarea.form-control { resize: vertical; min-height: 100px; }

        /* Stat cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
        }

        .stat-label { font-size: 11px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-value { font-size: 32px; font-weight: 700; margin-top: 6px; color: var(--gold); }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.open { display: flex; }

        .modal {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 28px;
            max-width: 360px;
            width: 90%;
            text-align: center;
        }

        .modal h3 { font-family: 'Playfair Display', serif; color: var(--gold); margin-bottom: 16px; }

        .modal canvas, .modal img { max-width: 100%; margin: 0 auto; display: block; }

        /* Pagination */
        .pagination { display: flex; gap: 8px; margin-top: 20px; flex-wrap: wrap; align-items: center; }
        .pagination a, .pagination span { padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; }
        .pagination a { border: 1px solid var(--border); color: var(--muted); }
        .pagination a:hover { border-color: var(--gold); color: var(--gold); }
        .pagination span.current { background: var(--gold); color: #0a0a0f; border: 1px solid var(--gold); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); z-index: 100; }
            .main { margin-left: 0; padding: 20px; }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-logo">
            <h1>Wedding Admin</h1>
            <p>Invitation Manager</p>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.wedding') }}" class="nav-link {{ request()->routeIs('admin.wedding*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                Wedding Details
            </a>
            <a href="{{ route('admin.guests') }}" class="nav-link {{ request()->routeIs('admin.guests*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Guests
            </a>
            <a href="{{ route('admin.rsvp') }}" class="nav-link {{ request()->routeIs('admin.rsvp*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                RSVP List
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="user-info">Logged in as</div>
            <div class="user-name">{{ auth()->user()->name }}</div>
            <form method="POST" action="{{ route('logout') }}" style="margin-top:10px">
                @csrf
                <button type="submit" class="btn-logout">Sign Out</button>
            </form>
        </div>
    </aside>

    <main class="main">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
