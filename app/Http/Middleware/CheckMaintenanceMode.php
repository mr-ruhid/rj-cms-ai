<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (rj_get_option('maintenance_mode') === '1') {
            if (auth()->check() && auth()->user()->isAdmin()) {
                return $next($request);
            }

            if ($request->is('admin') || $request->is('admin/*') || $request->is('login')) {
                return $next($request);
            }

            $message = rj_get_option('maintenance_message', __('We are performing scheduled maintenance. Please check back soon.'));
            $retry = (int) rj_get_option('maintenance_retry', 3600);

            $headers = [];
            if ($retry > 0) {
                $headers['Retry-After'] = $retry;
            }

            abort(503, $message, $headers);
        }

        return $next($request);
    }
}