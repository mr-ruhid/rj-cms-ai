<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FreeAiService
{
    public function ask(string $question): array
    {
        try {
            $prompt = "You are a helpful assistant for a CMS called RJ CMS. Provide a short, helpful answer to this question: " . $question;

            $url = "https://text.pollinations.ai/" . urlencode($prompt);

            $response = Http::timeout(30)->get($url);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'answer' => 'Xəta: Pulsuz AI serverinə qoşulmaq mümkün olmadı.',
                ];
            }

            $answer = $response->body();

            return [
                'success' => true,
                'answer' => trim($answer),
            ];

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'answer' => 'Şəbəkə xətası: ' . $e->getMessage(),
            ];
        }
    }
}