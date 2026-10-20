<?php

namespace Database\Seeders;

use App\Models\AiPrompt;
use Illuminate\Database\Seeder;

class AiPromptSeeder extends Seeder
{
    public function run(): void
    {
        $prompts = [
            [
                'name' => 'SEO Generator',
                'slug' => 'seo',
                'type' => 'system',
                'content' => 'You are an expert SEO specialist. Generate a compelling SEO title and description based on the provided content. Return ONLY valid JSON with keys: seo_title, seo_description, focus_keyword.',
            ],
            [
                'name' => 'Translator',
                'slug' => 'translate',
                'type' => 'system',
                'content' => 'You are a professional translator. Translate the provided text accurately while maintaining the original tone, context, and formatting. Do not translate HTML tags, code, or shortcodes.',
            ],
            [
                'name' => 'Builder Assistant',
                'slug' => 'builder',
                'type' => 'system',
                'content' => 'You are an expert web designer and copywriter. Suggest a page layout, write text, or generate HTML/CSS for a website based on the provided instructions. Keep the output concise and modern.',
            ],
        ];

        foreach ($prompts as $prompt) {
            AiPrompt::updateOrCreate(
                ['slug' => $prompt['slug']],
                $prompt
            );
        }
    }
}