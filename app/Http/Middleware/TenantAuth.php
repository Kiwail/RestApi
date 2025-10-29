<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Company;
use App\Models\ApiKey;
use App\Models\TenantRegistry;

class TenantAuth
{
    public function handle($request, Closure $next)
    {
        // 1. достаём Basic Auth
        $authHeader = $request->header('Authorization');

        if (!$authHeader || stripos($authHeader, 'Basic ') !== 0) {
            return $this->unauthorized('Missing or invalid Authorization header');
        }

        $encoded = substr($authHeader, 6);
        $decoded = base64_decode($encoded);

        if (!$decoded || !str_contains($decoded, ':')) {
            return $this->unauthorized('Invalid Basic credentials format');
        }

        [$slug, $secret] = explode(':', $decoded, 2);

        if (!$slug || !$secret) {
            return $this->unauthorized('Invalid Basic credentials data');
        }

        // 2. ищем компанию по slug
        $company = Company::where('slug', $slug)->first();

        if (!$company) {
            return $this->unauthorized('Unknown company');
        }

        // статус компании
        if ($company->status !== 'active') {
            return response()->json([
                'message' => 'Company suspended',
            ], Response::HTTP_FORBIDDEN);
        }

        // 3. ищем подходящий ключ компании
        // Вариант А (простой): пробегаем по всем api_key этой компании и сверяем secret
        $apiKeys = ApiKey::where('company_id', $company->id)->get();

        $matchedKey = null;

        foreach ($apiKeys as $key) {
            if (Hash::check($secret, $key->hashed_secret)) {
                $matchedKey = $key;
                break;
            }
        }

        if (!$matchedKey) {
            return $this->unauthorized('Invalid API key');
        }

        // 4. получаем из tenant_registry правильную базу
        $tenantReg = TenantRegistry::where('company_id', $company->id)->first();

        if (!$tenantReg) {
            return response()->json([
                'message' => 'No tenant DB registered for this company',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $dbName = $tenantReg->db_name; // например "tenant_evorm"

        // 5. переключаем основное подключение mysql на базу арендатора
        $this->switchTenantDatabase($dbName);

        // 6. положим контекст в запрос (на будущее)
        $request->attributes->set('company', $company);
        $request->attributes->set('api_key', $matchedKey);
        $request->attributes->set('tenant_db', $dbName);

        // 7. пропускаем дальше — теперь контроллеры работают уже в tenant_* базе
        return $next($request);
    }

    protected function switchTenantDatabase(string $dbName): void
    {
        // Мы меняем runtime-конфиг коннекта "mysql"
        Config::set('database.connections.mysql.database', $dbName);

        // Сбрасываем текущее соединение и открываем новое
        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    protected function unauthorized(string $msg)
    {
        return response()->json([
            'message' => $msg,
        ], Response::HTTP_UNAUTHORIZED);
    }
}
