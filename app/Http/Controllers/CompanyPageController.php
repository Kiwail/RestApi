<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class CompanyPageController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $authUser = $request->session()->get('auth_user');
        if (!$authUser) {
            return redirect()->route('login');
        }

        // 1) Находим компанию в master (resti_core)
        $company = DB::connection('master')
            ->table('company')
            ->where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if (!$company) {
            abort(404, 'Company not found');
        }

        // 2) Проверяем что пользователь реально "active" клиент этой компании
        // ВАЖНО: auth_user_company лежит в resti_auth, поэтому connection('auth')
        $isMember = DB::connection('auth')
            ->table('auth_user_company')
            ->where('user_id', $authUser['id'])
            ->where('company_id', $company->id)
            ->where('status', 'active')
            ->exists();

        if (!$isMember) {
            abort(403, 'You are not an active client of this company');
        }

        // 3) Берём tenant db_name по company_id
        $tenant = DB::connection('master')
            ->table('tenant_registry')
            ->where('company_id', $company->id)
            ->first();

        if (!$tenant) {
            abort(500, 'Tenant DB not found in registry');
        }

        // 4) Переключаем mysql на tenant базу
        $this->switchTenant($tenant->db_name);

        // 5) Ищем client в tenant базе по auth_user_id
        $client = DB::connection('mysql')
            ->table('client')
            ->where('auth_user_id', $authUser['id'])
            ->first();

        if (!$client) {
            // пользователь активный, но client запись ещё не создана в tenant (значит approve не сделал client insert)
            return view('company.show', [
                'user' => $authUser,
                'company' => $company,
                'dbName' => $tenant->db_name,
                'client' => null,
                'unpaidInvoices' => collect(),
                'contracts' => collect(),
            ]);
        }

        // 6) Достаём данные компании для этого клиента
        $unpaidInvoices = DB::connection('mysql')
            ->table('invoice')
            ->where('client_id', $client->id)
            ->where('status', 'unpaid')
            ->orderByDesc('issued_on')
            ->limit(50)
            ->get();

        $contracts = DB::connection('mysql')
            ->table('contract')
            ->where('client_id', $client->id)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('company.show', [
            'user' => $authUser,
            'company' => $company,
            'dbName' => $tenant->db_name,
            'client' => $client,
            'unpaidInvoices' => $unpaidInvoices,
            'contracts' => $contracts,
        ]);
    }

    protected function switchTenant(string $dbName): void
    {
        Config::set('database.connections.mysql.database', $dbName);
        DB::purge('mysql');
        DB::reconnect('mysql');
    }
}
