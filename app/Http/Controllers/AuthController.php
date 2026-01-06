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
        // 1. Validācija
        $data = $request->validate([
            'email'                 => 'required|email',
            'username'              => 'required|string|max:64',
            'phone'                 => 'nullable|string|max:32',
            'password'              => 'required|confirmed|min:6',
        ]);

        // 2. Pārbaudām, vai šāds e-pasts vēl nav aizņemts
        $exists = DB::connection('auth')
            ->table('auth_user')
            ->where('email', $data['email'])
            ->exists();

        if ($exists) {
            return back()
                ->with('error', 'Lietotājs ar šādu e-pastu jau eksistē')
                ->withInput();
        }

        // 3. Ģenerējam UUID PHP pusē
        $userId = (string) Str::uuid();

        // 4. Izveidojam lietotāju tabulā auth_user
        DB::connection('auth')->table('auth_user')->insert([
            'id'             => $userId,
            'email'          => $data['email'],
            'username'       => $data['username'],
            'phone'          => $data['phone'],
            'status'         => 'active',
            'email_verified' => false,
            // ja pievienosi role:
            // 'role'        => 'user',
            'created_at'     => now(),
        ]);

        // 5. Izveidojam paroles ierakstu tabulā auth_password
        DB::connection('auth')->table('auth_password')->insert([
            'user_id' => $userId,
            'algo'    => 'bcrypt',
            'hash'    => Hash::make($data['password']),
            'set_at'  => now(),
        ]);

        // 6. Pāradresējam uz pieteikšanos
        return redirect()
            ->route('login')
            ->with('success', 'Reģistrācija veiksmīga! Tagad varat pieteikties.');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Meklējam lietotāju resti_auth
        $user = DB::connection('auth')
            ->table('auth_user')
            ->join('auth_password', 'auth_password.user_id', '=', 'auth_user.id')
            ->where('email', $data['email'])
            ->first();

        if (!$user || !Hash::check($data['password'], $user->hash)) {
            return back()
                ->with('error', 'Nepareizs lietotājvārds vai parole')
                ->withInput();
        }

        // Saglabājam datus sesijā
        session([
            'auth_user' => [
                'id'       => $user->id,
                'email'    => $user->email,
                'username' => $user->username,
                'role'     => $user->role ?? 'user',
            ],
        ]);

        // 👉 Pāradrese atkarībā no lomas
        if (($user->role ?? 'user') === 'admin') {
            // admina maršruts, nosauc kā vēlies
            return redirect()->route('admin.index');
        }

        // parasts lietotājs
        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        // pilnībā dzēšam visus autorizācijas datus
        $request->session()->forget('auth_user');
        $request->session()->flush();

        return redirect()->route('login')->with('success', 'Jūs izrakstījāties no konta');
    }
}
