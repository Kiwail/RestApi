<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Mans profils — Resti</title>
    <style>
        * { box-sizing: border-box; }
html, body {
    height: 100%;
}

body {
    margin: 0;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    background: linear-gradient(180deg, #ffffff 0%, #e6daff 90%);
    color: #1f2933;

    /* 👇 главное */
    display: flex;
    flex-direction: column;
}

/* Контейнер страницы */
.page {
    flex: 1;           /* растягивается */
    padding: 40px;
    max-width: 900px;
    margin: 0 auto;
}

        a { text-decoration:none; color: inherit; }
        .header{
            display:flex;align-items:center;justify-content:space-between;
            padding:14px 40px;background:#fff;border-bottom:1px solid #dde2f0;
            position:sticky;top:0;z-index:10;
        }
        .logo{font-size:24px;font-weight:800;}
        .nav{display:flex;gap:12px;font-size:15px;}
        .nav-link{padding:6px 12px;border-radius:999px;color:#6b7280;}
        .nav-link.active{background:#e0e7ff;color:#111827;font-weight:600;}
        .page{padding:40px;max-width:900px;margin:0 auto;}
        .card{background:#fff;border-radius:20px;padding:20px 26px;box-shadow:0 3px 20px rgba(0,0,0,.05);margin-bottom:22px;}
        .card-title{font-size:20px;font-weight:700;margin-bottom:16px;}
        .row{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
        .field label{display:block;font-size:12px;color:#6b7280;margin-bottom:6px;}
        .field input{
            width:100%;padding:10px 12px;border-radius:12px;border:1px solid #e5e7eb;
            font-size:14px;outline:none;
        }
        .btn{
            padding:10px 16px;border-radius:999px;border:none;
            background:#111827;color:#fff;cursor:pointer;font-weight:700;
        }
        .btn:hover{opacity:.9;}
        .msg{
            padding:10px 14px;border-radius:12px;margin-bottom:14px;font-size:14px;
            background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;
        }
        .err{
            padding:10px 14px;border-radius:12px;margin-bottom:14px;font-size:14px;
            background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;
        }
        footer{width:100%;background:#2b2b2b;text-align:center;padding:18px 0;font-size:16px;color:#dcdcdc;margin-top:40px;}
        @media (max-width: 900px){
            .page{padding:20px 16px;}
            .header{padding:14px 16px;}
            .row{grid-template-columns:1fr;}
            .nav{display:none;}
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
            <span class="nav-link active">Profils</span>
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

    @if(session('success'))
        <div class="msg">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="err">
            @foreach($errors->all() as $e)
                <div>• {{ $e }}</div>
            @endforeach
        </div>
    @endif

    <div class="card">
        <div class="card-title">Profila dati</div>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf

            <div class="row">
                <div class="field">
                    <label>E-pasts</label>
                    <input value="{{ $user['email'] }}" disabled>
                </div>

                <div class="field">
                    <label>Lietotājvārds</label>
                    <input name="username" value="{{ old('username', $user['username']) }}" placeholder="piem. Ruslans">
                </div>

                <div class="field">
                    <label>Tālrunis (nav obligāts)</label>
                    <input name="phone" value="{{ old('phone', $user['phone']) }}" placeholder="piem. 29123456">
                </div>
            </div>

            <div style="margin-top:16px;">
                <button class="btn">Saglabāt</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-title">Mainīt paroli</div>

        <form method="POST" action="{{ route('profile.password') }}">
            @csrf

            <div class="row">
                <div class="field">
                    <label>Pašreizējā parole</label>
                    <input type="password" name="current_password" placeholder="••••••">
                </div>

                <div class="field">
                    <label>Jaunā parole</label>
                    <input type="password" name="new_password" placeholder="••••••">
                </div>

                <div class="field">
                    <label>Atkārtot jauno paroli</label>
                    <input type="password" name="new_password_confirmation" placeholder="••••••">
                </div>
            </div>

            <div style="margin-top:16px;">
                <button class="btn">Nomainīt paroli</button>
            </div>
        </form>
    </div>

</div>

<footer>© 2025 RestApi — Visas tiesības aizsargātas</footer>
</body>
</html>
