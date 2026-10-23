<?php

namespace RjCommerce\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCommerceApi
{
    public function handle(Request $request, Closure $next): Response
    {
        if (rj_get_option('commerce_api_enabled') !== '1') {
            return response()->json([
                'success' => false,
                'message' => 'API is disabled.'
            ], 403);
        }

        $publicKey = $request->header('X-Public-Key');
        $secretKey = $request->bearerToken();

        $validPublicKey = rj_get_option('commerce_api_public_key');
        $validSecretKey = rj_get_option('commerce_api_secret_key');

        if (empty($publicKey) || empty($secretKey) || $publicKey !== $validPublicKey || $secretKey !== $validSecretKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid API credentials.'
            ], 401);
        }

        return $next($request);
    }
}
