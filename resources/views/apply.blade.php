<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Компании — заявки</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f3f6fb;
            color: #1f2933;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* ====== HEADER ====== */

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 40px;
            background: #ffffff;
            border-bottom: 1px solid #dde2f0;
            position: sticky;
            top: 0;
            z-index: 10;
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

        .lang {
            color: #6b7280;
            cursor: pointer;
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
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
        }

        .logout-btn {
            padding: 6px 10px;
            border-radius: 999px;
            border: none;
            background: #fee2e2;
            color: #b91c1c;
            font-size: 12px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background: #fecaca;
        }

        /* ====== LAYOUT ====== */

        .page {
            display: flex;
            padding: 24px 40px 40px;
            gap: 24px;
        }

        .sidebar {
            width: 220px;
        }

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

        .sidebar-item {
            margin-bottom: 6px;
        }

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

        /* ====== MAIN ====== */

        .main {
            flex: 1;
        }

        .search-row {
            margin-bottom: 18px;
        }

        .search-input-wrap {
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 10px 14px 10px 40px;
            border-radius: 999px;
            border: 1px solid #d1d5db;
            font-size: 14px;
            background: #ffffff;
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: #9ca3af;
        }

        .alerts {
            margin-bottom: 10px;
        }

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

        /* ====== COMPANY LIST ====== */

        .company-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 18px;
        }

        .company-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
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

        .company-meta {
            font-size: 12px;
            color: #9ca3af;
        }

        .company-actions form {
            margin: 0;
        }

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

        .btn-add:hover {
            background: #c7d2fe;
        }

        @media (max-width: 960px) {
            .page {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
            }
            .company-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
@php
    // Формируем отображаемое имя
    $displayName = $user['first_name'] ?? null;
    if ($displayName && !empty($user['last_name'] ?? null)) {
        $displayName .= ' ' . $user['last_name'];
    } elseif (empty($displayName) && !empty($user['username'] ?? null)) {
        $displayName = $user['username'];
    } else {
        $displayName = $user['email'] ?? 'Пользователь';
    }

    $initials = mb_strtoupper(mb_substr($displayName, 0, 1));
@endphp

<header class="header">
    <div class="header-left">
        <div class="logo">Resti<span style="color:#4f46e5;">API</span></div>

        <nav class="nav">
            <a href="{{ route('home') }}" class="nav-link">Главная</a>
            <span class="nav-link active">Компании</span>
            <span class="nav-link">Архив</span>
            <span class="nav-link">Сообщения</span>
        </nav>
    </div>

    <div class="header-right">
        <div class="lang">Русский ▾</div>

        <div class="user-pill">
            <div class="user-avatar">{{ $initials }}</div>
            <span>{{ $displayName }}</span>
            <form action="{{ route('logout') }}" method="POST" style="margin-left:8px;">
                @csrf
                <button type="submit" class="logout-btn">Выйти</button>
            </form>
        </div>
    </div>
</header>

<div class="page">
    <!-- ЛЕВЫЙ САЙДБАР -->
    <aside class="sidebar">
        <div class="sidebar-title">Компании</div>
        <ul class="sidebar-list">
            <li class="sidebar-item">
                <span class="sidebar-link active">Все</span>
            </li>
            <li class="sidebar-item"><span class="sidebar-link">Операторы мобильной связи</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Телевидение и интернет</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Электричество</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Обслуживание недвижимости</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Водоснабжение</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Отопление</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Газ</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Охрана</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Здоровье и фитнес</span></li>
            <li class="sidebar-item"><span class="sidebar-link">Другое</span></li>
        </ul>
    </aside>

    <!-- ОСНОВНОЙ КОНТЕНТ -->
    <main class="main">
        <div class="search-row">
            <div class="search-input-wrap">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" placeholder="Найти компанию…">
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
                            <button type="submit" class="btn-add">Добавить</button>
                        </form>
                    </div>
                </div>
            @empty
                <p>Активных компаний пока нет.</p>
            @endforelse
        </div>
    </main>
</div>

</body>
</html>
