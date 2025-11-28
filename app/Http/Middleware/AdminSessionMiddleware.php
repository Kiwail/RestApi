<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $authUser = session('auth_user');

        // Не залогинен – на логин
        if (!$authUser) {
            return redirect()
                ->route('login')
                ->with('error', 'Сначала войдите в систему.');
        }

        // Залогинен, но не админ
        if (($authUser['role'] ?? 'user') !== 'admin') {
            return redirect()
                ->route('home')
                ->with('error', 'У вас нет доступа к админке.');
        }

        return $next($request);
    }
}
