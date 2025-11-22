<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function showForm(Request $request)
    {
        // проверяем авторизацию
        $authUser = $request->session()->get('auth_user');
        if (!$authUser) {
            return redirect()->route('login');
        }

        // получаем список активных компаний из resti_core (подключение master/ core)
        $companies = DB::connection('master')
            ->table('company')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('apply', [
            'user'      => $authUser,
            'companies' => $companies,
        ]);
    }

    public function submit(Request $request)
    {
        // проверяем авторизацию
        $authUser = $request->session()->get('auth_user');
        if (!$authUser) {
            return redirect()->route('login');
        }

        // валидация
        $data = $request->validate([
            'company_id' => 'required|string',
            'message'    => 'nullable|string|max:500',
        ]);

        // проверяем, что компания существует и активна
        $company = DB::connection('master')
            ->table('company')
            ->where('id', $data['company_id'])
            ->where('status', 'active')
            ->first();

        if (!$company) {
            return back()->with('error', 'Компания не найдена или отключена');
        }

        // создаём запись в company_join_request
        DB::connection('master')
            ->table('company_join_request')
            ->insert([
                'id'           => (string) Str::uuid(),
                'company_id'   => $company->id,
                'auth_user_id' => $authUser['id'],
                'status'       => 'pending',
                'message'      => $data['message'],
                'created_at'   => now(),
            ]);

        // (опционально) помечаем связь user–company как pending в auth_user_company
        DB::connection('auth')
            ->table('auth_user_company')
            ->updateOrInsert(
                [
                    'user_id'    => $authUser['id'],
                    'company_id' => $company->id,
                ],
                [
                    'status'     => 'pending',
                    'created_at' => now(),
                ]
            );

        return back()->with('success', 'Заявка отправлена и ожидает рассмотрения');
    }
}
