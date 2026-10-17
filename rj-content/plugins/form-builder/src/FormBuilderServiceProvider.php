<?php

namespace RjPlugins\FormBuilder;

use App\Plugins\PluginServiceProvider;

class FormBuilderServiceProvider extends PluginServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        // Gələcəkdə plugin-ə xas hook-ları bura əlavə edəcəyik
        // Məsələn: rj_add_action('some_hook', [$this, 'someMethod']);
    }
}