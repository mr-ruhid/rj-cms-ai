<?php

namespace RjPlugins\FormBuilder\Widgets;

use App\Widgets\BaseWidget;
use RjPlugins\FormBuilder\Models\Form;

class FormWidget extends BaseWidget
{
    public function id(): string
    {
        return 'form';
    }

    public function name(): string
    {
        return __('Form');
    }

    public function icon(): string
    {
        return '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';
    }

    public function category(): string
    {
        return 'content';
    }

    public function description(): string
    {
        return __('Embed a custom form created in Form Builder.');
    }

    public function schema(): array
    {
        $forms = Form::active()->get();
        $options = ['' => __('— Select Form —')];

        foreach ($forms as $form) {
            $options[$form->id] = $form->name;
        }

        return [
            [
                'name' => 'form_id',
                'type' => 'select',
                'label' => __('Select Form'),
                'default' => '',
                'options' => $options,
            ],
            [
                'name' => 'layout',
                'type' => 'select',
                'label' => __('Layout'),
                'default' => 'stack',
                'options' => [
                    'stack' => __('Stacked'),
                    'grid-2' => __('2 Columns Grid'),
                    'inline' => __('Inline'),
                ],
            ],
            [
                'name' => 'button_bg',
                'type' => 'color',
                'label' => __('Button Background'),
                'default' => '#7c3aed',
            ],
            [
                'name' => 'button_text_color',
                'type' => 'color',
                'label' => __('Button Text Color'),
                'default' => '#ffffff',
            ],
            [
                'name' => 'field_bg',
                'type' => 'color',
                'label' => __('Field Background'),
                'default' => '#ffffff',
            ],
            [
                'name' => 'field_border_color',
                'type' => 'color',
                'label' => __('Field Border Color'),
                'default' => '#d1d5db',
            ],
            [
                'name' => 'rounded',
                'type' => 'select',
                'label' => __('Border Radius'),
                'default' => 'md',
                'options' => [
                    'none' => __('None'),
                    'sm' => __('Small'),
                    'md' => __('Medium'),
                    'lg' => __('Large'),
                    'full' => __('Full'),
                ],
            ],
        ];
    }

    public function render(array $settings): string
    {
        $formId = $settings['form_id'] ?? null;

        if (!$formId) {
            return '<div class="p-4 bg-amber-50 text-amber-800 text-sm border border-amber-200 rounded-lg">' . __('Please select a form to display.') . '</div>';
        }

        $form = Form::with('fields')->find($formId);

        if (!$form) {
            return '<div class="p-4 bg-red-50 text-red-800 text-sm border border-red-200 rounded-lg">' . __('Selected form not found.') . '</div>';
        }

        $layout = $settings['layout'] ?? 'stack';
        $buttonBg = $settings['button_bg'] ?? '#7c3aed';
        $buttonTextColor = $settings['button_text_color'] ?? '#ffffff';
        $fieldBg = $settings['field_bg'] ?? '#ffffff';
        $fieldBorderColor = $settings['field_border_color'] ?? '#d1d5db';
        $rounded = $settings['rounded'] ?? 'md';

        $radiusMap = [
            'none' => '0',
            'sm' => '4px',
            'md' => '8px',
            'lg' => '12px',
            'full' => '9999px',
        ];
        $radius = $radiusMap[$rounded] ?? '8px';

        $styles = [
            'button_bg' => $buttonBg,
            'button_text' => $buttonTextColor,
            'field_bg' => $fieldBg,
            'field_border' => $fieldBorderColor,
            'radius' => $radius,
        ];

        return view('plugin-form-builder::widget', compact('form', 'layout', 'styles'))->render();
    }
}
