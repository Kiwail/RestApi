<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>RestApi – Rēķinu sistēma</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, #ffffff 0%, #e6daff 90%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

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

        /* Buttons */
        .login {
            color: #7a42ff;
            border: 2px solid #7a42ff;
        }

        .register {
            background: #7a42ff;
            color: white;
            border: 2px solid #ffffffff;

        }

        /* Hover effects */
        .login:hover {
            color: #fff;
            background: #7a42ff;
        }

        .register:hover {
            background: #ffffffff;
            color: #7a42ff;
            border: 2px solid #7a42ff;

        }

        .main {
            text-align: center;
            margin-top: 120px;
            padding: 20px;
            flex: 1;
        }

        h1 {
            font-size: 52px;
            color: #111;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .desc {
            font-size: 22px;
            color: #444;
            max-width: 650px;
            margin: 0 auto;
            line-height: 1.6;
        }

        footer {
            width: 100%;
            background: #2b2b2b;
            text-align: center;
            padding: 18px 0;
            font-size: 16px;
            color: #dcdcdc;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="nav-left">
        <a href="#">RestApi</a>
    </div>

    <div class="nav-right">
        <a href="login" class="login">Ienākt</a>
        <a href="register" class="register">Reģistrēties</a>
    </div>
</div>

<div class="main">
    <h1>Visi tavi rēķini vienuviet</h1>
    <p class="desc">
        Ērta un droša sistēma rēķinu pārvaldībai, komunālajiem maksājumiem,
        pakalpojumiem un ikmēneša norēķiniem.
    </p>
</div>

<footer>
    © 2025 RestApi — Visi tiesības aizsargātas
</footer>

</body>
</html>
