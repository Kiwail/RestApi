<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $authUser = $request->session()->get('auth_user');
        if (!$authUser) {
            return redirect()->route('login');
        }

        // jaunākie dati no resti_auth
        $user = DB::connection('auth')
            ->table('auth_user')
            ->where('id', $authUser['id'])
            ->first();

        if (!$user) {
            // ja sesijā ir, bet DB nav — piespiedu kārtā izrakstām
            $request->session()->forget('auth_user');
            return redirect()->route('login');
        }

        return view('profile', [
            'user' => [
                'id'       => $user->id,
                'email'    => $user->email,
                'username' => $user->username,
                'phone'    => $user->phone,
            ],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $authUser = $request->session()->get('auth_user');
        if (!$authUser) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'username' => 'nullable|string|max:64',
            'phone'    => 'nullable|string|max:32',
        ]);

        DB::connection('auth')
            ->table('auth_user')
            ->where('id', $authUser['id'])
            ->update([
                'username' => $data['username'] ?? null,
                'phone'    => $data['phone'] ?? null,
            ]);

        // atjaunosim sesiju (lai header uzreiz rādītu jauno vārdu)
        $fresh = DB::connection('auth')
            ->table('auth_user')
            ->where('id', $authUser['id'])
            ->first();

        $request->session()->put('auth_user', [
            'id'       => $fresh->id,
            'email'    => $fresh->email,
            'username' => $fresh->username,
            'phone'    => $fresh->phone,
        ]);

        return back()->with('success', 'Profils atjaunināts');
    }

    public function updatePassword(Request $request)
    {
        $authUser = $request->session()->get('auth_user');
        if (!$authUser) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'current_password' => 'required|string|min:6',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        $row = DB::connection('auth')
            ->table('auth_password')
            ->where('user_id', $authUser['id'])
            ->first();

        if (!$row || !Hash::check($data['current_password'], $row->hash)) {
            return back()->withErrors(['current_password' => 'Nepareiza pašreizējā parole']);
        }

        DB::connection('auth')
            ->table('auth_password')
            ->where('user_id', $authUser['id'])
            ->update([
                'algo'   => 'bcrypt',
                'hash'   => Hash::make($data['new_password']),
                'set_at' => now(),
            ]);

        return back()->with('success', 'Parole nomainīta');
    }
}
