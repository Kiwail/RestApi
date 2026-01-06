<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanyJoinRequestController extends Controller
{
    /**
     * GET /api/join-requests
     * Atgriezīs šī uzņēmuma pieteikumu sarakstu (parasti pending).
     */
    public function index(Request $request)
    {
        $company = $request->attributes->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company not resolved'], 500);
        }

        // šī uzņēmuma pieteikumi resti_core
        $requests = DB::connection('master')
            ->table('company_join_request')
            ->where('company_id', $company->id)
            ->whereIn('status', ['pending', 'approved', 'rejected']) // var atstāt tikai pending
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($requests);
    }

    /**
     * POST /api/join-requests/{id}/approve
     * Apstiprina pieteikumu:
     *  - atzīmē company_join_request kā approved
     *  - izveido/atjauno ierakstu auth_user_company ar statusu active
     *  - izveido klientu tenant datubāzē (client)
     */
    public function approve(Request $request, string $id)
    {
        $company = $request->attributes->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company not resolved'], 500);
        }

        // var pievienot pārbaudi, ka tas ir admins, ja tev ir lomas

        // atrodam pieteikumu master DB
        $join = DB::connection('master')
            ->table('company_join_request')
            ->where('id', $id)
            ->where('company_id', $company->id)
            ->where('status', 'pending')
            ->first();

        if (!$join) {
            return response()->json(['message' => 'Pieteikums nav atrasts vai jau ir apstrādāts'], 404);
        }

        // paņemam lietotāja datus no resti_auth
        $authUser = DB::connection('auth')
            ->table('auth_user')
            ->where('id', $join->auth_user_id)
            ->first();

        if (!$authUser) {
            return response()->json(['message' => 'Pieteikuma lietotājs nav atrasts'], 404);
        }

        // te mums ir trīs datubāzes: master, auth, tenant(mysql).
        // Darīsim pa soļiem, bez vienas kopīgas transakcijas (citādi vajag sadalītu transakciju).

        // 1) Atjaunojam pieteikumu master DB
        DB::connection('master')
            ->table('company_join_request')
            ->where('id', $join->id)
            ->update([
                'status'       => 'approved',
                'processed_at' => now(),
                'processed_by' => null, // te vēlāk var rakstīt admina id
            ]);

        // 2) Atjaunojam/izveidojam user↔company saiti resti_auth
        DB::connection('auth')
            ->table('auth_user_company')
            ->updateOrInsert(
                [
                    'user_id'    => $join->auth_user_id,
                    'company_id' => $company->id,
                ],
                [
                    'status'     => 'active',
                    'created_at' => now(),
                ]
            );

        // 3) Izveidojam klientu uzņēmuma tenant datubāzē
        // Pieņemam, ka TenantAuth jau ir veicis switchTenant,
        // un tagad connection('mysql') norāda uz šī uzņēmuma tenant_DB.

        // pārbaudīsim, vai klients ar tādu auth_user_id jau eksistē, lai nedublētu
        $existingClient = DB::connection('mysql')
            ->table('client')
            ->where('auth_user_id', $join->auth_user_id)
            ->first();

        if (!$existingClient) {
            DB::connection('mysql')
                ->table('client')
                ->insert([
                    'id'           => (string) Str::uuid(),
                    'auth_user_id' => $join->auth_user_id,
                    'name'         => $authUser->username ?? $authUser->email,
                    'email'        => $authUser->email,
                    'phone'        => $authUser->phone ?? null,
                    'created_at'   => now(),
                ]);
        }

        return response()->json([
            'message' => 'Pieteikums apstiprināts',
            'request_id' => $join->id,
        ]);
    }

    /**
     * (Pēc izvēles) noraidīt pieteikumu.
     */
    public function reject(Request $request, string $id)
    {
        $company = $request->attributes->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company not resolved'], 500);
        }

        $join = DB::connection('master')
            ->table('company_join_request')
            ->where('id', $id)
            ->where('company_id', $company->id)
            ->where('status', 'pending')
            ->first();

        if (!$join) {
            return response()->json(['message' => 'Pieteikums nav atrasts vai jau ir apstrādāts'], 404);
        }

        DB::connection('master')
            ->table('company_join_request')
            ->where('id', $join->id)
            ->update([
                'status'       => 'rejected',
                'processed_at' => now(),
                'processed_by' => null,
            ]);

        // auth_user_company nav obligāti aiztikt — var pat vispār neveidot ierakstu,
        // ja pieteikums ir noraidīts

        return response()->json([
            'message' => 'Pieteikums noraidīts',
            'request_id' => $join->id,
        ]);
    }
}
