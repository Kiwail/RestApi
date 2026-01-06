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

        // 1) Atrodam uzņēmumu master DB (resti_core)
        $company = DB::connection('master')
            ->table('company')
            ->where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if (!$company) {
            abort(404, 'Company not found');
        }

        // 2) Pārbaudām, ka lietotājs tiešām ir šī uzņēmuma "active" klients
        // SVARĪGI: auth_user_company atrodas resti_auth, tāpēc izmantojam connection('auth')
        $isMember = DB::connection('auth')
            ->table('auth_user_company')
            ->where('user_id', $authUser['id'])
            ->where('company_id', $company->id)
            ->where('status', 'active')
            ->exists();

        if (!$isMember) {
            abort(403, 'You are not an active client of this company');
        }

        // 3) Paņemam tenant db_name pēc company_id
        $tenant = DB::connection('master')
            ->table('tenant_registry')
            ->where('company_id', $company->id)
            ->first();

        if (!$tenant) {
            abort(500, 'Tenant DB not found in registry');
        }

        // 4) Pārslēdzam mysql uz tenant datubāzi
        $this->switchTenant($tenant->db_name);

        // 5) Meklējam client tenant datubāzē pēc auth_user_id
        $client = DB::connection('mysql')
            ->table('client')
            ->where('auth_user_id', $authUser['id'])
            ->first();

        if (!$client) {
            // lietotājs ir aktīvs, bet client ieraksts vēl nav izveidots tenant DB
            // (tas nozīmē, ka approve vēl nav izpildījis client insert)
            return view('company.show', [
                'user' => $authUser,
                'company' => $company,
                'dbName' => $tenant->db_name,
                'client' => null,
                'unpaidInvoices' => collect(),
                'contracts' => collect(),
            ]);
        }

        // 6) Iegūstam uzņēmuma datus šim klientam
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
