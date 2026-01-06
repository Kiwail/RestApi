<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Autorizācija</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, #ffffff 0%, #e6daff 90%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* NAVBAR */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 40px;
            font-size: 18px;
        }

        .nav-left a {
            text-decoration: none;
            color: #000;
            font-size: 24px;
        }

        .nav-right a {
            margin-left: 15px;
            padding: 8px 16px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.25s ease;
            display: inline-block;
        }

        .login-btn {
            color: #7a42ff;
            border: 2px solid #7a42ff;
        }

        .register-btn {
            background: #7a42ff;
            color: #fff;
            border: 2px solid #ffffffff;

        }

        /* NEW HOVER STYLE */
        .login-btn:hover {
            color: #fff;
            background: #7a42ff;
        }

        .register-btn:hover {
            background: #ffffffff;
            color: #7a42ff;
            border: 2px solid #7a42ff;

        }

        /* FORM CARD */
        .container {
            max-width: 400px;
            margin: 100px auto;
            padding: 35px;
            background: rgba(255,255,255,0.6);
            backdrop-filter: blur(6px);
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #3d2b62;
            font-size: 26px;
        }

        .input-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 15px;
            color: #3a2d66;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #b8a8e3;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            transition: 0.2s;
        }

        input:focus {
            outline: none;
            border-color: #7a42ff;
            box-shadow: 0 0 4px rgba(122,66,255,0.45);
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            margin-top: 12px;
            background: #7a42ff;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 17px;
            cursor: pointer;
            transition: 0.2s;
            font-weight: bold;
        }

        .submit-btn:hover {
            background: #7a42ff;
            color: #fff;
            transform: scale(1.05);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #5a4d87;
            text-decoration: none;
            transition: 0.2s;
        }

        .back-link:hover {
            color: #7a42ff;
        }

        .error {
            color: #d9534f;
            background: #f9d6d5;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-size: 14px;
            text-align: center;
            font-weight: bold;
        }

        /* FOOTER */
        footer {
            width: 100%;
            background: #2b2b2b;
            text-align: center;
            padding: 18px 0;
            font-size: 16px;
            color: #dcdcdc;
            margin-top: auto;
        }
                    .logo {
            font-size: 24px;
            font-weight: 800;
            gap: 24px;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="nav-left">
        <a href="{{ route('home') }}" class="logo">Resti<span style="color:#4f46e5;">API</span></a>
    </div>
    <div class="nav-right">
        <a href="login" class="login-btn">Ienākt</a>
        <a href="register" class="register-btn">Reģistrācija</a>
    </div>
</div>

<!-- FORM -->
<div class="container">
    <h2>Autorizācija</h2>

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    <form method="POST" action="login">
        @csrf

        <div class="input-group">
            <label for="email">E-pasts</label>
            <input type="email" id="email" name="email" required placeholder="example@mail.com">
        </div>

        <div class="input-group">
            <label for="password">Parole</label>
            <input type="password" id="password" name="password" required placeholder="Ievadiet paroli">
        </div>

        <button type="submit" class="submit-btn">Ienākt</button>
    </form>

    <a href="{{ route('register') }}" class="back-link">Nav konta? Reģistrēties</a>
    <a href="index" class="back-link">Atpakaļ</a>
</div>

<!-- FOOTER -->
<footer>
    © 2025 RestApi — Visas tiesības aizsargātas
</footer>

</body>
</html>
