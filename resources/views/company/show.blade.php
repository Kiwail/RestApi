<!DOCTYPE html>
<html lang="lv">

<head>
    <meta charset="UTF-8">
    <title>{{ $company->name }} — Resti</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(180deg, #ffffff 0%, #e6daff 90%);
            color: #1f2933;
            min-height: 100vh;
        }

        html, body { height: 100%; }

        body { display: flex; flex-direction: column; }

        .page { flex: 1; }

        a { text-decoration: none; color: inherit; }

        footer {
            width: 100%;
            background: #2b2b2b;
            text-align: center;
            padding: 18px 0;
            font-size: 16px;
            color: #dcdcdc;
            margin-top: 40px;
        }

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
            box-shadow: 0 3px 20px rgba(0, 0, 0, 0.05);
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

        .pill {
            display:inline-block;
            padding:4px 10px;
            border-radius:999px;
            font-size:12px;
            font-weight:700;
            background:#f3f4f6;
            color:#111827;
        }

        .pill-warning { background:#fff7ed; color:#b45309; }
        .pill-ok { background:#ecfdf5; color:#047857; }

        .list-row {
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            padding:8px 0;
            border-bottom:1px solid #e5e7eb;
        }

        .list-row:last-child { border-bottom:none; }

        .muted { font-size:12px; color:#6b7280; margin-top:2px; }

        .company-hero {
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:20px;
            margin-bottom: 18px;
        }

        .hero-left h2 {
            margin:0;
            font-size:26px;
            font-weight:800;
        }

        .hero-left .sub {
            margin-top:6px;
            color:#6b7280;
            font-size:13px;
        }

        .hero-right {
            text-align:right;
            color:#6b7280;
            font-size:12px;
        }

        @media (max-width: 980px) {
            .row { grid-template-columns: 1fr; }
            .header { padding: 14px 16px; }
            .page { padding: 20px 16px; }
            .nav { display:none; }
        }
    </style>
</head>

<body>
@php
    $displayName = $user['username'] ?? $user['email'];
    $initials = mb_strtoupper(mb_substr($displayName, 0, 1));
@endphp

<header class="header">
    <div style="display:flex;align-items:center;gap:24px;">
        <a href="{{ route('home') }}" class="logo">Resti<span style="color:#4f46e5;">API</span></a>
        <nav class="nav">
            <a href="{{ route('home') }}" class="nav-link">Sākums</a>
            <a href="{{ route('apply.form') }}" class="nav-link">Kompānijas</a>
            <a href="{{ route('profile.show') }}" class="nav-link">Profils</a>
            <span class="nav-link active">{{ $company->name }}</span>
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

    <div class="company-hero">
        <div class="hero-left">
            <h2>{{ $company->name }}</h2>

        </div>

        <div class="hero-right">
            @if($client)
                <div><span class="pill pill-ok">Klients aktīvs</span></div>
                <div style="margin-top:6px;">
                </div>
                <div style="margin-top:2px;">
                    {{ $client->name }} · {{ $client->email }}
                </div>
            @else
                <div><span class="pill pill-warning">Nav klienta profila tenant DB</span></div>
                <div style="margin-top:6px;">Iespējams, approve laikā client netika izveidots.</div>
            @endif
        </div>
    </div>

    <div class="row">
        {{-- ===== RĒĶINI ===== --}}
        <div class="card">
            <div class="card-title">Neapmaksātie rēķini</div>

            @if(!$client)
                <div class="card-inner">
                    <div style="font-size:35px;">🧾</div>
                    <div class="empty-title">Nav pieejams</div>
                    <div class="empty-desc">Vispirms jāizveido client ieraksts tenant DB.</div>
                </div>
            @elseif($unpaidInvoices->isEmpty())
                <div class="card-inner">
                    <div style="font-size:35px;">🧾</div>
                    <div class="empty-title">Pagaidām nav rēķinu</div>
                    <div class="empty-desc">Šeit tiks parādīti tavi neapmaksātie rēķini šajā kompānijā.</div>
                </div>
            @else
                <div class="card-inner" style="padding-top: 8px; padding-bottom: 8px;">
                    @foreach($unpaidInvoices as $invoice)
                        <div class="list-row">
                            <div style="padding-right:90px;">
                                <div style="font-weight:600; font-size:14px;">
                                    Rēķins № {{ $invoice->number }}
                                </div>
                                <div class="muted">
                                    Izrakstīts: {{ \Carbon\Carbon::parse($invoice->issued_on)->format('d.m.Y') }}
                                </div>
                                <div class="muted">
                                    Apmaksāt līdz: {{ \Carbon\Carbon::parse($invoice->due_on)->format('d.m.Y') }}
                                </div>
                            </div>

                            <div style="text-align:right;">
                                <div style="font-weight:800;">
                                    {{ number_format($invoice->amount_cents / 100, 2, ',', ' ') }} {{ $invoice->currency }}
                                </div>
                                <div style="font-size:12px; margin-top:4px;">
                                    <span class="pill pill-warning">
                                        {{ $invoice->status === 'unpaid' ? 'Gaidā maksājumu' : $invoice->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===== LĪGUMI ===== --}}
        <div class="card">
            <div class="card-title" style="display:flex;justify-content:space-between;align-items:center;">
                <span>Līgumi</span>
                <a href="{{ route('apply.form') }}"><button class="btn-small">Pievienot kompāniju</button></a>
            </div>

            @if(!$client)
                <div class="card-inner">
                    <div style="font-size:35px;">📄</div>
                    <div class="empty-title">Nav pieejams</div>
                    <div class="empty-desc">Vispirms jāizveido client ieraksts tenant DB.</div>
                </div>
            @elseif($contracts->isEmpty())
                <div class="card-inner">
                    <div style="font-size:35px;">📄</div>
                    <div class="empty-title">Pagaidām nav līgumu</div>
                    <div class="empty-desc">Šeit tiks parādīti tavi līgumi šajā kompānijā.</div>
                </div>
            @else
                <div class="card-inner" style="padding-top: 8px; padding-bottom: 8px;">
                    @foreach($contracts as $c)
                        <div class="list-row">
                            <div style="padding-right:90px;">
                                <div style="font-weight:700; font-size:14px;">
                                    {{ $c->number }}
                                </div>
                                <div class="muted">
                                    Izveidots: {{ \Carbon\Carbon::parse($c->created_at)->format('d.m.Y') }}
                                </div>
                                <div class="muted">
                                    Parakstīts: {{ $c->signed_at ? \Carbon\Carbon::parse($c->signed_at)->format('d.m.Y') : '—' }}
                                </div>
                            </div>

                            <div style="text-align:right;">
                                <span class="pill">
                                    {{ $c->status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

<footer>
    © 2025 RestApi — Visas tiesības aizsargātas
</footer>

</body>
</html>
