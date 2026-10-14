<?php

namespace App\Services;

use App\Models\Template;

class PopupResolver
{
    public function active(): array
    {
        return Template::query()
            ->where('type', 'popup')
            ->where('status', 'published')
            ->orderByDesc('updated_at')
            ->get()
            ->all();
    }

    public function find(int $id): ?Template
    {
        return Template::query()
            ->where('id', $id)
            ->where('type', 'popup')
            ->where('status', 'published')
            ->first();
    }

    public function toArray(Template $template): array
    {
        $settings = $template->popupSettings();

        return [
            'id' => $template->id,
            'name' => $template->name,
            'settings' => $settings,
            'html' => rj_render_template($template),
        ];
    }

    public function allToArray(): array
    {
        $out = [];

        foreach ($this->active() as $template) {
            $out[] = $this->toArray($template);
        }

        return $out;
    }
}