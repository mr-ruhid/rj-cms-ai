<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectRtlDirection
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof \Illuminate\Http\Response || $response instanceof \Symfony\Component\HttpFoundation\Response) {
            $contentType = $response->headers->get('Content-Type');

            if ($contentType && str_contains($contentType, 'text/html')) {
                $content = $response->getContent();

                if (str_contains($content, '<html')) {
                    $dir = rj_dir();
                    
                    $content = preg_replace_callback('/<html\b[^>]*>/i', function ($matches) use ($dir) {
                        $htmlTag = $matches[0];
                        $htmlTag = preg_replace('/\s+dir=(["\'])(?:ltr|rtl)\1/i', '', $htmlTag);
                        return str_replace('<html', '<html dir="' . $dir . '"', $htmlTag);
                    }, $content, 1);

                    $response->setContent($content);
                }
            }
        }

        return $response;
    }
}