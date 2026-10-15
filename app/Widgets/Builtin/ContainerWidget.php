<?php

namespace App\Widgets\Builtin;

use App\Widgets\BaseWidget;
use App\Widgets\WidgetRegistry;

class ContainerWidget extends BaseWidget
{
    public function id(): string
    {
        return 'container';
    }

    public function name(): string
    {
        return __('Container');
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"/></svg>';
    }

    public function category(): string
    {
        return 'layout';
    }

    public function description(): string
    {
        return __('Group widgets into rows or columns.');
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'width',
                'type' => 'select',
                'label' => __('Width'),
                'default' => 'boxed',
                'tab' => 'content',
                'options' => [
                    'boxed' => __('Boxed') . ' — ' . __('Centered with max width'),
                    'full' => __('Full Width') . ' — ' . __('Edge to edge'),
                ],
            ],
            [
                'name' => 'columns',
                'type' => 'select',
                'label' => __('Columns'),
                'default' => '1',
                'tab' => 'content',
                'options' => [
                    '1' => '1 ' . __('Column'),
                    '2' => '2 ' . __('Columns'),
                    '3' => '3 ' . __('Columns'),
                    '4' => '4 ' . __('Columns'),
                    '6' => '6 ' . __('Columns'),
                ],
            ],
            [
                'name' => 'column_widths',
                'type' => 'text',
                'label' => __('Column Widths'),
                'default' => '',
                'tab' => 'content',
                'placeholder' => '70,30',
                'help' => __('Comma-separated ratios, e.g. 70,30. Leave empty for equal columns.'),
            ],
            [
                'name' => 'keep_tablet',
                'type' => 'toggle',
                'label' => __('Keep Layout on Tablet'),
                'default' => false,
                'tab' => 'content',
                'help' => __('Prevent columns from stacking on tablet.'),
            ],
            [
                'name' => 'keep_mobile',
                'type' => 'toggle',
                'label' => __('Keep Layout on Mobile'),
                'default' => false,
                'tab' => 'content',
                'help' => __('Prevent columns from stacking on mobile. Useful for headers.'),
            ],
            [
                'name' => 'flex_direction',
                'type' => 'select',
                'label' => __('Direction'),
                'default' => 'row',
                'tab' => 'content',
                'options' => [
                    'row' => __('Row'),
                    'column' => __('Column'),
                    'row-reverse' => __('Row Reverse'),
                    'column-reverse' => __('Column Reverse'),
                ],
            ],
            [
                'name' => 'justify_content',
                'type' => 'select',
                'label' => __('Justify Content'),
                'default' => 'start',
                'tab' => 'content',
                'options' => [
                    'start' => __('Start'),
                    'center' => __('Center'),
                    'end' => __('End'),
                    'space-between' => __('Space Between'),
                    'space-around' => __('Space Around'),
                    'space-evenly' => __('Space Evenly'),
                ],
            ],
            [
                'name' => 'align_items',
                'type' => 'select',
                'label' => __('Align Items'),
                'default' => 'stretch',
                'tab' => 'content',
                'options' => [
                    'start' => __('Top'),
                    'center' => __('Center'),
                    'end' => __('Bottom'),
                    'stretch' => __('Stretch'),
                    'baseline' => __('Baseline'),
                ],
            ],
            [
                'name' => 'flex_wrap',
                'type' => 'select',
                'label' => __('Wrap'),
                'default' => 'wrap',
                'tab' => 'content',
                'options' => [
                    'nowrap' => __('No Wrap'),
                    'wrap' => __('Wrap'),
                    'wrap-reverse' => __('Wrap Reverse'),
                ],
            ],
            [
                'name' => 'gap',
                'type' => 'select',
                'label' => __('Gap'),
                'default' => 'md',
                'tab' => 'style',
                'options' => [
                    'none' => __('None'),
                    'sm' => __('Small'),
                    'md' => __('Medium'),
                    'lg' => __('Large'),
                    'xl' => __('Extra Large'),
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
                'name' => 'min_height',
                'type' => 'select',
                'label' => __('Min Height'),
                'default' => 'auto',
                'tab' => 'style',
                'options' => [
                    'auto' => __('Auto'),
                    'sm' => '200px',
                    'md' => '400px',
                    'lg' => '600px',
                    'xl' => '800px',
                    'screen' => '100vh',
                    'custom' => __('Custom'),
                ],
            ],
            [
                'name' => 'min_height_custom',
                'type' => 'text',
                'label' => __('Custom Min Height'),
                'default' => '',
                'tab' => 'style',
                'placeholder' => 'e.g. 500px, 80vh',
            ],
            [
                'name' => 'border',
                'type' => 'select',
                'label' => __('Border'),
                'default' => 'none',
                'tab' => 'style',
                'options' => [
                    'none' => __('None'),
                    'solid' => __('Solid'),
                    'dashed' => __('Dashed'),
                ],
            ],
            [
                'name' => 'border_radius',
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

    public function render(array $settings, array $children = []): string
    {
        $cols = max(1, (int) ($settings['columns'] ?? 1));

        $gapMap = ['none' => '0', 'sm' => '0.5rem', 'md' => '1rem', 'lg' => '1.5rem', 'xl' => '2rem'];
        $gap = $gapMap[$settings['gap'] ?? 'md'] ?? '1rem';

        $alignMap = [
            'start' => 'flex-start',
            'center' => 'center',
            'end' => 'flex-end',
            'stretch' => 'stretch',
            'baseline' => 'baseline',
        ];
        $ai = $alignMap[$settings['align_items'] ?? 'stretch'] ?? 'stretch';

        $jcMap = [
            'start' => 'flex-start',
            'center' => 'center',
            'end' => 'flex-end',
            'space-between' => 'space-between',
            'space-around' => 'space-around',
            'space-evenly' => 'space-evenly',
        ];
        $jc = $jcMap[$settings['justify_content'] ?? 'start'] ?? 'flex-start';

        $dir = in_array($settings['flex_direction'] ?? 'row', ['row', 'column', 'row-reverse', 'column-reverse'], true)
            ? $settings['flex_direction']
            : 'row';

        $wrap = in_array($settings['flex_wrap'] ?? 'wrap', ['nowrap', 'wrap', 'wrap-reverse'], true)
            ? $settings['flex_wrap']
            : 'wrap';

        $padding = $this->dimensionCss($settings, 'padding_dim');
        $margin = $this->dimensionCss($settings, 'margin_dim');

        $minHeight = 'auto';
        if (($settings['min_height'] ?? 'auto') === 'custom') {
            $minHeight = $settings['min_height_custom'] ?? 'auto';
        } elseif (($settings['min_height'] ?? 'auto') === 'screen') {
            $minHeight = '100vh';
        } elseif (!empty($settings['min_height']) && $settings['min_height'] !== 'auto') {
            $minHeight = ['sm' => '200px', 'md' => '400px', 'lg' => '600px', 'xl' => '800px'][$settings['min_height']] ?? 'auto';
        }

        $border = 'none';
        if (($settings['border'] ?? 'none') === 'solid') {
            $border = '2px solid #e5e7eb';
        } elseif (($settings['border'] ?? 'none') === 'dashed') {
            $border = '2px dashed #e5e7eb';
        }

        $radiusMap = ['none' => '0', 'sm' => '4px', 'md' => '8px', 'lg' => '16px', 'full' => '9999px'];
        $radius = $radiusMap[$settings['border_radius'] ?? 'md'] ?? '8px';

        $bg = $this->backgroundCss($settings);
        $shadow = $this->boxShadowCss($settings['box_shadow'] ?? null);

        $outerStyles = [];
        if ($bg !== '') $outerStyles[] = rtrim($bg, ';');
        if ($padding !== '') $outerStyles[] = 'padding:' . $padding;
        if ($border !== 'none') $outerStyles[] = 'border:' . $border;
        if ($minHeight !== 'auto') $outerStyles[] = 'min-height:' . $minHeight;
        if ($shadow !== '') $outerStyles[] = rtrim($shadow, ';');

        $isFullWidth = ($settings['width'] ?? 'boxed') === 'full';

        if ($isFullWidth) {
            $outerStyles[] = 'width:100%';
            if ($margin !== '') $outerStyles[] = 'margin:' . $margin;
            $outerStyles[] = 'border-radius:0';
        } else {
            $outerStyles[] = 'max-width:1100px';
            if ($margin !== '') {
                $outerStyles[] = 'margin:' . $margin;
            } else {
                $outerStyles[] = 'margin:0 auto';
            }
            $outerStyles[] = 'width:100%';
            $outerStyles[] = 'border-radius:' . $radius;
        }

        $widths = $this->parseWidths($settings['column_widths'] ?? '');
        $slotCount = max($cols, count($children));

        $gridClass = 'rj-cg';
        $gridClass .= ($settings['distribute'] ?? '') === 'content' ? ' rj-fit' : '';
        $gridClass .= !empty($settings['keep_tablet']) ? ' rj-keep-tablet' : '';
        $gridClass .= !empty($settings['keep_mobile']) ? ' rj-keep-mobile' : '';

        $gridVars = [
            '--c:' . $cols,
            '--g:' . $gap,
            '--al:' . $ai,
            '--jc:' . $jc,
            '--dir:' . $dir,
            '--wrap:' . $wrap,
        ];

        $slotsHtml = '';

        for ($i = 0; $i < $slotCount; $i++) {
            $child = $children[$i] ?? null;
            $slotStyles = ['min-width:0'];

            if (!empty($widths[$i])) {
                $slotStyles[] = '--sw:' . $widths[$i];
            }

            $slotAttrs = [
                'class' => 'rj-slot',
                'style' => implode(';', $slotStyles),
            ];

            $inner = '';

            if ($child) {
                $inner = $this->renderChild($child);
            }

            $slotsHtml .= '<div' . $this->attr($slotAttrs) . '>' . $inner . '</div>';
        }

        $gridAttrs = [
            'class' => $gridClass,
            'style' => implode(';', $gridVars),
        ];

        $outerAttrs = [
            'style' => implode(';', $outerStyles),
        ];

        $id = $this->cssId($settings);
        if ($id !== '') {
            $outerAttrs['id'] = $id;
        }

        $class = $this->cssClass($settings);
        if ($class !== '') {
            $outerAttrs['class'] = $class;
        }

        $html = '<div' . $this->attr($outerAttrs) . '>'
            . '<div' . $this->attr($gridAttrs) . '>'
            . $slotsHtml
            . '</div>'
            . '</div>';

        return $this->applyAnimation($html, $settings);
    }

    protected function parseWidths(string $raw): array
    {
        $raw = trim($raw);

        if ($raw === '') {
            return [];
        }

        $parts = array_map('trim', explode(',', $raw));
        $out = [];

        foreach ($parts as $p) {
            if ($p === '') {
                continue;
            }
            $n = (float) $p;
            if ($n > 0) {
                $out[] = $n;
            }
        }

        return $out;
    }

    protected function renderChild(array $child): string
    {
        $type = $child['type'] ?? '';

        if ($type === '') {
            return '';
        }

        $widget = WidgetRegistry::get($type);

        if (!$widget) {
            return '';
        }

        $settings = $child['settings'] ?? [];
        $kids = $child['children'] ?? [];

        if ($type === 'container') {
            return $widget->render($settings, $kids);
        }

        return $widget->render($settings);
    }
}