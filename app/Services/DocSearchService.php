<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DocSearchService
{
    protected string $docsPath;

    public function __construct()
    {
        $this->docsPath = resource_path('docs');
    }

    public function getContext(string $question): array
    {
        if (!File::isDirectory($this->docsPath)) {
            return ['context' => '', 'sources' => []];
        }

        $files = File::files($this->docsPath);
        $keywords = $this->extractKeywords($question);

        $results = [];

        foreach ($files as $file) {
            if ($file->getExtension() !== 'md') {
                continue;
            }

            $content = File::get($file->getPathname());
            $score = $this->calculateScore($content, $keywords);

            if ($score > 0) {
                $results[] = [
                    'file' => $file->getFilename(),
                    'score' => $score,
                    'content' => $content,
                ];
            }
        }

        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        $topResults = array_slice($results, 0, 3);

        $context = '';
        $sources = [];

        foreach ($topResults as $result) {
            $context .= "=== Document: {$result['file']} ===\n";
            $context .= $result['content'] . "\n\n";
            $sources[] = $result['file'];
        }

        if (empty($context)) {
            $overviewFile = $this->docsPath . '/project-overview.md';
            if (File::exists($overviewFile)) {
                $context = "=== Document: project-overview.md ===\n" . File::get($overviewFile);
                $sources[] = 'project-overview.md';
            }
        }

        return [
            'context' => $context,
            'sources' => $sources,
        ];
    }

    protected function extractKeywords(string $text): array
    {
        $stopWords = ['the', 'a', 'an', 'and', 'or', 'but', 'is', 'are', 'was', 'were', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'how', 'what', 'where', 'when', 'why', 'who', 'which', 'mən', 'necə', 'nə', 'hansı', 'harada', 'nə vaxt', 'niyə', 'kim', 'və', 'və ya', 'bu', 'o', 'bir', 'üçün', 'ilə', 'olan', 'olur', 'edir', 'edə', 'bilərəm', 'edim', 'necə', 'what', 'how', 'do', 'i', 'can', 'my', 'your', 'site'];

        $text = Str::lower(strip_tags($text));
        $words = preg_split('/[\s,.\-!?()]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);

        $keywords = [];
        foreach ($words as $word) {
            if (mb_strlen($word) < 3 || in_array($word, $stopWords, true)) {
                continue;
            }
            $keywords[] = $word;
        }

        return array_unique($keywords);
    }

    protected function calculateScore(string $content, array $keywords): int
    {
        $score = 0;
        $contentLower = Str::lower($content);

        foreach ($keywords as $keyword) {
            $count = substr_count($contentLower, $keyword);
            $score += $count;

            if (str_contains($contentLower, $keyword)) {
                $score += 1;
            }
        }

        return $score;
    }
}
