<?php

namespace App\Widgets\Builtin;

use App\Models\Menu;
use App\Widgets\BaseWidget;

class MenuWidget extends BaseWidget
{
    public function id(): string
    {
        return 'menu';
    }

    public function name(): string
    {
        return __('Menu');
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>';
    }

    public function category(): string
    {
        return 'header';
    }

    public function description(): string
    {
        return __('Display a navigation menu.');
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'menu_id',
                'type' => 'menu',
                'label' => __('Menu'),
                'default' => '',
                'tab' => 'content',
            ],
            [
                'name' => 'layout',
                'type' => 'select',
                'label' => __('Layout'),
                'default' => 'horizontal',
                'tab' => 'content',
                'options' => [
                    'horizontal' => __('Horizontal'),
                    'vertical' => __('Vertical'),
                ],
            ],
            [
                'name' => 'align',
                'type' => 'select',
                'label' => __('Alignment'),
                'default' => 'right',
                'tab' => 'content',
                'options' => [
                    'left' => __('Left'),
                    'center' => __('Center'),
                    'right' => __('Right'),
                ],
            ],
            [
                'name' => 'mobile_behavior',
                'type' => 'select',
                'label' => __('Mobile Behavior'),
                'default' => 'hamburger',
                'tab' => 'content',
                'options' => [
                    'hamburger' => __('Hamburger'),
                    'stack' => __('Stack'),
                    'scroll' => __('Scroll'),
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
                'name' => 'font_size',
                'type' => 'select',
                'label' => __('Font Size'),
                'default' => 'base',
                'tab' => 'style',
                'options' => [
                    'sm' => __('Small'),
                    'base' => __('Normal'),
                    'lg' => __('Large'),
                ],
            ],
            [
                'name' => 'font_weight',
                'type' => 'select',
                'label' => __('Font Weight'),
                'default' => '500',
                'tab' => 'style',
                'options' => [
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                ],
            ],
            [
                'name' => 'text_color',
                'type' => 'color',
                'label' => __('Text Color'),
                'default' => '#111827',
                'tab' => 'style',
            ],
            [
                'name' => 'hover_color',
                'type' => 'color',
                'label' => __('Hover Color'),
                'default' => '#7c3aed',
                'tab' => 'style',
            ],
            [
                'name' => 'hover_style',
                'type' => 'select',
                'label' => __('Hover Style'),
                'default' => 'underline',
                'tab' => 'style',
                'options' => [
                    'none' => __('None'),
                    'underline' => __('Underline'),
                    'background' => __('Background'),
                    'both' => __('Both'),
                ],
            ],
            [
                'name' => 'active_color',
                'type' => 'color',
                'label' => __('Active Color'),
                'default' => '#7c3aed',
                'tab' => 'style',
            ],
            [
                'name' => 'dropdown_bg',
                'type' => 'color',
                'label' => __('Dropdown Background'),
                'default' => '#ffffff',
                'tab' => 'style',
            ],
            [
                'name' => 'dropdown_border',
                'type' => 'select',
                'label' => __('Dropdown Border'),
                'default' => 'yes',
                'tab' => 'style',
                'options' => [
                    'yes' => __('Yes'),
                    'no' => __('No'),
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
        ];
    }

    public function render(array $settings): string
    {
        $menuId = (int) ($settings['menu_id'] ?? 0);

        if ($menuId <= 0) {
            return '';
        }

        $menu = Menu::with(['items' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])->find($menuId);

        if (!$menu) {
            return '';
        }

        $items = $this->buildTree($menu->items->all());

        if (empty($items)) {
            return '';
        }

        $layout = $settings['layout'] ?? 'horizontal';
        $align = in_array($settings['align'] ?? 'right', ['left', 'center', 'right'], true) ? $settings['align'] : 'right';
        $mobileBehavior = in_array($settings['mobile_behavior'] ?? 'hamburger', ['hamburger', 'stack', 'scroll'], true)
            ? $settings['mobile_behavior']
            : 'hamburger';

        $gapMap = ['none' => '0', 'sm' => '12px', 'md' => '24px', 'lg' => '36px', 'xl' => '48px'];
        $fontSizeMap = ['sm' => '13px', 'base' => '15px', 'lg' => '17px'];

        $gap = $gapMap[$settings['gap'] ?? 'md'] ?? '24px';
        $fontSize = $fontSizeMap[$settings['font_size'] ?? 'base'] ?? '15px';

        $hoverStyle = in_array($settings['hover_style'] ?? 'underline', ['none', 'underline', 'background', 'both'], true)
            ? $settings['hover_style']
            : 'underline';

        $jcMap = ['left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end'];
        $jc = $jcMap[$align];

        $vars = [
            '--mt:' . ($settings['text_color'] ?? '#111827'),
            '--mh:' . ($settings['hover_color'] ?? '#7c3aed'),
            '--ma:' . ($settings['active_color'] ?? '#7c3aed'),
            '--mbg:' . ($settings['dropdown_bg'] ?? '#ffffff'),
            '--mbd:' . (($settings['dropdown_border'] ?? 'yes') === 'yes' ? '1px solid #e5e7eb' : '0'),
            '--fs:' . $fontSize,
            '--fw:' . ($settings['font_weight'] ?? '500'),
            '--gap:' . $gap,
            '--dir:' . ($layout === 'vertical' ? 'column' : 'row'),
            '--ai:' . ($layout === 'vertical' ? $jc : 'center'),
            '--jc:' . ($layout === 'vertical' ? 'flex-start' : $jc),
            '--mjc:' . $jc,
        ];

        $wrapperStyles = [];

        $padding = $this->dimensionCss($settings, 'padding_dim');
        if ($padding !== '') {
            $wrapperStyles[] = 'padding:' . $padding;
        }

        $margin = $this->dimensionCss($settings, 'margin_dim');
        if ($margin !== '') {
            $wrapperStyles[] = 'margin:' . $margin;
        }

        $bg = $this->backgroundCss($settings);
        if ($bg !== '') {
            $wrapperStyles[] = rtrim($bg, ';');
        }

        $wrapperAttrs = [];

        if (!empty($wrapperStyles)) {
            $wrapperAttrs['style'] = implode(';', $wrapperStyles);
        }

        $id = $this->cssId($settings);
        if ($id !== '') {
            $wrapperAttrs['id'] = $id;
        }

        $class = $this->cssClass($settings);
        if ($class !== '') {
            $wrapperAttrs['class'] = $class;
        }

        $menuAttrs = [
            'class' => 'rj-menu',
            'data-hover' => $hoverStyle,
            'data-mobile' => $mobileBehavior,
            'data-align' => $align,
            'style' => implode(';', $vars),
        ];

        $itemsHtml = $this->renderItems($items);

        $desktop = '<nav class="rj-menu-d"><ul>' . $itemsHtml . '</ul></nav>';

        $toggle = "event.preventDefault();var m=this.closest('.rj-menu');var o=m.classList.toggle('rj-open');this.setAttribute('aria-expanded',o);";

        $mobile = '<nav class="rj-menu-m">'
            . '<div class="rj-bm">'
            . '<button type="button" class="rj-bb" aria-label="Menu" aria-expanded="false" onclick="' . $toggle . '">'
            . '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>'
            . '</button>'
            . '<ul class="rj-bl">' . $itemsHtml . '</ul>'
            . '</div>'
            . '</nav>';

        $html = '<div' . $this->attr($wrapperAttrs) . '>'
            . '<div' . $this->attr($menuAttrs) . '>'
            . $desktop
            . $mobile
            . '</div>'
            . '</div>';

        return $this->applyAnimation($html, $settings);
    }

    protected function buildTree(array $items, ?int $parentId = null): array
    {
        $tree = [];

        foreach ($items as $item) {
            $itemParent = $item->parent_id ?? null;

            if ($itemParent === $parentId) {
                $children = $this->buildTree($items, $item->id);

                $tree[] = [
                    'label' => $this->itemLabel($item),
                    'url' => $item->url ?: '#',
                    'target' => $item->target ?: '_self',
                    'children' => $children,
                ];
            }
        }

        return $tree;
    }

    protected function itemLabel($item): string
    {
        $label = $item->label ?? '';

        if (!empty($item->translations) && is_array($item->translations)) {
            $locale = app()->getLocale();

            if (!empty($item->translations[$locale]['label'])) {
                return $item->translations[$locale]['label'];
            }
        }

        return (string) $label;
    }

    protected function renderItems(array $items): string
    {
        $html = '';

        foreach ($items as $item) {
            $label = $this->escape($item['label']);
            $url = $this->escape($item['url']);
            $target = $this->escape($item['target']);
            $hasChildren = !empty($item['children']);

            $rel = '';
            if ($target === '_blank') {
                $rel = ' rel="noopener"';
            }

            $html .= '<li class="rj-mi">';
            $html .= '<a href="' . $url . '" target="' . $target . '"' . $rel . ' class="rj-ml">';
            $html .= $label;
            if ($hasChildren) {
                $html .= ' <span class="rj-car">▾</span>';
            }
            $html .= '</a>';

            if ($hasChildren) {
                $html .= '<ul class="rj-dd">' . $this->renderItems($item['children']) . '</ul>';
            }

            $html .= '</li>';
        }

        return $html;
    }
}