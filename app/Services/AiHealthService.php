<?php

namespace App\Services;

class AiHealthService
{
    protected const SEVERITIES = ['critical', 'high', 'medium', 'low'];
    protected const OVERALL = ['excellent', 'good', 'fair', 'poor', 'critical'];
    protected const CATEGORIES = ['environment', 'storage', 'database', 'security', 'performance', 'content', 'logs', 'integrations'];

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
        return (string) rj_get_option('ai_default_model', 'openai/gpt-4o-mini');
    }

    public function analyze(array $checks, array $summary): array
    {
        if (!$this->enabled()) {
            return [
                'success' => false,
                'error' => __('AI is not configured. Set an OpenRouter API key in Settings → AI.'),
            ];
        }

        $issues = array_values(array_filter($checks, fn($c) => $c['status'] !== 'good'));

        if (empty($issues)) {
            return [
                'success' => true,
                'data' => [
                    'overall' => 'excellent',
                    'score' => $summary['score'],
                    'headline' => __('Your site is in excellent health. No issues detected.'),
                    'summary' => __('All :n checks passed. Continue regular monitoring and backups.', ['n' => $summary['total']]),
                    'priorities' => [],
                    'quick_wins' => [],
                    'positives' => $this->positives($checks),
                    'model' => null,
                ],
            ];
        }

        $payload = $this->buildPayload($checks, $issues, $summary);
        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ['role' => 'user', 'content' => "Analyze this RJ CMS health report and return JSON:\n\n"
                . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)],
        ];

        $content = '';

        try {
            // Parse alınmasa bir dəfə təkrar cəhd edilir
            for ($attempt = 1; $attempt <= 2; $attempt++) {
                $response = $this->openRouter->chat($messages, [
                    'model' => $this->model(),
                    'temperature' => $attempt === 1 ? 0.3 : 0.1,
                    'max_tokens' => 3000,
                ]);

                $content = $this->extractContent($response);
                $parsed = $this->parseJson($content);

                if ($parsed !== null) {
                    return [
                        'success' => true,
                        'data' => $this->normalize($parsed, $checks, $summary),
                    ];
                }
            }
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => __('AI request failed: :msg', ['msg' => $e->getMessage()]),
            ];
        }

        return [
            'success' => false,
            'error' => __('AI response could not be parsed. Try again.'),
            'raw' => $content,
        ];
    }

    protected function systemPrompt(): string
    {
        $locale = app()->getLocale();

        return 'You are a senior DevOps and Laravel systems engineer. Analyze the given site health report. '
            . 'Return ONLY valid JSON, no markdown, no code fences, no text outside JSON. '
            . 'The JSON schema must be exactly: '
            . '{"overall":"excellent|good|fair|poor|critical","headline":"short one-line summary","summary":"2-3 sentence technical summary",'
            . '"priorities":[{"severity":"critical|high|medium|low","title":"short title","why":"why it matters (1-2 sentences)",'
            . '"action":"concrete steps to fix, may include shell or config commands, one step per line",'
            . '"category":"environment|storage|database|security|performance|content|logs|integrations","affected_check":"check id"}],'
            . '"quick_wins":["short actionable tip 1","tip 2"]}. '
            . 'Rules: order priorities from most to least urgent; maximum 8 priorities and 5 quick wins; '
            . 'prioritize critical checks over warnings; if there are no critical issues, overall must be good or excellent unless several warnings stack up (then fair). '
            . 'Actions must be specific, safe and non-destructive (never suggest deleting data or disabling security features). '
            . 'Keep JSON keys and enum values in English exactly as in the schema, but write all human-readable text values in the language with locale code "' . $locale . '". '
            . 'Command names, file paths and env variables stay unchanged.';
    }

    protected function buildPayload(array $checks, array $issues, array $summary): array
    {
        return [
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
                'message' => $this->scrub((string) $c['message']),
                'value' => is_scalar($c['value']) ? $this->scrub((string) $c['value']) : $c['value'],
                'recommendation' => $c['recommendation'],
            ], $issues),
            'passing' => $this->positives($checks),
            'environment' => [
                'php' => PHP_VERSION,
                'laravel' => app()->version(),
                'env' => app()->environment(),
            ],
        ];
    }

    /**
     * Server yollarını AI-yə göndərmirik.
     */
    protected function scrub(string $text): string
    {
        return str_replace([base_path(), storage_path()], ['[app]', '[app]/storage'], $text);
    }

    protected function normalize(array $parsed, array $checks, array $summary): array
    {
        $overall = strtolower((string) ($parsed['overall'] ?? 'fair'));
        if (!in_array($overall, self::OVERALL, true)) {
            $overall = 'fair';
        }

        $priorities = [];
        foreach ((array) ($parsed['priorities'] ?? []) as $p) {
            if (!is_array($p) || empty($p['title'])) {
                continue;
            }

            $severity = strtolower((string) ($p['severity'] ?? 'medium'));
            $category = strtolower((string) ($p['category'] ?? ''));

            $priorities[] = [
                'severity' => in_array($severity, self::SEVERITIES, true) ? $severity : 'medium',
                'title' => $this->text($p['title']),
                'why' => $this->text($p['why'] ?? ''),
                'action' => $this->text($p['action'] ?? ''),
                'category' => in_array($category, self::CATEGORIES, true) ? $category : '',
                'affected_check' => $this->text($p['affected_check'] ?? ''),
            ];

            if (count($priorities) >= 8) {
                break;
            }
        }

        $quickWins = array_values(array_filter(array_map(
            fn($w) => $this->text($w),
            array_slice((array) ($parsed['quick_wins'] ?? []), 0, 5)
        )));

        return [
            'overall' => $overall,
            'score' => $summary['score'],
            'headline' => $this->text($parsed['headline'] ?? ''),
            'summary' => $this->text($parsed['summary'] ?? ''),
            'priorities' => $priorities,
            'quick_wins' => $quickWins,
            'positives' => $this->positives($checks),
            'model' => $this->model(),
        ];
    }

    protected function text($value): string
    {
        if (is_array($value)) {
            $value = implode("\n", array_map(fn($v) => is_scalar($v) ? (string) $v : '', $value));
        }

        return trim((string) $value);
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
            $content = $response['choices'][0]['message']['content'];

            if (is_array($content)) {
                $content = implode('', array_map(fn($part) => is_array($part) ? ($part['text'] ?? '') : (string) $part, $content));
            }

            return (string) $content;
        }

        if (isset($response['content'])) {
            return (string) (is_array($response['content']) ? json_encode($response['content']) : $response['content']);
        }

        if (isset($response['message'])) {
            return (string) (is_array($response['message']) ? json_encode($response['message']) : $response['message']);
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