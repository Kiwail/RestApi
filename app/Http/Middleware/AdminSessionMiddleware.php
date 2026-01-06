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

        // Nav pieteicies — pāradresējam uz pieteikšanos
        if (!$authUser) {
            return redirect()
                ->route('login')
                ->with('error', 'Vispirms piesakieties sistēmā.');
        }

        // Pieteicies, bet nav admins
        if (($authUser['role'] ?? 'user') !== 'admin') {
            return redirect()
                ->route('home')
                ->with('error', 'Jums nav piekļuves administratora panelim.');
        }

        return $next($request);
    }
}
