<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class HomeController extends Controller
{
    public function index()
    {
        $authUser = session('auth_user');

        // Если не авторизован — уводим на логин
        if (!$authUser) {
            return redirect()->route('login');
        }

        $userId = $authUser['id'];

        // 1) Компания-пользователь: связи лежат в resti_auth.auth_user_company
        $companyIds = DB::connection('auth')
            ->table('auth_user_company')
            ->where('user_id', $userId)
            ->where('status', 'active')   // принят компанией
            ->pluck('company_id');

        // 2) Сами компании в resti_core.company
        $activeCompanies = DB::connection('master')
            ->table('company')
            ->whereIn('id', $companyIds)
            ->get();

        // 3) tenant_registry: какая компания → какая БД
        $tenantByCompany = DB::connection('master')
            ->table('tenant_registry')
            ->whereIn('company_id', $companyIds)
            ->get()
            ->keyBy('company_id');

        // 4) Собираем все неоплаченные счета по всем tenant_* базам
        $unpaidInvoices = collect();

        foreach ($activeCompanies as $company) {
            $tenant = $tenantByCompany->get($company->id);
            if (!$tenant) {
                continue;
            }

            $dbName = $tenant->db_name;

            // Переключаем mysql на нужную tenant-базу
            Config::set('database.connections.mysql.database', $dbName);
            DB::purge('mysql');

            // Ищем счета текущего пользователя в этой базе
            $rows = DB::connection('mysql')
                ->table('invoice')
                ->join('client', 'client.id', '=', 'invoice.client_id')
                ->where('client.auth_user_id', $userId)
                ->where('invoice.status', 'unpaid')
                ->select(
                    'invoice.id',
                    'invoice.number',
                    'invoice.issued_on',
                    'invoice.due_on',
                    'invoice.amount_cents',
                    'invoice.currency',
                    'invoice.status'
                )
                ->orderBy('invoice.issued_on', 'desc')
                ->get()
                ->map(function ($inv) use ($company) {
                    $inv->company_name = $company->name;
                    return $inv;
                });

            $unpaidInvoices = $unpaidInvoices->merge($rows);
        }

        // Вернём всё в шаблон
        return view('home', [
            'user'            => $authUser,
            'activeCompanies' => $activeCompanies,
            'unpaidInvoices'  => $unpaidInvoices,
        ]);
    }
}
