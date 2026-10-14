<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class CurrentUserTag extends BaseTag
{
    public function id(): string
    {
        return 'current_user';
    }

    public function name(): string
    {
        return __('Current User');
    }

    public function category(): string
    {
        return 'user';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
    }

    public function options(): array
    {
        return [
            [
                'name' => 'field',
                'type' => 'select',
                'label' => __('Field'),
                'default' => 'name',
                'options' => [
                    'name' => __('Name'),
                    'email' => __('Email'),
                    'id' => __('ID'),
                ],
            ],
        ];
    }

    public function resolve(array $options = []): string
    {
        $user = auth()->user();

        if (!$user) {
            return '';
        }

        $field = $this->option($options, 'field', 'name');

        $value = match ($field) {
            'email' => $user->email ?? '',
            'id' => (string) ($user->id ?? ''),
            default => $user->name ?? '',
        };

        return $this->escape($value);
    }
}