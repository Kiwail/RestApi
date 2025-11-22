<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная — Resti</title>

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f3f6fb;
            color: #1f2933;
        }
        a { text-decoration: none; color: inherit; }

        /* ===== HEADER ===== */
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
        .header-left {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        .logo {
            font-size: 24px;
            font-weight: 800;
        }
        .nav { display: flex; gap: 12px; font-size: 15px; }
        .nav-link { padding: 6px 12px; border-radius: 999px; color: #6b7280; }
        .nav-link.active { background: #e0e7ff; color: #111827; font-weight: 600; }

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

        /* ===== PAGE ===== */
        .page {
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        /* ===== CARD ===== */
        .card {
            background: #fff;
            border-radius: 20px;
            padding: 20px 26px;
            box-shadow: 0 3px 20px rgba(0,0,0,0.05);
        }
        .card-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
        }
        .card-inner {
            padding: 40px 20px;
            text-align: center;
            color: #6b7280;
        }
        .empty-title {
            margin-top: 14px;
            font-size: 17px;
            font-weight: 600;
        }
        .empty-desc {
            margin-top: 6px;
            font-size: 14px;
            color: #9ca3af;
        }

        .btn-small {
            padding: 6px 14px;
            border-radius: 999px;
            border: none;
            font-size: 14px;
            cursor: pointer;
            background: #111827;
            color: #fff;
        }
        .btn-small:hover { opacity: 0.85; }
        .company-card {
    padding: 20px;
    text-align: center;
    border-radius: 12px;
    border: 1px solid #ddd;
    background: #fff;
    margin-bottom: 15px;
}

.company-title {
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 8px;
}

.company-desc {
    font-size: 14px;
    color: #666;
}

.btn-view {
    display: inline-block;
    margin-top: 12px;
    padding: 8px 16px;
    background: #4a90e2;
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
}

    </style>
</head>
<body>
@php
    $displayName = $user['username'] ?? $user['email'];
    $initials = mb_strtoupper(mb_substr($displayName, 0, 1));
@endphp

<header class="header">
    <div class="header-left">
        <div class="logo">Resti<span style="color:#4f46e5;">API</span></div>

        <nav class="nav">
            <span class="nav-link active">Главная</span>
            <a href="{{ route('apply.form') }}" class="nav-link">Компании</a>
            <span class="nav-link">Архив</span>
            <span class="nav-link">Сообщения</span>
        </nav>
    </div>

    <div class="header-right">
        <div class="user-pill">
            <div class="user-avatar">{{ $initials }}</div>
            <span>{{ $displayName }}</span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn">Выйти</button>
            </form>
        </div>
    </div>
</header>

<div class="page">

    <div class="row">

        <!-- ===== МОИ СЧЕТА ===== -->
        <div class="card">
            <div class="card-title">Мои счета</div>

            <div class="card-inner">
                <div style="font-size:35px;">🧾</div>
                <div class="empty-title">Счетов пока нет</div>
                <div class="empty-desc">Здесь будут отображаться счета, полученные от компаний.</div>
            </div>
        </div>

        <!-- ===== МОИ КОМПАНИИ ===== -->
        <div class="card">
            <div class="card-title" style="display:flex;justify-content:space-between;align-items:center;">
                <span>Мои компании</span>

                <a href="{{ route('apply.form') }}">
                    <button class="btn-small">Добавить</button>
                </a>
            </div>

<div class="card-container">

    @if($activeCompanies->isEmpty())
        <div class="card-inner">
            <div style="font-size:35px;">⚪➕</div>
            <div class="empty-title">Компаний пока нет</div>
            <div class="empty-desc">Они появятся после добавления.</div>
        </div>
    @else
        @foreach($activeCompanies as $company)
            <div class="card-inner company-card">
                <div class="company-title">{{ $company->name }}</div>
                <div class="company-desc">Вы являетесь клиентом этой компании</div>
                <div class="company-actions">
                    <a href="/company/{{ $company->slug }}" class="btn-view">Перейти</a>
                </div>
            </div>
        @endforeach
    @endif

</div>
        </div>
    </div>

</div>

</body>
</html>
