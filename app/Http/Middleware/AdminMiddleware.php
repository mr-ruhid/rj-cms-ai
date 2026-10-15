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

        if (!in_array($role, ['admin', 'super-admin'], true)) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return $next($request);
    }
}