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
     * Вернёт список заявок этой компании (обычно pending).
     */
    public function index(Request $request)
    {
        $company = $request->attributes->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company not resolved'], 500);
        }

        // заявки этой компании в resti_core
        $requests = DB::connection('master')
            ->table('company_join_request')
            ->where('company_id', $company->id)
            ->whereIn('status', ['pending', 'approved', 'rejected']) // можно оставить только pending
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($requests);
    }

    /**
     * POST /api/join-requests/{id}/approve
     * Одобряет заявку:
     *  - помечает company_join_request как approved
     *  - создаёт/обновляет запись в auth_user_company со статусом active
     *  - создаёт клиента в tenant-базе (client)
     */
    public function approve(Request $request, string $id)
    {
        $company = $request->attributes->get('company');
        if (!$company) {
            return response()->json(['message' => 'Company not resolved'], 500);
        }

        // можно добавить проверку, что это админ, если у тебя есть роли

        // находим заявку в master
        $join = DB::connection('master')
            ->table('company_join_request')
            ->where('id', $id)
            ->where('company_id', $company->id)
            ->where('status', 'pending')
            ->first();

        if (!$join) {
            return response()->json(['message' => 'Заявка не найдена или уже обработана'], 404);
        }

        // забираем данные пользователя из resti_auth
        $authUser = DB::connection('auth')
            ->table('auth_user')
            ->where('id', $join->auth_user_id)
            ->first();

        if (!$authUser) {
            return response()->json(['message' => 'Пользователь заявки не найден'], 404);
        }

        // тут у нас три базы: master, auth, tenant(mysql).
        // Сделаем по шагам, без одной общей транзакции (иначе нужно распределённую).

        // 1) Обновляем заявку в master
        DB::connection('master')
            ->table('company_join_request')
            ->where('id', $join->id)
            ->update([
                'status'       => 'approved',
                'processed_at' => now(),
                'processed_by' => null, // сюда можно позже писать id админа
            ]);

        // 2) Обновляем/создаём связь user↔company в resti_auth
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

        // 3) Создаём клиента в tenant-базе компании
        // Предполагается, что TenantAuth уже сделал switchTenant,
        // и сейчас connection('mysql') указывает на tenant_DB этой компании.

        // проверим, есть ли уже клиент с таким auth_user_id, чтобы не дублировать
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
            'message' => 'Заявка одобрена',
            'request_id' => $join->id,
        ]);
    }

    /**
     * (Опционально) отклонить заявку.
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
            return response()->json(['message' => 'Заявка не найдена или уже обработана'], 404);
        }

        DB::connection('master')
            ->table('company_join_request')
            ->where('id', $join->id)
            ->update([
                'status'       => 'rejected',
                'processed_at' => now(),
                'processed_by' => null,
            ]);

        // auth_user_company трогать не обязательно, можно вообще не создавать запись,
        // если заявка отклонена

        return response()->json([
            'message' => 'Заявка отклонена',
            'request_id' => $join->id,
        ]);
    }
}
