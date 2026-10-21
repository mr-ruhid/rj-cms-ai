<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FreeAiService
{
    public function ask(string $question, string $context = ''): array
    {
        try {
            $systemPrompt = "You are the official AI assistant for RJ CMS (and RJ Nexus). Answer the user's question clearly, accurately, and concisely based ONLY on the provided documentation context. If the answer is not in the context, politely say you don't know and suggest checking the RJ About section. Respond in the same language as the user's question.";

            $prompt = $systemPrompt . "\n\n--- DOCUMENTATION CONTEXT ---\n" . mb_substr($context, 0, 4000) . "\n\n--- USER QUESTION ---\n" . $question;

            $url = "https://text.pollinations.ai/" . urlencode($prompt);

            $response = Http::timeout(45)->get($url);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'answer' => __('Error: Could not connect to the free AI server.'),
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
                'answer' => __('Network error: :msg', ['msg' => $e->getMessage()]),
            ];
        }
    }
}
