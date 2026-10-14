<?php

namespace App\Services;

use App\Models\Template;
use Illuminate\Support\Str;

class TemplateLibraryService
{
    public function export(Template $template): array
    {
        return [
            'format' => 'rj-template',
            'version' => '1.0',
            'exported_at' => now()->toIso8601String(),
            'site' => [
                'name' => rj_get_option('site_name', config('app.name')),
                'url' => url('/'),
            ],
            'template' => [
                'name' => $template->name,
                'slug' => $template->slug,
                'type' => $template->type,
                'content' => $template->content,
                'conditions' => $template->conditions,
                'settings' => $template->settings,
                'status' => $template->status,
            ],
            'assets' => $this->collectAssets($template->content),
        ];
    }

    public function exportJson(Template $template): string
    {
        return json_encode(
            $this->export($template),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    public function exportMultiple(array $ids): array
    {
        $templates = Template::whereIn('id', $ids)->get();

        return [
            'format' => 'rj-template-pack',
            'version' => '1.0',
            'exported_at' => now()->toIso8601String(),
            'site' => [
                'name' => rj_get_option('site_name', config('app.name')),
                'url' => url('/'),
            ],
            'templates' => $templates->map(fn($t) => $this->export($t)['template'])->toArray(),
        ];
    }

    public function import(array $data, array $options = []): array
    {
        $format = $data['format'] ?? '';

        if ($format === 'rj-template') {
            return $this->importSingle($data, $options);
        }

        if ($format === 'rj-template-pack') {
            return $this->importPack($data, $options);
        }

        throw new \InvalidArgumentException('Unknown template format.');
    }

    protected function importSingle(array $data, array $options): array
    {
        $tpl = $data['template'] ?? [];

        if (empty($tpl['name']) || empty($tpl['type'])) {
            throw new \InvalidArgumentException('Invalid template data.');
        }

        $template = $this->createFromData($tpl, $options);

        return [
            'created' => 1,
            'updated' => 0,
            'skipped' => 0,
            'templates' => [
                ['id' => $template->id, 'name' => $template->name, 'action' => 'created'],
            ],
        ];
    }

    protected function importPack(array $data, array $options): array
    {
        $templates = $data['templates'] ?? [];

        $stats = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'templates' => [],
        ];

        foreach ($templates as $tpl) {
            try {
                $existing = null;

                if (!empty($tpl['slug'])) {
                    $existing = Template::where('slug', $tpl['slug'])->first();
                }

                if ($existing && !empty($options['overwrite'])) {
                    $existing->update([
                        'name' => $tpl['name'],
                        'type' => $tpl['type'],
                        'content' => $tpl['content'] ?? [],
                        'conditions' => $tpl['conditions'] ?? null,
                        'settings' => $tpl['settings'] ?? null,
                    ]);
                    $stats['updated']++;
                    $stats['templates'][] = ['id' => $existing->id, 'name' => $existing->name, 'action' => 'updated'];
                    continue;
                }

                if ($existing) {
                    $stats['skipped']++;
                    $stats['templates'][] = ['id' => $existing->id, 'name' => $tpl['name'], 'action' => 'skipped'];
                    continue;
                }

                $template = $this->createFromData($tpl, $options);
                $stats['created']++;
                $stats['templates'][] = ['id' => $template->id, 'name' => $template->name, 'action' => 'created'];
            } catch (\Throwable $e) {
                $stats['skipped']++;
            }
        }

        return $stats;
    }

    protected function createFromData(array $tpl, array $options): Template
    {
        $name = $options['prefix'] ?? '';
        $name .= $tpl['name'] ?? 'Imported';

        $slug = $tpl['slug'] ?? Str::slug($name);
        $slug = $this->uniqueSlug($slug);

        return Template::create([
            'name' => $name,
            'slug' => $slug,
            'type' => $tpl['type'] ?? 'page',
            'content' => $tpl['content'] ?? [],
            'conditions' => $tpl['conditions'] ?? null,
            'settings' => $tpl['settings'] ?? null,
            'status' => 'draft',
            'is_default' => false,
            'author_id' => auth()->id(),
        ]);
    }

    protected function uniqueSlug(string $slug): string
    {
        $slug = Str::slug($slug);
        $base = $slug;
        $i = 1;

        while (Template::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    protected function collectAssets($content): array
    {
        $images = [];

        if (!is_array($content) || empty($content['blocks'])) {
            return ['images' => []];
        }

        $walk = function ($blocks) use (&$walk, &$images) {
            foreach ($blocks as $block) {
                if (!is_array($block)) continue;

                $settings = $block['settings'] ?? [];

                foreach ($settings as $key => $value) {
                    if (is_string($value) && preg_match('/^https?:\/\//', $value) &&
                        preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $value)) {
                        $images[] = $value;
                    }
                }

                if (!empty($block['children'])) {
                    $walk($block['children']);
                }
            }
        };

        $walk($content['blocks']);

        return ['images' => array_values(array_unique($images))];
    }
}