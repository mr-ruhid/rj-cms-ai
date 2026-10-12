<?php

namespace App\Services;

class AiTranslatorService
{
    protected OpenRouterService $ai;
    protected int $batchSize = 50;

    public function __construct(?OpenRouterService $ai = null)
    {
        $this->ai = $ai ?? new OpenRouterService();
    }

    public function translateMissing(string $targetLocale): array
    {
        $baseLocale = config('app.locale');
        $basePath = lang_path($baseLocale . '.json');
        $targetPath = lang_path($targetLocale . '.json');

        if (!file_exists($basePath)) {
            return ['success' => false, 'error' => 'Base language file not found.'];
        }

        if (!file_exists($targetPath)) {
            return ['success' => false, 'error' => 'Target language file not found.'];
        }

        $base = json_decode(file_get_contents($basePath), true) ?? [];
        $target = json_decode(file_get_contents($targetPath), true) ?? [];

        $base = $this->cleanupKeys($base);

        $missing = [];
        foreach ($base as $key => $value) {
            if (!isset($target[$key]) || trim((string) $target[$key]) === '') {
                $missing[] = $key;
            }
        }

        if (empty($missing)) {
            return [
                'success' => true,
                'translated' => 0,
                'message' => 'All keys already translated.',
            ];
        }

        $batches = array_chunk($missing, $this->batchSize);
        $translated = 0;
        $errors = [];

        foreach ($batches as $batch) {
            $result = $this->translateBatch($batch, $targetLocale);

            if (!$result['success']) {
                $errors[] = $result['error'];
                continue;
            }

            foreach ($result['translations'] as $english => $translation) {
                $target[$english] = $translation;
                $translated++;
            }
        }

        ksort($target);

        file_put_contents(
            $targetPath,
            json_encode($target, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        return [
            'success' => true,
            'translated' => $translated,
            'missing_total' => count($missing),
            'errors' => $errors,
        ];
    }

    protected function translateBatch(array $keys, string $targetLocale): array
    {
        $localeName = $this->localeName($targetLocale);

        $lines = [];
        foreach ($keys as $i => $key) {
            $lines[] = ($i + 1) . '. ' . $key;
        }
        $userContent = implode("\n", $lines);

        $system = "You are a professional translator. Translate each numbered line from English to {$localeName}. "
            . "Return ONLY the translations, one per line, keeping the same numbering. "
            . "Preserve placeholders like :name, :count, {var} unchanged. "
            . "Do not add explanations, notes or comments.";

        $result = $this->ai->chat(
            [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $userContent],
            ],
            null,
            [
                'temperature' => 0.2,
                'max_tokens' => max(500, count($keys) * 80),
            ]
        );

        if (!$result['success']) {
            return ['success' => false, 'error' => $result['error'] ?? 'Unknown error'];
        }

        $translations = $this->parseResponse($result['content'] ?? '', $keys);

        return ['success' => true, 'translations' => $translations];
    }

    protected function parseResponse(string $content, array $keys): array
    {
        $result = [];
        $lines = preg_split('/\r\n|\r|\n/', trim($content));

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            if (preg_match('/^(\d+)[\.\)]\s*(.+)$/u', $line, $m)) {
                $idx = (int) $m[1] - 1;
                $translation = trim($m[2]);

                if (isset($keys[$idx]) && $translation !== '') {
                    $result[$keys[$idx]] = $translation;
                }
            }
        }

        return $result;
    }

    protected function cleanupKeys(array $data): array
    {
        $clean = [];

        foreach ($data as $key => $value) {
            $key = trim((string) $key);

            if ($key === '') {
                continue;
            }

            if (preg_match('/^[\-\_\=\.\*\#\s\+]+$/u', $key)) {
                continue;
            }

            $clean[$key] = $value;
        }

        return $clean;
    }

    protected function localeName(string $locale): string
    {
        $names = [
            'az' => 'Azerbaijani',
            'en' => 'English',
            'ru' => 'Russian',
            'tr' => 'Turkish',
            'de' => 'German',
            'fr' => 'French',
            'es' => 'Spanish',
            'it' => 'Italian',
            'ar' => 'Arabic',
            'zh' => 'Chinese',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'pt' => 'Portuguese',
            'nl' => 'Dutch',
            'pl' => 'Polish',
            'uk' => 'Ukrainian',
            'fa' => 'Persian',
        ];

        return $names[$locale] ?? $locale;
    }
}
