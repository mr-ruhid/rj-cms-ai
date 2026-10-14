<?php

namespace App\Services;

use App\Models\Template;
use Illuminate\Http\Request;

class ThemeResolver
{
    protected ?Request $request = null;

    public function __construct()
    {
        $this->request = request();
    }

    public function header(): ?Template
    {
        return $this->resolve('header');
    }

    public function footer(): ?Template
    {
        return $this->resolve('footer');
    }

    public function page(): ?Template
    {
        return $this->resolve('page');
    }

    protected function resolve(string $type): ?Template
    {
        $candidates = Template::query()
            ->where('type', $type)
            ->where('status', 'published')
            ->orderByDesc('is_default')
            ->orderByDesc('updated_at')
            ->get();

        foreach ($candidates as $template) {
            if ($this->matchesConditions($template)) {
                return $template;
            }
        }

        return null;
    }

    protected function matchesConditions(Template $template): bool
    {
        $conditions = $template->conditions ?? [];

        if (empty($conditions) || !is_array($conditions)) {
            return true;
        }

        if (empty($conditions['rules'])) {
            return true;
        }

        $rules = $conditions['rules'];
        $match = $conditions['match'] ?? 'all';

        $results = [];

        foreach ($rules as $rule) {
            $results[] = $this->evaluateRule($rule);
        }

        if (empty($results)) {
            return true;
        }

        return $match === 'any' ? in_array(true, $results, true) : !in_array(false, $results, true);
    }

    protected function evaluateRule(array $rule): bool
    {
        $type = $rule['type'] ?? '';
        $value = $rule['value'] ?? '';

        return match ($type) {
            'entire_site' => true,
            'front_page' => $this->request->is('/'),
            'not_front_page' => !$this->request->is('/'),
            'single_post' => $this->isSinglePost(),
            'single_page' => $this->isSinglePage(),
            'archive' => $this->isArchive(),
            'search' => $this->request->is('search*'),
            '404' => false,
            'url_contains' => $value !== '' && str_contains($this->request->path(), $value),
            'url_exact' => $value !== '' && $this->request->path() === ltrim($value, '/'),
            default => true,
        };
    }

    protected function isSinglePost(): bool
    {
        return false;
    }

    protected function isSinglePage(): bool
    {
        return false;
    }

    protected function isArchive(): bool
    {
        return false;
    }
}