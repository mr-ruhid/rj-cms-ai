<?php

namespace App\Services;

use App\Models\Comment;

class AiCommentService
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

    public function analyze(Comment $comment): array
    {
        if (!$this->enabled()) {
            return ['success' => false, 'message' => __('AI is not configured.')];
        }

        $content = trim((string) $comment->content);

        if (mb_strlen($content) < 3) {
            return ['success' => false, 'message' => __('Comment is too short to analyze.')];
        }

        $authorInfo = [];
        if ($comment->author_name) $authorInfo[] = 'Name: ' . $comment->author_name;
        if ($comment->author_email) $authorInfo[] = 'Email: ' . $comment->author_email;
        if ($comment->author_url) $authorInfo[] = 'URL: ' . $comment->author_url;
        if ($comment->author_ip) $authorInfo[] = 'IP: ' . $comment->author_ip;

        $authorBlock = implode("\n", $authorInfo);

        $prompt = <<<PROMPT
Analyze this blog comment for spam, sentiment, and quality. Respond with valid JSON only.

Comment:
"""
{$content}
"""

Author info:
{$authorBlock}

Return a JSON object with this exact structure (no markdown, no explanation):
{
    "spam_score": 0-100 (0 = definitely legitimate, 100 = definitely spam),
    "sentiment": "positive" | "neutral" | "negative",
    "toxicity": 0-100 (0 = clean, 100 = very toxic),
    "is_spam": true | false,
    "reason": "One short sentence explaining your spam verdict (max 120 chars)",
    "flags": ["list", "of", "short", "warning", "flags"] (each max 30 chars, empty array if none)
}

Rules:
- SPAM indicators: promotional links, unrelated content, generic praise, repeated words, suspicious URLs, keywords like "buy now", "click here", "casino", "viagra", "crypto", etc.
- Legitimate comments: on-topic, specific, personal, question-like, constructive
- Sentiment is the emotion of the commenter toward the post
- Toxicity measures rudeness, insults, aggression
- flags may include: "link", "promotional", "off-topic", "toxic", "generic", "suspicious-email", "repeat"
PROMPT;

        $result = $this->ai->chat([
            ['role' => 'system', 'content' => 'You are a strict but fair comment moderation assistant. Always respond with valid JSON.'],
            ['role' => 'user', 'content' => $prompt],
        ], null, [
            'temperature' => 0.3,
            'max_tokens' => 400,
        ]);

        if (!$result['success']) {
            return $result;
        }

        $parsed = $this->parseJson($result['content']);

        if (!$parsed) {
            return [
                'success' => false,
                'message' => __('AI returned invalid response. Please try again.'),
                'raw' => $result['content'],
            ];
        }

        return [
            'success' => true,
            'data' => [
                'spam_score' => max(0, min(100, (int) ($parsed['spam_score'] ?? 0))),
                'sentiment' => in_array($parsed['sentiment'] ?? '', ['positive', 'neutral', 'negative'])
                    ? $parsed['sentiment']
                    : 'neutral',
                'toxicity' => max(0, min(100, (int) ($parsed['toxicity'] ?? 0))),
                'is_spam' => (bool) ($parsed['is_spam'] ?? false),
                'reason' => mb_substr((string) ($parsed['reason'] ?? ''), 0, 200),
                'flags' => is_array($parsed['flags'] ?? null)
                    ? array_slice(array_filter(array_map('strval', $parsed['flags'])), 0, 6)
                    : [],
            ],
            'model' => $result['model'] ?? null,
            'usage' => $result['usage'] ?? [],
        ];
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
}