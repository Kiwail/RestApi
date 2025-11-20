<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Валидация
        $data = $request->validate([
            'email'                 => 'required|email',
            'username'              => 'required|string|max:64',
            'phone'                 => 'nullable|string|max:32',
            'password'              => 'required|confirmed|min:6',
        ]);

        // 2. Проверяем, что такой email ещё не занят
        $exists = DB::connection('auth')
            ->table('auth_user')
            ->where('email', $data['email'])
            ->exists();

        if ($exists) {
            return back()
                ->with('error', 'Пользователь с такой почтой уже существует')
                ->withInput();
        }

        // 3. Генерируем UUID в PHP
        $userId = (string) Str::uuid();

        // 4. Создаём пользователя в auth_user
        DB::connection('auth')->table('auth_user')->insert([
            'id'             => $userId,
            'email'          => $data['email'],
            'username'       => $data['username'],
            'phone'          => $data['phone'],
            'status'         => 'active',
            'email_verified' => false,
            // если добавишь role:
            // 'role'        => 'user',
            'created_at'     => now(),
        ]);

        // 5. Создаём запись пароля в auth_password
        DB::connection('auth')->table('auth_password')->insert([
            'user_id' => $userId,
            'algo'    => 'bcrypt',
            'hash'    => Hash::make($data['password']),
            'set_at'  => now(),
        ]);

        // 6. Перенаправляем на логин
        return redirect()
            ->route('login')
            ->with('success', 'Регистрация успешна! Теперь вы можете войти.');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = DB::connection('auth')
            ->table('auth_user')
            ->join('auth_password', 'auth_password.user_id', '=', 'auth_user.id')
            ->where('email', $data['email'])
            ->first();

        if (!$user || !Hash::check($data['password'], $user->hash)) {
            return back()
                ->with('error', 'Неверный логин или пароль')
                ->withInput();
        }

        session([
            'auth_user' => [
                'id'    => $user->id,
                'email' => $user->email,
                'role'  => $user->role ?? 'user',
            ],
        ]);

        return redirect('/admin');
    }
}
