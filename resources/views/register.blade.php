<!DOCTYPE html>
<html lang="lv">

<head>
    <meta charset="UTF-8">
    <title>Reģistrācija</title>

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
            font-weight: bold;
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

        /* UPDATED BUTTONS */
        .login-btn {
            color: #7a42ff;
            border: 2px solid #7a42ff;
        }

        .register-btn {
            background: #7a42ff;
            color: white;
            border: 2px solid #ffffffff;
        }

        .login-btn:hover {
            background: #7a42ff;
            color: #fff;
        }

        .register-btn:hover {
            background: #ffffffff;
            color: #7a42ff;
            border: 2px solid #7a42ff;
        }

        /* CARD */
        .container {
            width: 550px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 14px;
            backdrop-filter: blur(6px);
            position: relative;
            left: 50%;
            transform: translateX(-50%);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #3d2b62;
            font-size: 26px;
        }

        .input-group {
            margin-bottom: 12px;
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
            padding: 11px;
            border: 1px solid #b8a8e3;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            transition: 0.2s;
        }

        input:focus {
            outline: none;
            border-color: #7a42ff;
            box-shadow: 0 0 4px rgba(122, 66, 255, 0.45);
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background: #7a42ff;
            color: #fff;
            border: 2px solid #ffffffff;
            border-radius: 8px;
            font-size: 17px;
            cursor: pointer;
            transition: 0.2s;
            font-weight: bold;
        }

        .submit-btn:hover {
            background: #ffffffff;
            color: #7a42ff;
            border: 2px solid #7a42ff;
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

        .error,
        .success {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-size: 14px;
        }

        .error {
            color: #d9534f;
            background: #f9d6d5;
        }

        .success {
            color: #218838;
            background: #d4edda;
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
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <div class="navbar">
        <div class="nav-left">
            <a href="index">RestApi</a>
        </div>
        <div class="nav-right">
            <a href="{{ route('login') }}" class="login-btn">Ienākt</a>
            <a href="{{ route('register') }}" class="register-btn">Reģistrācija</a>
        </div>
    </div>

    <!-- CARD -->
    <div class="container">
        <h2>Reģistrācija</h2>

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf

            <div class="input-group">
                <label for="email">E-pasts *</label>
                <input type="email" id="email" name="email" required value="{{ old('email') }}"
                    placeholder="example@mail.com">
            </div>

            <div class="input-group">
                <label for="username">Lietotājvārds *</label>
                <input type="text" id="username" name="username" required value="{{ old('username') }}"
                    placeholder="user123">
            </div>

            <div class="input-group">
                <label for="phone">Telefons</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+371 123 456 78">
            </div>

            <div class="input-group">
                <label for="password">Parole *</label>
                <input type="password" id="password" name="password" required placeholder="Ievadiet paroli">
            </div>

            <div class="input-group">
                <label for="password_confirmation">Atkārtojiet paroli *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    placeholder="Ievadiet paroli vēlreiz">
            </div>

            <button type="submit" class="submit-btn">Reģistrēties</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">Jau ir konts? Ienākt</a>
        <a href="index" class="back-link">Atpakaļ</a>
    </div>

    <footer>
        © 2025 RestApi — Visas tiesības aizsargātas
    </footer>

</body>

</html>