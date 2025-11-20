<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f0f2f5;
        }
        .container {
            max-width: 450px;
            margin: 80px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; margin-bottom: 25px; color: #333; }
        .input-group { margin-bottom: 18px; }
        label { display:block; margin-bottom:6px; font-size:14px; color:#555; }
        input {
            width:100%; padding:12px; border:1px solid #ccc; border-radius:6px;
            font-size:16px; box-sizing:border-box;
        }
        input:focus { outline:none; border-color:#4e73df; }
        .submit-btn {
            width:100%; padding:12px; margin-top:10px;
            background:#1cc88a; color:#fff; border:none; border-radius:6px;
            font-size:17px; cursor:pointer; transition:0.2s;
        }
        .submit-btn:hover { background:#17a673; }
        .back-link { display:block; text-align:center; margin-top:15px; color:#666; text-decoration:none; }
        .back-link:hover { text-decoration:underline; }
        .error, .success {
            padding:10px; margin-bottom:15px; border-radius:6px; font-size:14px;
        }
        .error {
            color:#d9534f; background:#f9d6d5;
        }
        .success {
            color:#218838; background:#d4edda;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Регистрация</h2>

    {{-- Ошибки валидации --}}
    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Наши кастомные ошибки/успех --}}
    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('register.post') }}">
        @csrf

        <div class="input-group">
            <label for="email">Почта *</label>
            <input type="email" id="email" name="email" required value="{{ old('email') }}" placeholder="example@mail.com">
        </div>

        <div class="input-group">
            <label for="username">Имя пользователя (логин) *</label>
            <input type="text" id="username" name="username" required value="{{ old('username') }}" placeholder="user123">
        </div>

        <div class="input-group">
            <label for="phone">Телефон (опционально)</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+48 123 456 789">
        </div>

        <div class="input-group">
            <label for="password">Пароль *</label>
            <input type="password" id="password" name="password" required placeholder="Введите пароль">
        </div>

        <div class="input-group">
            <label for="password_confirmation">Повторите пароль *</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Введите пароль ещё раз">
        </div>

        <button type="submit" class="submit-btn">Зарегистрироваться</button>
    </form>

    <a href="{{ route('login') }}" class="back-link">Уже есть аккаунт? Войти</a>
</div>

</body>
</html>
