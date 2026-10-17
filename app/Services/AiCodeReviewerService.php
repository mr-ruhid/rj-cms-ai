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