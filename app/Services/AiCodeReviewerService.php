<?php

namespace App\Services;

class AiCodeReviewerService
{
    protected OpenRouterService $ai;

    public function __construct(OpenRouterService $ai)
    {
        $this->ai = $ai;
    }

    public function enabled(): bool
    {
        return $this->ai->isConfigured();
    }

    public function review(string $code, string $filename): array
    {
        if (!$this->enabled()) {
            return ['success' => false, 'error' => __('AI is not configured.')];
        }

        $language = $this->detectLanguage($filename);

        if (mb_strlen($code) > 15000) {
            $code = mb_substr($code, 0, 15000) . "\n\n... [truncated]";
        }

        $prompt = <<<PROMPT
You are an expert code reviewer for a Laravel/Blade CMS.
Analyze the following file for bugs, syntax errors, performance issues, and security risks (like XSS, SQL injection, missing CSRF, path traversal).

File: {$filename}
Language: {$language}

Code:
```{$language}
{$code}
```

Return ONLY a valid JSON object with this exact structure:
{
  "score": 0-100,
  "status": "good" | "warning" | "danger",
  "summary": "One short sentence summarizing the code quality.",
  "issues": [
    {
      "severity": "error" | "warning" | "info",
      "line": "line number or 'global'",
      "message": "Description of the issue and how to fix it."
    }
  ]
}
PROMPT;

        $result = $this->ai->chat([
            ['role' => 'system', 'content' => 'You are a strict but fair code reviewer. Always respond with valid JSON only.'],
            ['role' => 'user', 'content' => $prompt],
        ], null, [
            'temperature' => 0.2,
            'max_tokens' => 1500,
        ]);

        if (!($result['success'] ?? false)) {
            return $result;
        }

        $parsed = $this->parseJson($result['content']);

        if (!$parsed) {
            return [
                'success' => false,
                'error' => __('Could not parse AI response.'),
                'raw' => $result['content'],
            ];
        }

        return [
            'success' => true,
            'data' => [
                'score' => max(0, min(100, (int) ($parsed['score'] ?? 0))),
                'status' => in_array($parsed['status'] ?? '', ['good', 'warning', 'danger'], true)
                    ? $parsed['status']
                    : 'warning',
                'summary' => mb_substr((string) ($parsed['summary'] ?? ''), 0, 200),
                'issues' => is_array($parsed['issues'] ?? null)
                    ? array_slice($parsed['issues'], 0, 20)
                    : [],
            ],
        ];
    }

    protected function detectLanguage(string $filename): string
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return match ($ext) {
            'php' => 'php',
            'js' => 'javascript',
            'css' => 'css',
            'json' => 'json',
            'html' => 'html',
            'md' => 'markdown',
            'svg' => 'xml',
            default => 'text',
        };
    }

    protected function parseJson(string $raw): ?array
    {
        $raw = trim($raw);

        if (preg_match('/```(?:json)?\s*(.+?)\s*```/s', $raw, $m)) {
            $raw = trim($m[1]);
        }

        $start = strpos($raw, '{');
        $end = strrpos($raw, '}');

        if ($start === false || $end === false) {
            return null;
        }

        $json = substr($raw, $start, $end - $start + 1);
        $decoded = json_decode($json, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }
}