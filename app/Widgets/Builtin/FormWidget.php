<?php

namespace App\Widgets\Builtin;

use App\Models\Form;
use App\Widgets\BaseWidget;

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
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M7 8h10M7 12h10M7 16h6"/></svg>';
    }

    public function category(): string
    {
        return 'content';
    }

    public function description(): string
    {
        return __('Embed a form.');
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'form_id',
                'type' => 'form',
                'label' => __('Form'),
                'default' => '',
                'tab' => 'content',
            ],
            [
                'name' => 'layout',
                'type' => 'select',
                'label' => __('Layout'),
                'default' => 'stack',
                'tab' => 'content',
                'options' => [
                    'stack' => __('Stacked'),
                    'inline' => __('Inline'),
                    'grid-2' => __('2 Columns'),
                ],
            ],
            [
                'name' => 'label_position',
                'type' => 'select',
                'label' => __('Label Position'),
                'default' => 'top',
                'tab' => 'content',
                'options' => [
                    'top' => __('Above field'),
                    'inline' => __('Inline'),
                    'hidden' => __('Hidden'),
                ],
            ],
            [
                'name' => 'padding_dim',
                'type' => 'dimensions',
                'label' => __('Padding'),
                'default' => ['top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'linked' => false],
                'tab' => 'style',
            ],
            [
                'name' => 'margin_dim',
                'type' => 'dimensions',
                'label' => __('Margin'),
                'default' => ['top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'linked' => false],
                'tab' => 'style',
            ],
            [
                'name' => 'field_bg',
                'type' => 'color',
                'label' => __('Field Background'),
                'default' => '#ffffff',
                'tab' => 'style',
            ],
            [
                'name' => 'field_border_color',
                'type' => 'color',
                'label' => __('Field Border'),
                'default' => '#d1d5db',
                'tab' => 'style',
            ],
            [
                'name' => 'field_text_color',
                'type' => 'color',
                'label' => __('Field Text'),
                'default' => '#111827',
                'tab' => 'style',
            ],
            [
                'name' => 'button_bg',
                'type' => 'color',
                'label' => __('Button Background'),
                'default' => '#7c3aed',
                'tab' => 'style',
            ],
            [
                'name' => 'button_text_color',
                'type' => 'color',
                'label' => __('Button Text'),
                'default' => '#ffffff',
                'tab' => 'style',
            ],
            [
                'name' => 'rounded',
                'type' => 'select',
                'label' => __('Border Radius'),
                'default' => 'md',
                'tab' => 'style',
                'options' => [
                    'none' => __('None'),
                    'sm' => '4px',
                    'md' => '8px',
                    'lg' => '16px',
                    'full' => __('Full'),
                ],
            ],
        ];
    }

    public function render(array $settings): string
    {
        $formId = (int) ($settings['form_id'] ?? 0);

        if ($formId <= 0) {
            return '';
        }

        $form = Form::with('fields')->active()->find($formId);

        if (!$form) {
            return '';
        }

        $layout = $settings['layout'] ?? 'stack';
        $labelPosition = $settings['label_position'] ?? 'top';
        $radiusMap = ['none' => '0', 'sm' => '4px', 'md' => '8px', 'lg' => '16px', 'full' => '9999px'];
        $radius = $radiusMap[$settings['rounded'] ?? 'md'] ?? '8px';

        $wrapStyles = [];
        $wrapStyles[] = '--rj-form-radius:' . $radius;
        $wrapStyles[] = '--rj-form-field-bg:' . ($settings['field_bg'] ?? '#ffffff');
        $wrapStyles[] = '--rj-form-field-bc:' . ($settings['field_border_color'] ?? '#d1d5db');
        $wrapStyles[] = '--rj-form-field-tc:' . ($settings['field_text_color'] ?? '#111827');
        $wrapStyles[] = '--rj-form-btn-bg:' . ($settings['button_bg'] ?? '#7c3aed');
        $wrapStyles[] = '--rj-form-btn-tc:' . ($settings['button_text_color'] ?? '#ffffff');

        $padding = $this->dimensionCss($settings, 'padding_dim');
        if ($padding !== '') $wrapStyles[] = 'padding:' . $padding;

        $margin = $this->dimensionCss($settings, 'margin_dim');
        if ($margin !== '') $wrapStyles[] = 'margin:' . $margin;

        $bg = $this->backgroundCss($settings);
        if ($bg !== '') $wrapStyles[] = rtrim($bg, ';');

        $wrapAttrs = [
            'class' => 'rj-form-wrap rj-form-' . $layout . ' rj-form-labels-' . $labelPosition,
            'style' => implode(';', $wrapStyles),
        ];

        $id = $this->cssId($settings);
        if ($id !== '') $wrapAttrs['id'] = $id;

        $class = $this->cssClass($settings);
        if ($class !== '') $wrapAttrs['class'] .= ' ' . $class;

        $formSettings = $form->settings();
        $fieldsHtml = '';

        foreach ($form->fields as $field) {
            $fieldsHtml .= $this->renderField($field, $labelPosition);
        }

        $captcha = '';
        if ($formSettings['enable_captcha'] ?? false) {
            $a = random_int(2, 9);
            $b = random_int(2, 9);
            session(['rj_captcha_answer' => (string) ($a + $b)]);
            $captcha = $this->renderCaptcha($a, $b);
        }

        $html = '<div' . $this->attr($wrapAttrs) . '>'
            . '<form method="POST" action="' . route('frontend.form.submit', $form->slug) . '" class="rj-form" enctype="multipart/form-data" novalidate>'
            . csrf_field()
            . '<input type="hidden" name="rj_form_id" value="' . $form->id . '">'
            . $fieldsHtml
            . $captcha
            . '<div class="rj-form-actions">'
            . '<button type="submit" class="rj-form-submit">' . $this->escape($formSettings['submit_label'] ?? __('Submit')) . '</button>'
            . '</div>'
            . '<div class="rj-form-message" style="display:none;"></div>'
            . '</form>'
            . '</div>';

        return $this->applyAnimation($html, $settings);
    }

    protected function renderField($field, string $labelPosition): string
    {
        $required = $field->required ? ' required' : '';
        $requiredMark = $field->required ? '<span class="rj-form-req">*</span>' : '';
        $labelHtml = '';

        if ($labelPosition !== 'hidden') {
            $labelHtml = '<label class="rj-form-label" for="rj_f_' . $field->id . '">'
                . $this->escape($field->label)
                . $requiredMark
                . '</label>';
        }

        $inputAttrs = [
            'id' => 'rj_f_' . $field->id,
            'name' => $field->name,
            'class' => 'rj-form-input',
            'placeholder' => $field->placeholder ?? '',
        ];

        if ($field->required) {
            $inputAttrs['required'] = 'required';
        }

        $helpHtml = $field->help_text
            ? '<div class="rj-form-help">' . $this->escape($field->help_text) . '</div>'
            : '';

        $inputHtml = '';

        switch ($field->type) {
            case 'textarea':
                $inputAttrs['rows'] = 5;
                $inputHtml = '<textarea' . $this->attr($inputAttrs) . '>' . $this->escape($field->default_value ?? '') . '</textarea>';
                break;

            case 'select':
                $options = '';
                if (!($field->required)) {
                    $options .= '<option value="">' . __('— Select —') . '</option>';
                }
                foreach ($field->options() as $opt) {
                    $val = $opt['value'] ?? '';
                    $lbl = $opt['label'] ?? $val;
                    $sel = ($field->default_value ?? '') === $val ? ' selected' : '';
                    $options .= '<option value="' . $this->escape($val) . '"' . $sel . '>' . $this->escape($lbl) . '</option>';
                }
                $inputHtml = '<select' . $this->attr($inputAttrs) . '>' . $options . '</select>';
                break;

            case 'radio':
                $inputHtml = '<div class="rj-form-radio-group">';
                foreach ($field->options() as $opt) {
                    $val = $opt['value'] ?? '';
                    $lbl = $opt['label'] ?? $val;
                    $checked = ($field->default_value ?? '') === $val ? ' checked' : '';
                    $inputHtml .= '<label class="rj-form-radio">'
                        . '<input type="radio" name="' . $this->escape($field->name) . '" value="' . $this->escape($val) . '"' . $checked . $required . '>'
                        . '<span>' . $this->escape($lbl) . '</span>'
                        . '</label>';
                }
                $inputHtml .= '</div>';
                break;

            case 'checkbox':
                $inputHtml = '<div class="rj-form-checkbox-group">';
                foreach ($field->options() as $opt) {
                    $val = $opt['value'] ?? '';
                    $lbl = $opt['label'] ?? $val;
                    $inputHtml .= '<label class="rj-form-checkbox">'
                        . '<input type="checkbox" name="' . $this->escape($field->name) . '[]" value="' . $this->escape($val) . '">'
                        . '<span>' . $this->escape($lbl) . '</span>'
                        . '</label>';
                }
                $inputHtml .= '</div>';
                break;

            case 'file':
                unset($inputAttrs['placeholder']);
                $inputHtml = '<input type="file"' . $this->attr($inputAttrs) . '>';
                break;

            case 'hidden':
                unset($inputAttrs['placeholder'], $inputAttrs['class']);
                $inputAttrs['type'] = 'hidden';
                $inputAttrs['value'] = $field->default_value ?? '';
                $inputHtml = '<input' . $this->attr($inputAttrs) . '>';
                return $inputHtml;

            default:
                $inputAttrs['type'] = $field->type;
                $inputAttrs['value'] = $field->default_value ?? '';
                $inputHtml = '<input' . $this->attr($inputAttrs) . '>';
        }

        return '<div class="rj-form-field rj-form-field-' . $field->type . '">'
            . $labelHtml
            . $inputHtml
            . $helpHtml
            . '</div>';
    }

    protected function renderCaptcha(int $a, int $b): string
    {
        return '<div class="rj-form-field rj-form-field-captcha">'
            . '<label class="rj-form-label" for="rj_captcha">'
            . $this->escape($a . ' + ' . $b) . ' = ?'
            . '<span class="rj-form-req">*</span>'
            . '</label>'
            . '<input type="text" id="rj_captcha" name="rj_captcha" class="rj-form-input" required>'
            . '</div>';
    }
}