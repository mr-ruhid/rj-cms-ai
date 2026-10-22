<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExtractTranslationsCommand extends Command
{
    protected $signature = 'rj:extract-translations {--locale=en}';
    protected $description = 'Scan all project files for __() and trans() strings and extract them to a JSON file';

    public function handle()
    {
        $locale = $this->option('locale');
        $langPath = lang_path("{$locale}.json");

        $this->info("Scanning project for translation strings...");

        $directories = [
            app_path(),
            resource_path('views'),
            base_path('routes'),
        ];

        $strings = [];

        $patterns = [
            '/__\(\s*[\'"]([^\'"]+)[\'"]\s*\)/',
            '/trans\(\s*[\'"]([^\'"]+)[\'"]\s*\)/',
            '/@lang\(\s*[\'"]([^\'"]+)[\'"]\s*\)/',
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                continue;
            }

            $files = File::allFiles($directory);

            foreach ($files as $file) {
                if (!in_array($file->getExtension(), ['php', 'blade.php'])) {
                    continue;
                }

                $content = File::get($file->getPathname());

                foreach ($patterns as $pattern) {
                    if (preg_match_all($pattern, $content, $matches)) {
                        foreach ($matches[1] as $string) {
                            $strings[trim($string)] = trim($string);
                        }
                    }
                }
            }
        }

        ksort($strings);

        $existing = [];
        if (File::exists($langPath)) {
            $existing = json_decode(File::get($langPath), true) ?? [];
        }

        $merged = array_merge($existing, $strings);

        File::put($langPath, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("Extracted " . count($strings) . " strings.");
        $this->info("Total " . count($merged) . " strings saved to {$locale}.json");
    }
}