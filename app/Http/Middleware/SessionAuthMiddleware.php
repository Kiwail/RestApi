<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('auth_user')) {
            return redirect()
                ->route('login')
                ->with('error', 'Сначала войдите в систему.');
        }

        return $next($request);
    }
}
