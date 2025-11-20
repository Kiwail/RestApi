<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f0f2f5;
        }

        .container {
            max-width: 400px;
            margin: 120px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        .input-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            color: #555;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
        }

        input:focus {
            outline: none;
            border-color: #4e73df;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background: #4e73df;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 17px;
            cursor: pointer;
            transition: 0.2s;
        }

        .submit-btn:hover {
            background: #2e59d9;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #666;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .error {
            color: #d9534f;
            background: #f9d6d5;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Авторизация</h2>

    {{-- Ошибка авторизации --}}
    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    <form method="POST" action="/login">
        @csrf

        <div class="input-group">
            <label for="email">Почта</label>
            <input type="email" id="email" name="email" required placeholder="example@mail.com">
        </div>

        <div class="input-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required placeholder="Введите пароль">
        </div>

        <button type="submit" class="submit-btn">Войти</button>
    </form>

    <a href="/" class="back-link">Вернуться назад</a>
</div>

</body>
</html>
