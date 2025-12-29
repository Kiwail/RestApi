<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Kompānijas — pieteikumi</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: system-ui, sans-serif;
            background: linear-gradient(180deg, #ffffff 0%, #e6daff 90%);
            color: #1f2933;
                display: flex;
    flex-direction: column;
    min-height: 100vh;
        }
html, body {
    height: 100%;
}
.page {
    flex: 1;
}

        a { text-decoration: none; color: inherit; }

        /* HEADER */

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 40px;
            background: #fff;
            border-bottom: 1px solid #dde2f0;
            position: sticky;
            top: 0;
            z-index: 10;
        }
            footer {
        width: 100%;
        background: #2b2b2b;
        text-align: center;
        padding: 18px 0;
        font-size: 16px;
        color: #dcdcdc;
        margin-top: 40px;
        }


        .header-left {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .logo {
            font-size: 24px;
            font-weight: 800;
            color: #111827;
        }

        .nav {
            display: flex;
            gap: 12px;
            font-size: 15px;
        }

        .nav-link {
            padding: 6px 12px;
            border-radius: 999px;
            color: #6b7280;
        }

        .nav-link.active {
            background: #e0e7ff;
            color: #111827;
            font-weight: 600;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 14px;
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #e6fffa;
            border-radius: 999px;
            padding: 6px 14px;
            font-weight: 600;
            font-size: 13px;
        }

        .user-avatar {
            width: 22px;
            height: 22px;
            border-radius: 999px;
            background: #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
        }

        .logout-btn {
            padding: 6px 10px;
            border-radius: 999px;
            border: none;
            background: #fee2e2;
            color: #b91c1c;
            cursor: pointer;
        }

        .logout-btn:hover { background: #fecaca; }

        /* LAYOUT */

        .page {
            display: flex;
            padding: 24px 40px 40px;
            gap: 24px;
        }

        .sidebar { width: 220px; }

        .sidebar-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .sidebar-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-item { margin-bottom: 6px; }

        .sidebar-link {
            display: block;
            padding: 8px 10px;
            border-radius: 10px;
            font-size: 14px;
            color: #4b5563;
        }

        .sidebar-link.active,
        .sidebar-link:hover {
            background: #e5edff;
            color: #111827;
            font-weight: 500;
        }

        .main { flex: 1; }

        .search-row { margin-bottom: 18px; }

        .search-input-wrap { position: relative; }

        .search-input {
            width: 100%;
            padding: 10px 14px 10px 40px;
            border-radius: 999px;
            border: 1px solid #d1d5db;
            font-size: 14px;
            background: #fff;
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: #9ca3af;
        }

        .alerts { margin-bottom: 10px; }

        .alert {
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        /* COMPANY LIST */

        .company-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 18px;
        }

        .company-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            border-radius: 18px;
            padding: 10px 16px;
            box-shadow: 0 2px 5px rgba(15, 23, 42, 0.05);
        }

        .company-main {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .company-logo {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            background: #e5edff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
            color: #4f46e5;
            flex-shrink: 0;
        }

        .company-name {
            font-size: 15px;
            font-weight: 600;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .company-meta { font-size: 12px; color: #9ca3af; }

        .btn-add {
            padding: 6px 14px;
            border-radius: 999px;
            border: none;
            background: #e5edff;
            color: #111827;
            font-size: 13px;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-add:hover { background: #c7d2fe; }

        @media (max-width: 960px) {
            .page { flex-direction: column; }
            .sidebar { width: 100%; }
            .company-list { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
@php
    $displayName = $user['first_name'] ?? $user['username'] ?? $user['email'];
    $initials = mb_strtoupper(mb_substr($displayName, 0, 1));
@endphp

<header class="header">
    <div style="display:flex;align-items:center;gap:24px;">
        <a href="{{ route(name: 'home') }}" class="logo">Resti<span style="color:#4f46e5;">API</span></a>
        <nav class="nav">
            <a href="{{ route('home') }}" class="nav-link">Sākums</a>
            <span href="{{ route('apply.form') }}" class="nav-link active">Kompānijas</span>
            <a href="{{ route('profile.show') }}" class="nav-link">Profils</a>
        </nav>
    </div>

    <div class="header-right">
        <div class="user-pill">
            <div class="user-avatar">{{ $initials }}</div>
            <a href="{{ route('profile.show') }}">{{ $displayName }}</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn">Iziet</button>
            </form>
        </div>
    </div>
</header>

<div class="page">
    <aside class="sidebar">
        <div class="sidebar-title">Kompānijas</div>
        <ul class="sidebar-list">
            <li class="sidebar-item"><span class="sidebar-link active">Visas</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Mobilo sakaru operatori</span></li>
            <li class="sidebar-item"><span class="sidebar-link">TV un internets</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Elektrība</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Īpašuma apsaimniekošana</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Ūdensapgāde</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Apkure</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Gāze</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Apsardze</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Veselība un sports</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Cits</span></li>
        </ul>
    </aside>

    <main class="main">
        <div class="search-row">
            <div class="search-input-wrap">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" placeholder="Meklēt kompāniju…">
            </div>
        </div>

        <div class="alerts">
            @if ($errors->any())
                <div class="alert alert-error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
        </div>

        <div class="company-list">
            @forelse ($companies as $company)
                <div class="company-card">
                    <div class="company-main">
                        <div class="company-logo">
                            {{ mb_strtoupper(mb_substr($company->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="company-name">{{ $company->name }}</div>
                            <div class="company-meta">{{ $company->slug }}</div>
                        </div>
                    </div>

                    <div class="company-actions">
                        <form method="POST" action="{{ route('apply.submit') }}">
                            @csrf
                            <input type="hidden" name="company_id" value="{{ $company->id }}">
                            <button type="submit" class="btn-add">Pievienot</button>
                        </form>
                    </div>
                </div>
            @empty
                <p>Nav pieejamu kompāniju.</p>
            @endforelse
        </div>
    </main>
</div>
<footer>
    © 2025 RestApi — Visas tiesības aizsargātas
</footer>

</body>
</html>
