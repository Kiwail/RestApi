<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Resti API</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .header {
            width: 100%;
            padding: 20px;
            background: #ffffff;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: flex-end;
            box-sizing: border-box;
        }

        .auth-btn {
            padding: 10px 16px;
            background: #4e73df;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.2s;
        }

        .auth-btn:hover {
            background: #2e59d9;
        }

        .content {
            padding: 40px;
            text-align: center;
        }

        h1 {
            margin-top: 100px;
            font-size: 40px;
            color: #333;
        }

        p {
            color: #666;
            font-size: 18px;
        }

    </style>
</head>
<body>

<div class="header">
    <a href="login" class="auth-btn">Авторизация</a>
    <a href="register" class="auth-btn">Регистрация</a>
</div>

<div class="content">
    <h1>Добро пожаловать в Resti API</h1>
    <p>Ваш универсальный интерфейс для работы с многотенантной системой</p>
</div>

</body>
</html>
