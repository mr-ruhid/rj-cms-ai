<?php

namespace App\Widgets\Builtin;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Widgets\BaseWidget;

class PostsWidget extends BaseWidget
{
    public function id(): string
    {
        return 'posts';
    }

    public function name(): string
    {
        return __('Posts');
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>';
    }

    public function category(): string
    {
        return 'content';
    }

    public function description(): string
    {
        return __('Display a grid or list of posts.');
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'post_type',
                'type' => 'post_type',
                'label' => __('Post Type'),
                'default' => 'post',
                'tab' => 'content',
            ],
            [
                'name' => 'layout',
                'type' => 'select',
                'label' => __('Layout'),
                'default' => 'grid',
                'tab' => 'content',
                'options' => [
                    'grid' => __('Grid'),
                    'list' => __('List'),
                    'masonry' => __('Masonry'),
                    'carousel' => __('Carousel'),
                ],
            ],
            [
                'name' => 'columns',
                'type' => 'select',
                'label' => __('Columns'),
                'default' => '3',
                'tab' => 'content',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
            ],
            [
                'name' => 'limit',
                'type' => 'number',
                'label' => __('Number of Posts'),
                'default' => 6,
                'min' => 1,
                'max' => 30,
                'tab' => 'content',
            ],
            [
                'name' => 'order_by',
                'type' => 'select',
                'label' => __('Order By'),
                'default' => 'published_at',
                'tab' => 'content',
                'options' => [
                    'published_at' => __('Published Date'),
                    'created_at' => __('Created Date'),
                    'title' => __('Title'),
                    'view_count' => __('View Count'),
                    'random' => __('Random'),
                ],
            ],
            [
                'name' => 'order',
                'type' => 'select',
                'label' => __('Order'),
                'default' => 'desc',
                'tab' => 'content',
                'options' => [
                    'desc' => __('Descending'),
                    'asc' => __('Ascending'),
                ],
            ],
            [
                'name' => 'categories',
                'type' => 'terms',
                'label' => __('Categories'),
                'default' => '',
                'tab' => 'content',
                'taxonomy' => 'category',
            ],
            [
                'name' => 'tags',
                'type' => 'terms',
                'label' => __('Tags'),
                'default' => '',
                'tab' => 'content',
                'taxonomy' => 'tag',
            ],
            [
                'name' => 'exclude_ids',
                'type' => 'text',
                'label' => __('Exclude Post IDs'),
                'default' => '',
                'tab' => 'content',
                'placeholder' => '4,5,6',
            ],
            [
                'name' => 'show_image',
                'type' => 'toggle',
                'label' => __('Show Featured Image'),
                'default' => true,
                'tab' => 'content',
            ],
            [
                'name' => 'image_ratio',
                'type' => 'select',
                'label' => __('Image Ratio'),
                'default' => '16/9',
                'tab' => 'style',
                'options' => [
                    '16/9' => '16:9',
                    '4/3' => '4:3',
                    '1/1' => '1:1',
                    '3/2' => '3:2',
                    'auto' => __('Auto'),
                ],
            ],
            [
                'name' => 'show_excerpt',
                'type' => 'toggle',
                'label' => __('Show Excerpt'),
                'default' => true,
                'tab' => 'content',
            ],
            [
                'name' => 'excerpt_length',
                'type' => 'number',
                'label' => __('Excerpt Length (words)'),
                'default' => 20,
                'min' => 5,
                'max' => 60,
                'tab' => 'content',
            ],
            [
                'name' => 'show_date',
                'type' => 'toggle',
                'label' => __('Show Date'),
                'default' => true,
                'tab' => 'content',
            ],
            [
                'name' => 'show_author',
                'type' => 'toggle',
                'label' => __('Show Author'),
                'default' => false,
                'tab' => 'content',
            ],
            [
                'name' => 'show_categories',
                'type' => 'toggle',
                'label' => __('Show Categories'),
                'default' => false,
                'tab' => 'content',
            ],
            [
                'name' => 'show_read_more',
                'type' => 'toggle',
                'label' => __('Show Read More'),
                'default' => false,
                'tab' => 'content',
            ],
            [
                'name' => 'read_more_text',
                'type' => 'text',
                'label' => __('Read More Text'),
                'default' => 'Read more',
                'tab' => 'content',
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
                'name' => 'card_style',
                'type' => 'select',
                'label' => __('Card Style'),
                'default' => 'default',
                'tab' => 'style',
                'options' => [
                    'default' => __('Default'),
                    'bordered' => __('Bordered'),
                    'shadow' => __('Shadow'),
                    'minimal' => __('Minimal'),
                ],
            ],
            [
                'name' => 'card_bg',
                'type' => 'color',
                'label' => __('Card Background'),
                'default' => '#ffffff',
                'tab' => 'style',
            ],
            [
                'name' => 'card_radius',
                'type' => 'select',
                'label' => __('Card Radius'),
                'default' => 'md',
                'tab' => 'style',
                'options' => [
                    'none' => __('None'),
                    'sm' => '4px',
                    'md' => '8px',
                    'lg' => '16px',
                ],
            ],
            [
                'name' => 'title_color',
                'type' => 'color',
                'label' => __('Title Color'),
                'default' => '#111827',
                'tab' => 'style',
            ],
            [
                'name' => 'title_size',
                'type' => 'select',
                'label' => __('Title Size'),
                'default' => 'lg',
                'tab' => 'style',
                'options' => [
                    'sm' => __('Small'),
                    'base' => __('Normal'),
                    'lg' => __('Large'),
                    'xl' => __('Extra Large'),
                ],
            ],
            [
                'name' => 'text_color',
                'type' => 'color',
                'label' => __('Text Color'),
                'default' => '#6b7280',
                'tab' => 'style',
            ],
            [
                'name' => 'meta_color',
                'type' => 'color',
                'label' => __('Meta Color'),
                'default' => '#9ca3af',
                'tab' => 'style',
            ],
            [
                'name' => 'hover_lift',
                'type' => 'toggle',
                'label' => __('Lift on Hover'),
                'default' => true,
                'tab' => 'style',
            ],
            [
                'name' => 'pagination',
                'type' => 'select',
                'label' => __('Pagination'),
                'default' => 'none',
                'tab' => 'content',
                'options' => [
                    'none' => __('None'),
                    'load_more' => __('Load More'),
                    'numbers' => __('Page Numbers'),
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
        $query = $this->buildQuery($settings);

        $limit = max(1, (int) ($settings['limit'] ?? 6));
        $posts = $query->limit($limit)->get();

        if ($posts->isEmpty()) {
            return $this->renderEmpty($settings);
        }

        $layout = $settings['layout'] ?? 'grid';
        $columns = (int) ($settings['columns'] ?? 3);

        $gapMap = ['none' => '0', 'sm' => '12px', 'md' => '20px', 'lg' => '32px', 'xl' => '48px'];
        $gap = $gapMap[$settings['gap'] ?? 'md'] ?? '20px';

        $radiusMap = ['none' => '0', 'sm' => '4px', 'md' => '8px', 'lg' => '16px'];
        $radius = $radiusMap[$settings['card_radius'] ?? 'md'] ?? '8px';

        $titleSizes = ['sm' => '14px', 'base' => '16px', 'lg' => '18px', 'xl' => '22px'];
        $titleSize = $titleSizes[$settings['title_size'] ?? 'lg'] ?? '18px';

        $wrapperStyles = [
            '--rj-posts-gap:' . $gap,
            '--rj-posts-radius:' . $radius,
            '--rj-posts-bg:' . ($settings['card_bg'] ?? '#ffffff'),
            '--rj-posts-title-color:' . ($settings['title_color'] ?? '#111827'),
            '--rj-posts-title-size:' . $titleSize,
            '--rj-posts-text-color:' . ($settings['text_color'] ?? '#6b7280'),
            '--rj-posts-meta-color:' . ($settings['meta_color'] ?? '#9ca3af'),
        ];

        $padding = $this->dimensionCss($settings, 'padding_dim');
        if ($padding !== '') $wrapperStyles[] = 'padding:' . $padding;

        $margin = $this->dimensionCss($settings, 'margin_dim');
        if ($margin !== '') $wrapperStyles[] = 'margin:' . $margin;

        $bg = $this->backgroundCss($settings);
        if ($bg !== '') $wrapperStyles[] = rtrim($bg, ';');

        $wrapperAttrs = [
            'class' => 'rj-posts rj-posts-' . $layout . ' rj-posts-cols-' . $columns . ' rj-posts-card-' . ($settings['card_style'] ?? 'default'),
            'style' => implode(';', $wrapperStyles),
        ];

        if (!empty($settings['hover_lift'])) {
            $wrapperAttrs['class'] .= ' rj-posts-hover-lift';
        }

        $id = $this->cssId($settings);
        if ($id !== '') $wrapperAttrs['id'] = $id;

        $class = $this->cssClass($settings);
        if ($class !== '') $wrapperAttrs['class'] .= ' ' . $class;

        $itemsHtml = '';

        foreach ($posts as $post) {
            $itemsHtml .= $this->renderPostCard($post, $settings);
        }

        $pagination = $settings['pagination'] ?? 'none';
        $paginationHtml = '';

        if ($pagination === 'load_more') {
            $paginationHtml = '<div class="rj-posts-loadmore"><button type="button" class="rj-posts-btn-load" data-post-type="' . e($settings['post_type'] ?? 'post') . '">' . __('Load More') . '</button></div>';
        } elseif ($pagination === 'numbers') {
            $paginationHtml = '<div class="rj-posts-pagination"></div>';
        }

        $html = '<div' . $this->attr($wrapperAttrs) . '>'
            . '<div class="rj-posts-grid">' . $itemsHtml . '</div>'
            . $paginationHtml
            . '</div>';

        return $this->applyAnimation($html, $settings);
    }

    protected function buildQuery(array $settings)
    {
        $postType = $settings['post_type'] ?? 'post';
        $orderBy = $settings['order_by'] ?? 'published_at';
        $order = strtolower($settings['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $query = Post::query()
            ->with(['author', 'categories', 'tags'])
            ->where('type', $postType)
            ->published();

        if ($orderBy === 'random') {
            $query->inRandomOrder();
        } else {
            $query->orderBy($orderBy, $order);
        }

        if (!empty($settings['categories'])) {
            $ids = array_filter(array_map('intval', explode(',', (string) $settings['categories'])));

            if (!empty($ids)) {
                $query->whereHas('categories', fn($q) => $q->whereIn('rj_categories.id', $ids));
            }
        }

        if (!empty($settings['tags'])) {
            $ids = array_filter(array_map('intval', explode(',', (string) $settings['tags'])));

            if (!empty($ids)) {
                $query->whereHas('tags', fn($q) => $q->whereIn('rj_tags.id', $ids));
            }
        }

        if (!empty($settings['exclude_ids'])) {
            $ids = array_filter(array_map('intval', explode(',', (string) $settings['exclude_ids'])));

            if (!empty($ids)) {
                $query->whereNotIn('id', $ids);
            }
        }

        return $query;
    }

    protected function renderPostCard(Post $post, array $settings): string
    {
        $url = url('/blog/' . $post->slug);
        $showImage = !empty($settings['show_image']);
        $image = $post->featured_image;
        $ratio = $settings['image_ratio'] ?? '16/9';

        $cardStyles = [];
        if ($ratio !== 'auto') {
            $cardStyles[] = '--rj-post-ratio:' . $ratio;
        }

        $html = '<article class="rj-post-card" style="' . implode(';', $cardStyles) . '">';

        if ($showImage && $image) {
            $html .= '<a href="' . e($url) . '" class="rj-post-image">';
            $html .= '<img src="' . e($image) . '" alt="' . e($post->title) . '" loading="lazy">';
            $html .= '</a>';
        }

        $html .= '<div class="rj-post-body">';

        if (!empty($settings['show_categories']) && $post->categories->isNotEmpty()) {
            $cats = $post->categories->take(2);
            $html .= '<div class="rj-post-cats">';

            foreach ($cats as $cat) {
                $html .= '<span class="rj-post-cat">' . e($cat->name) . '</span>';
            }

            $html .= '</div>';
        }

        $html .= '<h3 class="rj-post-title"><a href="' . e($url) . '">' . e($post->title) . '</a></h3>';

        if (!empty($settings['show_excerpt'])) {
            $length = max(5, (int) ($settings['excerpt_length'] ?? 20));
            $excerpt = \Illuminate\Support\Str::words(strip_tags((string) $post->excerpt), $length, '...');
            $html .= '<p class="rj-post-excerpt">' . e($excerpt) . '</p>';
        }

        $meta = [];

        if (!empty($settings['show_date']) && $post->published_at) {
            $meta[] = '<span class="rj-post-meta-item">' . e($post->published_at->format('M j, Y')) . '</span>';
        }

        if (!empty($settings['show_author']) && $post->author) {
            $meta[] = '<span class="rj-post-meta-item">' . e($post->author->name) . '</span>';
        }

        if (!empty($meta)) {
            $html .= '<div class="rj-post-meta">' . implode('<span class="rj-post-meta-sep">·</span>', $meta) . '</div>';
        }

        if (!empty($settings['show_read_more'])) {
            $label = $settings['read_more_text'] ?? __('Read more');
            $html .= '<a href="' . e($url) . '" class="rj-post-read-more">' . e($label) . ' →</a>';
        }

        $html .= '</div></article>';

        return $html;
    }

    protected function renderEmpty(array $settings): string
    {
        return '<div class="rj-posts rj-posts-empty">'
            . '<div style="padding:32px 20px; text-align:center; color:#9ca3af; font-size:13px; border:1px dashed #e5e7eb; border-radius:8px;">'
            . '<svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="display:block; margin:0 auto 8px;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 12h6M9 16h4"/></svg>'
            . e(__('No posts found.'))
            . '</div></div>';
    }
}