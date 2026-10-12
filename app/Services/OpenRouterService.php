<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    protected ?string $apiKey;
    protected string $endpoint;
    protected int $timeout;

    public function __construct()
    {
        $this->apiKey = rj_get_option('ai_openrouter_api_key');
        $this->endpoint = rtrim(rj_get_option('ai_openrouter_endpoint', 'https://openrouter.ai/api/v1'), '/');
        $this->timeout = (int) rj_get_option('ai_timeout', 120);
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function chat(array $messages, ?string $model = null, array $options = []): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'OpenRouter API key is not configured.',
            ];
        }

        $model = $model ?: rj_get_option('ai_default_model', 'google/gemini-2.0-flash-exp:free');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'HTTP-Referer' => config('app.url'),
                'X-Title' => rj_get_option('site_name', config('app.name')),
            ])
                ->timeout($this->timeout)
                ->acceptJson()
                ->post($this->endpoint . '/chat/completions', array_merge([
                    'model' => $model,
                    'messages' => $messages,
                ], $options));

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => $response->json('error.message') ?? 'HTTP ' . $response->status(),
                    'status' => $response->status(),
                ];
            }

            $data = $response->json();

            return [
                'success' => true,
                'content' => $data['choices'][0]['message']['content'] ?? '',
                'usage' => $data['usage'] ?? [],
                'model' => $data['model'] ?? $model,
                'raw' => $data,
            ];
        } catch (\Throwable $e) {
            Log::error('OpenRouter chat failed', [
                'message' => $e->getMessage(),
                'model' => $model,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function models(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->acceptJson()
                ->get($this->endpoint . '/models');

            if (!$response->successful()) {
                return [];
            }

            $data = $response->json();

            return $data['data'] ?? [];
        } catch (\Throwable $e) {
            Log::error('OpenRouter models fetch failed', ['message' => $e->getMessage()]);

            return [];
        }
    }

    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'API key is not set.',
            ];
        }

        $result = $this->chat([
            ['role' => 'user', 'content' => 'Reply with just the word: OK'],
        ], null, ['max_tokens' => 10]);

        if (!$result['success']) {
            return $result;
        }

        return [
            'success' => true,
            'message' => 'Connection OK. Model: ' . $result['model'],
        ];
    }
}
