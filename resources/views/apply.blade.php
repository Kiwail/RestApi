<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Подать заявку</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .header {
            width: 100%;
            padding: 15px 25px;
            background: #ffffff;
            border-bottom: 1px solid #ddd;
            box-sizing: border-box;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .user-info {
            font-size: 14px;
            color: #555;
        }

        .user-name {
            font-weight: bold;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            padding: 25px 30px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }

        h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        .field {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            color: #555;
        }

        select,
        textarea {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        textarea {
            min-height: 80px;
            resize: vertical;
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            background: #4e73df;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn:hover {
            background: #2e59d9;
        }

        .alert {
            padding: 10px 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .alert-error {
            background: #f9d6d5;
            color: #b52b27;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .logout-btn {
            padding: 8px 14px;
            background: #e74a3b;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.2s;
        }

        .logout-btn:hover {
            background: #c0392b;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="user-info">
            {{-- здесь выводим имя/фамилию или username/email --}}
            <span class="user-name">
                @php
                // ожидаем, что в сессии есть auth_user с полями first_name/last_name или username
                $displayName = $user['first_name'] ?? null;
                if ($displayName && !empty($user['last_name'] ?? null)) {
                $displayName .= ' ' . $user['last_name'];
                } elseif (empty($displayName) && !empty($user['username'] ?? null)) {
                $displayName = $user['username'];
                } else {
                $displayName = $user['email'] ?? 'Пользователь';
                }
                @endphp
                {{ $displayName }}

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">Выйти</button>
                </form>
                
            </span>

        </div>
    </div>

    <div class="container">
        <h2>Подать заявку в компанию</h2>

        {{-- ошибки валидации --}}
        @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
            @endforeach
        </div>
        @endif

        {{-- флеш-сообщения --}}
        @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('apply.submit') }}">
            @csrf

            <div class="field">
                <label for="company_id">Компания</label>
                <select name="company_id" id="company_id" required>
                    <option value="">Выберите компанию</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}"
                        {{ old('company_id') == $company->id ? 'selected' : '' }}>
                        {{ $company->name }} ({{ $company->slug }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="message">Комментарий (опционально)</label>
                <textarea name="message" id="message" placeholder="Расскажите, для чего вы подаёте заявку...">{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="btn">Отправить заявку</button>
        </form>
    </div>

</body>

</html>