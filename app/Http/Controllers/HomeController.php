<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $authUser = session('auth_user');

        // Если не авторизован — редирект
        if (!$authUser) {
            return redirect()->route('login');
        }

        $userId = $authUser['id'];

        // 1) компании пользователя из БД resti_auth
        $companyIds = DB::connection('auth')
            ->table('auth_user_company')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->pluck('company_id');

        // 2) сами компании из resti_core
        $activeCompanies = DB::connection('master')
            ->table('company')
            ->whereIn('id', $companyIds)
            ->get();

        return view('home', [
            'user' => $authUser,          // ← добавили
            'activeCompanies' => $activeCompanies
        ]);
    }
}
