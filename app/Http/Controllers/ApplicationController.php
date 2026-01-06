<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function showForm(Request $request)
    {
        // pārbaudām autorizāciju
        $authUser = $request->session()->get('auth_user');
        if (!$authUser) {
            return redirect()->route('login');
        }

        // iegūstam aktīvo uzņēmumu sarakstu no resti_core (master/core pieslēgums)
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
        // pārbaudām autorizāciju
        $authUser = $request->session()->get('auth_user');
        if (!$authUser) {
            return redirect()->route('login');
        }

        // validācija
        $data = $request->validate([
            'company_id' => 'required|string',
            'message'    => 'nullable|string|max:500',
        ]);

        // pārbaudām, ka uzņēmums eksistē un ir aktīvs
        $company = DB::connection('master')
            ->table('company')
            ->where('id', $data['company_id'])
            ->where('status', 'active')
            ->first();

        if (!$company) {
            return back()->with('error', 'Uzņēmums nav atrasts vai ir deaktivizēts');
        }

        // ---- AIZSARDZĪBA PRET SPAMU: pārbaudām esošu pieteikumu ----
        $existing = DB::connection('master')
            ->table('company_join_request')
            ->where('company_id', $company->id)
            ->where('auth_user_id', $authUser['id'])
            ->first();

        if ($existing) {
            if ($existing->status === 'pending') {
                return back()->with('error', 'Pieteikums jau ir nosūtīts un gaida apstiprinājumu');
            }

            if ($existing->status === 'approved') {
                return back()->with('error', 'Jūs jau esat pieņemts šajā uzņēmumā');
            }

            // ja rejected — atļaujam atkārtotu nosūtīšanu (atjaunojam ierakstu)
            DB::connection('master')
                ->table('company_join_request')
                ->where('id', $existing->id)
                ->update([
                    'status'       => 'pending',
                    'message'      => $data['message'] ?? null,
                    'processed_at' => null,
                    'processed_by' => null,
                    'created_at'   => now(),
                ]);

            // saite user–company -> pending
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

            return back()->with('success', 'Pieteikums ir nosūtīts atkārtoti un gaida izskatīšanu');
        }

        // ---- ja pieteikuma nav — izveidojam jaunu ----
        try {
            DB::connection('master')
                ->table('company_join_request')
                ->insert([
                    'id'           => (string) Str::uuid(),
                    'company_id'   => $company->id,
                    'auth_user_id' => $authUser['id'],
                    'status'       => 'pending',
                    'message'      => $data['message'] ?? null,
                    'created_at'   => now(),
                ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // ja vienlaikus atnāk divi pieprasījumi (race condition) — noķersim UNIQUE
            if (($e->errorInfo[1] ?? null) == 1062) {
                return back()->with('error', 'Pieteikums jau ir nosūtīts un gaida apstiprinājumu');
            }
            throw $e;
        }

        // (pēc izvēles) atzīmējam saiti user–company kā pending tabulā auth_user_company
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

        return back()->with('success', 'Pieteikums ir nosūtīts un gaida izskatīšanu');
    }
}
