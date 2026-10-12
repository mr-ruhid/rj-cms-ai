<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale');

        if ($locale && $this->isValidLocale($locale)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    protected function isValidLocale(string $locale): bool
    {
        return (bool) preg_match('/^[a-z]{2}(_[A-Z]{2})?$/', $locale);
    }
}
