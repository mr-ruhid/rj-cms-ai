<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        $role = $user->role ?? null;

        // Həm 'super_admin', həm 'super-admin', həm də 'admin' rollarına icazə verilir
        if (!in_array($role, ['admin', 'super-admin', 'super_admin'], true)) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return $next($request);
    }
}