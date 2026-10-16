<?php

namespace App\Services;

class AiSeoService
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

    public function generateMeta(string $title, string $content, string $locale = 'en', array $options = []): array
    {
        if (!$this->enabled()) {
            return ['success' => false, 'error' => __('AI is not configured.')];
        }

        $content = $this->cleanContent($content);

        if (empty($content) || empty($title)) {
            return ['success' => false, 'error' => __('Title or content is empty.')];
        }

        $language = $this->languageName($locale);
        $tone = $options['tone'] ?? 'professional';

        $prompt = <<<PROMPT
You are an SEO expert. Analyze the following content and generate SEO metadata.

Language: {$language}
Tone: {$tone}

Title: {$title}

Content:
{$content}

Return ONLY a valid JSON object with these exact keys (no markdown, no code fences, no explanation):
{
    "seo_title": "SEO optimized title, 50-60 characters, includes main keyword",
    "seo_description": "Meta description, 150-160 characters, compelling and clickable",
    "focus_keyword": "single most relevant keyword or short phrase (2-4 words)",
    "og_title": "Open Graph title for social sharing, max 60 characters",
    "og_description": "Open Graph description, 100-200 characters",
    "twitter_title": "Twitter title, max 70 characters",
    "twitter_description": "Twitter description, max 200 characters",
    "suggested_tags": ["tag1", "tag2", "tag3", "tag4", "tag5"]
}

Rules:
- All text must be in {$language}
- Do NOT include the JSON keys inside the string values
- Escape quotes properly
- seo_title must be under 60 chars
- seo_description must be under 160 chars
PROMPT;

        $result = $this->ai->chat([
            ['role' => 'system', 'content' => 'You are a professional SEO copywriter. Always respond with valid JSON only.'],
            ['role' => 'user', 'content' => $prompt],
        ], null, [
            'temperature' => 0.7,
            'max_tokens' => 800,
        ]);

        if (!$result['success']) {
            return $result;
        }

        $parsed = $this->parseJson($result['content']);

        if (!$parsed) {
            return [
                'success' => false,
                'error' => __('AI returned invalid JSON. Please try again.'),
                'raw' => $result['content'],
            ];
        }

        return [
            'success' => true,
            'data' => [
                'seo_title' => $this->limit($parsed['seo_title'] ?? '', 70),
                'seo_description' => $this->limit($parsed['seo_description'] ?? '', 180),
                'focus_keyword' => $this->limit($parsed['focus_keyword'] ?? '', 100),
                'og_title' => $this->limit($parsed['og_title'] ?? '', 70),
                'og_description' => $this->limit($parsed['og_description'] ?? '', 200),
                'twitter_title' => $this->limit($parsed['twitter_title'] ?? '', 70),
                'twitter_description' => $this->limit($parsed['twitter_description'] ?? '', 200),
                'suggested_tags' => is_array($parsed['suggested_tags'] ?? null)
                    ? array_slice(array_filter($parsed['suggested_tags']), 0, 5)
                    : [],
            ],
            'usage' => $result['usage'] ?? [],
            'model' => $result['model'] ?? null,
        ];
    }

    public function generateTitle(string $content, string $locale = 'en'): array
    {
        if (!$this->enabled()) {
            return ['success' => false, 'error' => __('AI is not configured.')];
        }

        $content = $this->cleanContent($content);
        $language = $this->languageName($locale);

        if (empty($content)) {
            return ['success' => false, 'error' => __('Content is empty.')];
        }

        $prompt = <<<PROMPT
Generate ONE SEO-friendly title in {$language} for the following content.

Content:
{$content}

Rules:
- 50-60 characters
- Include the most relevant keyword
- Compelling and clickable
- Return ONLY the title text, nothing else
PROMPT;

        $result = $this->ai->chat([
            ['role' => 'user', 'content' => $prompt],
        ], null, [
            'temperature' => 0.7,
            'max_tokens' => 100,
        ]);

        if (!$result['success']) {
            return $result;
        }

        return [
            'success' => true,
            'title' => $this->limit(trim($result['content']), 80),
        ];
    }

    public function generateDescription(string $title, string $content, string $locale = 'en'): array
    {
        if (!$this->enabled()) {
            return ['success' => false, 'error' => __('AI is not configured.')];
        }

        $content = $this->cleanContent($content);
        $language = $this->languageName($locale);

        if (empty($title) && empty($content)) {
            return ['success' => false, 'error' => __('Title or content is empty.')];
        }

        $prompt = <<<PROMPT
Write a compelling meta description in {$language} for the following content.

Title: {$title}

Content:
{$content}

Rules:
- 150-160 characters
- Include the most relevant keyword
- Action-oriented, clickable
- Return ONLY the description text, nothing else
PROMPT;

        $result = $this->ai->chat([
            ['role' => 'user', 'content' => $prompt],
        ], null, [
            'temperature' => 0.7,
            'max_tokens' => 150,
        ]);

        if (!$result['success']) {
            return $result;
        }

        return [
            'success' => true,
            'description' => $this->limit(trim($result['content']), 180),
        ];
    }

    public function suggestKeyword(string $title, string $content, string $locale = 'en'): array
    {
        if (!$this->enabled()) {
            return ['success' => false, 'error' => __('AI is not configured.')];
        }

        $content = $this->cleanContent($content);
        $language = $this->languageName($locale);

        $prompt = <<<PROMPT
Identify the single most important SEO focus keyword (2-4 words) for this content.

Language: {$language}

Title: {$title}

Content:
{$content}

Rules:
- Return ONLY the keyword phrase
- 2-4 words
- Lowercase
- No quotes, no punctuation
PROMPT;

        $result = $this->ai->chat([
            ['role' => 'user', 'content' => $prompt],
        ], null, [
            'temperature' => 0.5,
            'max_tokens' => 30,
        ]);

        if (!$result['success']) {
            return $result;
        }

        return [
            'success' => true,
            'keyword' => $this->limit(trim(str_replace(['"', "'", '.'], '', $result['content'])), 50),
        ];
    }

    protected function cleanContent(string $content): string
    {
        $content = strip_tags($content);
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);

        if (mb_strlen($content) > 3000) {
            $content = mb_substr($content, 0, 3000) . '...';
        }

        return $content;
    }

    protected function languageName(string $locale): string
    {
        $map = [
            'az' => 'Azerbaijani',
            'en' => 'English',
            'ru' => 'Russian',
            'tr' => 'Turkish',
            'fr' => 'French',
            'de' => 'German',
            'es' => 'Spanish',
            'it' => 'Italian',
            'ar' => 'Arabic',
            'zh' => 'Chinese',
            'ja' => 'Japanese',
        ];

        return $map[$locale] ?? 'English';
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

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return is_array($decoded) ? $decoded : null;
    }

    protected function limit(string $value, int $max): string
    {
        $value = trim($value);

        if (mb_strlen($value) <= $max) {
            return $value;
        }

        return mb_substr($value, 0, $max);
    }
}