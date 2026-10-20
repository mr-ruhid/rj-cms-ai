<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class LogViewerService
{
    protected string $logPath;

    public function __construct()
    {
        $this->logPath = storage_path('logs');
    }

    public function getFiles(): array
    {
        if (!File::isDirectory($this->logPath)) {
            return [];
        }

        $files = File::files($this->logPath);
        $result = [];

        foreach ($files as $file) {
            if ($file->getExtension() !== 'log') continue;
            
            $result[] = [
                'name' => $file->getFilename(),
                'size' => $file->getSize(),
                'size_human' => $this->formatSize($file->getSize()),
                'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                'timestamp' => $file->getMTime(),
            ];
        }

        usort($result, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $result;
    }

    public function getEntries(string $filename, int $maxLines = 500): array
    {
        $path = $this->logPath . '/' . basename($filename);

        if (!File::exists($path)) {
            return [];
        }

        $file = new \SplFileObject($path, 'r');
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key();
        
        $startLine = max(0, $totalLines - $maxLines);
        $file->seek($startLine);

        $rawLines = [];
        while (!$file->eof()) {
            $line = $file->current();
            if (trim($line) !== '') {
                $rawLines[] = rtrim($line);
            }
            $file->next();
        }

        $entries = [];
        $currentEntry = null;
        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+(\w+)\.(\w+):\s+(.*)$/';

        foreach ($rawLines as $line) {
            if (preg_match($pattern, $line, $matches)) {
                if ($currentEntry) {
                    $entries[] = $currentEntry;
                }
                
                $context = '';
                $message = $matches[4];
                
                if (preg_match('/^(.*?)(\{.*\})$/s', $message, $msgMatches)) {
                    $message = trim($msgMatches[1]);
                    $context = trim($msgMatches[2]);
                }

                $currentEntry = [
                    'timestamp' => $matches[1],
                    'environment' => $matches[2],
                    'level' => strtoupper($matches[3]),
                    'message' => $message,
                    'context' => $context,
                    'stack' => '',
                ];
            } elseif ($currentEntry) {
                $currentEntry['stack'] .= $line . "\n";
            }
        }

        if ($currentEntry) {
            $entries[] = $currentEntry;
        }

        return array_reverse($entries);
    }

    public function clear(string $filename): array
    {
        $path = $this->logPath . '/' . basename($filename);

        if (!File::exists($path)) {
            return ['success' => false, 'message' => __('Log file not found.')];
        }

        try {
            File::put($path, '');
            return ['success' => true, 'message' => __('Log file cleared successfully.')];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => __('Failed to clear log: :msg', ['msg' => $e->getMessage()])];
        }
    }

    public function download(string $filename)
    {
        $path = $this->logPath . '/' . basename($filename);

        if (!File::exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }

    protected function formatSize(float $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}