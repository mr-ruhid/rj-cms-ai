<?php

namespace App\Services;

class AiHealthService
{
    protected OpenRouterService $openRouter;

    public function __construct(OpenRouterService $openRouter)
    {
        $this->openRouter = $openRouter;
    }

    public function enabled(): bool
    {
        try {
            return (bool) $this->openRouter->enabled();
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function model(): string
    {
        return rj_get_option('ai_default_model', 'openai/gpt-4o-mini');
    }

    public function analyze(array $checks, array $summary): array
    {
        if (!$this->enabled()) {
            return [
                'success' => false,
                'error' => __('AI is not configured. Set an OpenRouter API key in Settings → AI.'),
            ];
        }

        $filtered = array_values(array_filter($checks, fn($c) => $c['status'] !== 'good'));

        if (empty($filtered)) {
            return [
                'success' => true,
                'data' => [
                    'overall' => 'excellent',
                    'score' => $summary['score'],
                    'headline' => __('Your site is in excellent health. No issues detected.'),
                    'summary' => __('All :n checks passed. Continue regular monitoring and backups.', ['n' => $summary['total']]),
                    'priorities' => [],
                    'positives' => $this->positives($checks),
                ],
            ];
        }

        $payload = [
            'score' => $summary['score'],
            'counts' => [
                'total' => $summary['total'],
                'good' => $summary['good'],
                'warning' => $summary['warning'],
                'critical' => $summary['critical'],
            ],
            'issues' => array_map(fn($c) => [
                'id' => $c['id'],
                'category' => $c['category'],
                'label' => $c['label'],
                'status' => $c['status'],
                'message' => is_string($c['message']) ? $c['message'] : (string) $c['message'],
                'value' => $c['value'],
                'recommendation' => $c['recommendation'],
            ], $filtered),
            'passing' => $this->positives($checks),
            'environment' => [
                'php' => PHP_VERSION,
                'laravel' => app()->version(),
                'env' => app()->environment(),
                'url' => config('app.url'),
            ],
        ];

        $systemPrompt = 'You are a senior DevOps and Laravel systems engineer. Analyze the given site health report. Return ONLY valid JSON, no markdown, no explanation outside JSON. The JSON schema must be exactly: '
            . '{"overall":"excellent|good|fair|poor|critical","headline":"short one-line summary","summary":"2-3 sentence technical summary","priorities":[{"severity":"critical|high|medium|low","title":"short title","why":"why it matters (1-2 sentences)","action":"concrete steps to fix, may include shell or config commands","category":"environment|storage|database|security|performance|content|logs|integrations","affected_check":"check id"}],"quick_wins":["short actionable tip 1","tip 2"]}. '
            . 'Prioritize critical over warnings. If no critical issues, overall should be good or excellent. Actions must be specific and safe.';

        $userPrompt = "Analyze this RJ CMS health report and return JSON:\n\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        try {
            $response = $this->openRouter->chat([
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ], [
                'model' => $this->model(),
                'temperature' => 0.3,
                'max_tokens' => 2000,
            ]);

            $content = $this->extractContent($response);
            $parsed = $this->parseJson($content);

            if ($parsed === null) {
                return [
                    'success' => false,
                    'error' => __('AI response could not be parsed. Try again.'),
                    'raw' => $content,
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'overall' => $parsed['overall'] ?? 'fair',
                    'score' => $summary['score'],
                    'headline' => $parsed['headline'] ?? '',
                    'summary' => $parsed['summary'] ?? '',
                    'priorities' => $parsed['priorities'] ?? [],
                    'quick_wins' => $parsed['quick_wins'] ?? [],
                    'positives' => $this->positives($checks),
                    'model' => $this->model(),
                ],
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => __('AI request failed: :msg', ['msg' => $e->getMessage()]),
            ];
        }
    }

    protected function positives(array $checks): array
    {
        return array_values(array_map(
            fn($c) => $c['label'] . ' — ' . (is_string($c['message']) ? $c['message'] : ''),
            array_filter($checks, fn($c) => $c['status'] === 'good')
        ));
    }

    protected function extractContent(array $response): string
    {
        if (isset($response['choices'][0]['message']['content'])) {
            return (string) $response['choices'][0]['message']['content'];
        }

        if (isset($response['content'])) {
            return (string) $response['content'];
        }

        if (isset($response['message'])) {
            return (string) $response['message'];
        }

        return (string) json_encode($response);
    }

    protected function parseJson(string $content): ?array
    {
        $content = trim($content);

        if (str_starts_with($content, '```')) {
            $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);
        }

        $decoded = json_decode($content, true);

        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{.*\}/s', $content, $match)) {
            $decoded = json_decode($match[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}