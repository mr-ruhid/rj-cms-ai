<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiBuilderService
{
    protected OpenRouterService $openRouter;

    public function __construct(OpenRouterService $openRouter)
    {
        $this->openRouter = $openRouter;
    }

    public function enabled(): bool
    {
        return trim((string) rj_get_option('ai_openrouter_api_key', '')) !== '';
    }

    public function generateText(string $prompt, array $options = []): array
    {
        $context = $options['context'] ?? '';
        $tone = $options['tone'] ?? 'professional';
        $length = $options['length'] ?? 'medium';
        $language = $options['language'] ?? app()->getLocale();

        $system = $this->textSystemPrompt($tone, $length, $language);
        $userMessage = $context !== ''
            ? "Context: {$context}\n\nTask: {$prompt}"
            : $prompt;

        $result = $this->chat($system, $userMessage, [
            'temperature' => 0.75,
            'max_tokens' => $this->lengthToTokens($length),
        ]);

        return [
            'success' => $result['success'],
            'text' => $result['text'],
            'error' => $result['error'] ?? null,
        ];
    }

    public function improveText(string $text, array $options = []): array
    {
        $mode = $options['mode'] ?? 'improve';
        $language = $options['language'] ?? app()->getLocale();

        $tasks = [
            'improve' => 'Improve the following text. Make it more clear, engaging, and grammatically correct. Keep the same meaning and approximate length. Return only the improved text.',
            'shorten' => 'Shorten the following text while keeping the core meaning. Return only the shortened text.',
            'expand' => 'Expand the following text with more details and examples. Return only the expanded text.',
            'fix' => 'Fix grammar, spelling, and punctuation. Keep the original tone. Return only the corrected text.',
            'formal' => 'Rewrite the following text in a formal, professional tone. Return only the rewritten text.',
            'casual' => 'Rewrite the following text in a friendly, casual tone. Return only the rewritten text.',
        ];

        $instruction = $tasks[$mode] ?? $tasks['improve'];

        $system = "You are a writing assistant. Language: {$language}. Do not add explanations, preambles, or quotation marks. Return only the result.";
        $userMessage = $instruction . "\n\n---\n" . $text . "\n---";

        $result = $this->chat($system, $userMessage, [
            'temperature' => 0.4,
            'max_tokens' => max(200, mb_strlen($text) * 3),
        ]);

        return [
            'success' => $result['success'],
            'text' => $result['text'],
            'error' => $result['error'] ?? null,
        ];
    }

    public function translateText(string $text, string $targetLocale, string $sourceLocale = ''): array
    {
        $languageName = rj_lang_name($targetLocale) ?: $targetLocale;

        $system = "You are a translator. Translate the given text to {$languageName}. Return only the translated text, no explanations, no quotes.";
        $userMessage = $text;

        $result = $this->chat($system, $userMessage, [
            'temperature' => 0.3,
            'max_tokens' => max(200, mb_strlen($text) * 3),
        ]);

        return [
            'success' => $result['success'],
            'text' => $result['text'],
            'error' => $result['error'] ?? null,
        ];
    }

    public function suggestSection(string $description, array $options = []): array
    {
        $language = $options['language'] ?? app()->getLocale();

        $system = "You are an expert web designer. Suggest a webpage section based on the user's description. "
            . "Return only valid JSON, no markdown, no explanations. "
            . "The JSON must have this shape: "
            . '{"blocks":[{"type":"container","settings":{"columns":"1"},"children":[{"type":"heading","settings":{"text":"...","level":"h2","align":"center"}},{"type":"text","settings":{"text":"...","align":"center"}}]}]}. '
            . "Allowed types: heading, text, image, container. Keep it simple (1-3 blocks). "
            . "Language for all text content: {$language}.";

        $userMessage = $description;

        $result = $this->chat($system, $userMessage, [
            'temperature' => 0.7,
            'max_tokens' => 1500,
        ]);

        if (!$result['success']) {
            return ['success' => false, 'blocks' => [], 'error' => $result['error'] ?? __('AI request failed.')];
        }

        $blocks = $this->parseBlocksJson($result['text']);

        return [
            'success' => !empty($blocks),
            'blocks' => $blocks,
            'error' => empty($blocks) ? __('Could not parse AI response.') : null,
        ];
    }

    protected function textSystemPrompt(string $tone, string $length, string $language): string
    {
        $toneMap = [
            'professional' => 'professional and business-like',
            'friendly' => 'friendly and warm',
            'casual' => 'casual and conversational',
            'formal' => 'formal and authoritative',
            'playful' => 'playful and energetic',
            'luxury' => 'luxurious and premium',
        ];

        $lengthMap = [
            'short' => 'Keep it very short, 1-2 sentences max.',
            'medium' => 'Keep it concise, 2-4 sentences.',
            'long' => 'Write a longer, detailed text, 4-6 sentences.',
        ];

        $toneText = $toneMap[$tone] ?? $toneMap['professional'];
        $lengthText = $lengthMap[$length] ?? $lengthMap['medium'];

        return "You are a professional copywriter. Write in a {$toneText} tone. {$lengthText} "
            . "Language: {$language}. Return only the final text, no explanations, no markdown, no quotes.";
    }

    protected function lengthToTokens(string $length): int
    {
        return match ($length) {
            'short' => 200,
            'long' => 1200,
            default => 500,
        };
    }

    protected function parseBlocksJson(string $raw): array
    {
        $raw = trim($raw);
        $raw = preg_replace('/^```(?:json)?\s*/i', '', $raw);
        $raw = preg_replace('/\s*```$/i', '', $raw);

        $data = json_decode($raw, true);

        if (!is_array($data) || empty($data['blocks']) || !is_array($data['blocks'])) {
            if (preg_match('/\{[\s\S]*\}/', $raw, $m)) {
                $data = json_decode($m[0], true);
            }
        }

        if (!is_array($data) || empty($data['blocks']) || !is_array($data['blocks'])) {
            return [];
        }

        $allowed = ['heading', 'text', 'image', 'container'];

        return $this->sanitizeBlocks($data['blocks'], $allowed);
    }

    protected function sanitizeBlocks(array $blocks, array $allowed): array
    {
        $out = [];

        foreach ($blocks as $block) {
            if (!is_array($block)) continue;

            $type = $block['type'] ?? '';

            if (!in_array($type, $allowed, true)) continue;

            $clean = [
                'id' => 'ai_' . uniqid(),
                'type' => $type,
                'settings' => is_array($block['settings'] ?? null) ? $block['settings'] : [],
                'children' => [],
            ];

            if (!empty($block['children']) && is_array($block['children'])) {
                $clean['children'] = $this->sanitizeBlocks($block['children'], $allowed);
            }

            $out[] = $clean;
        }

        return $out;
    }

    protected function chat(string $system, string $userMessage, array $options = []): array
    {
        if (!$this->enabled()) {
            return [
                'success' => false,
                'text' => '',
                'error' => __('AI is not configured. Add your OpenRouter API key in Settings → AI.'),
            ];
        }

        try {
            $payload = [
                'model' => rj_get_option('ai_default_model', 'openai/gpt-4o-mini'),
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $userMessage],
                ],
                'temperature' => $options['temperature'] ?? (float) rj_get_option('ai_default_temperature', 0.7),
                'max_tokens' => $options['max_tokens'] ?? (int) rj_get_option('ai_default_max_tokens', 1000),
            ];

            $endpoint = rtrim((string) rj_get_option('ai_openrouter_endpoint', 'https://openrouter.ai/api/v1'), '/') . '/chat/completions';
            $apiKey = trim((string) rj_get_option('ai_openrouter_api_key', ''));
            $timeout = (int) rj_get_option('ai_timeout', 60);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => url('/'),
                'X-Title' => rj_get_option('site_name', 'RJ CMS'),
            ])->timeout($timeout)->post($endpoint, $payload);

            if (!$response->successful()) {
                $msg = $response->json('error.message') ?: ('HTTP ' . $response->status());

                if (rj_get_option('ai_log_enabled', false)) {
                    Log::warning('AI request failed', ['status' => $response->status(), 'body' => $response->body()]);
                }

                return ['success' => false, 'text' => '', 'error' => $msg];
            }

            $data = $response->json();
            $text = $data['choices'][0]['message']['content'] ?? '';

            return [
                'success' => true,
                'text' => trim((string) $text),
                'error' => null,
            ];
        } catch (\Throwable $e) {
            if (rj_get_option('ai_log_enabled', false)) {
                Log::error('AI request exception: ' . $e->getMessage());
            }

            return ['success' => false, 'text' => '', 'error' => $e->getMessage()];
        }
    }
}