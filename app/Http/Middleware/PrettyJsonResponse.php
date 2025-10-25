<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class PrettyJsonResponse
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            // Получаем данные
            $data = $response->getData(true);
            // Пересоздаём JSON с флагом JSON_PRETTY_PRINT
            $response->setData(json_decode(json_encode($data), true));
            $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return $response;
    }
}
