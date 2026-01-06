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
            // Iegūstam datus
            $data = $response->getData(true);
            // Izveidojam JSON no jauna ar karogu JSON_PRETTY_PRINT
            $response->setData(json_decode(json_encode($data), true));
            $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return $response;
    }
}
