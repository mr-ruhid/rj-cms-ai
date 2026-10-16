@php
    $seoPost = $post ?? $page ?? null;

    $siteName = rj_get_option('site_name', config('app.name'));
    $currentUrl = url()->current();

    if ($seoPost) {
        $metaTitle = $seoPost->seo_title ?: ($seoPost->title ?? $siteName);
        $metaDescription = $seoPost->seo_description ?: ($seoPost->excerpt ?: rj_get_option('site_tagline', ''));
        $metaImage = $seoPost->seo_image_url;
        $canonical = $seoPost->canonical_url_full;
        $robots = $seoPost->robots;
        $ogTitle = $seoPost->og_title ?: $metaTitle;
        $ogDescription = $seoPost->og_description ?: $metaDescription;
        $ogImage = $seoPost->og_image_url ?: $metaImage;
        $twitterCard = $seoPost->twitter_card ?: 'summary_large_image';
        $twitterTitle = $seoPost->twitter_title ?: $ogTitle;
        $twitterDescription = $seoPost->twitter_description ?: $ogDescription;
        $twitterImage = $seoPost->twitter_image_url ?: $ogImage;
        $schemaType = $seoPost->schema_type ?: 'Article';
        $postType = $seoPost->type === 'page' ? 'website' : 'article';
    } else {
        $metaTitle = $title ?? $siteName;
        $metaDescription = rj_get_option('site_tagline', '');
        $metaImage = rj_get_option('site_logo') ? asset(rj_get_option('site_logo')) : null;
        $canonical = $currentUrl;
        $robots = 'index,follow';
        $ogTitle = $metaTitle;
        $ogDescription = $metaDescription;
        $ogImage = $metaImage;
        $twitterCard = 'summary_large_image';
        $twitterTitle = $ogTitle;
        $twitterDescription = $ogDescription;
        $twitterImage = $ogImage;
        $schemaType = 'WebSite';
        $postType = 'website';
    }

    $defaultImage = rj_get_option('seo_default_image');
    if (!$metaImage && $defaultImage) {
        $metaImage = \Illuminate\Support\Str::startsWith($defaultImage, 'http') ? $defaultImage : asset($defaultImage);
        $ogImage = $ogImage ?: $metaImage;
        $twitterImage = $twitterImage ?: $metaImage;
    }
@endphp

<title>{{ $metaTitle }}{{ $metaTitle !== $siteName ? ' — ' . $siteName : '' }}</title>

@if($metaDescription)
    <meta name="description" content="{{ Str::limit(strip_tags($metaDescription), 160) }}">
@endif

@if($robots)
    <meta name="robots" content="{{ $robots }}">
@endif

@if($canonical)
    <link rel="canonical" href="{{ $canonical }}">
@endif

<meta property="og:type" content="{{ $postType }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $ogTitle }}">
@if($ogDescription)
    <meta property="og:description" content="{{ Str::limit(strip_tags($ogDescription), 200) }}">
@endif
<meta property="og:url" content="{{ $canonical ?: $currentUrl }}">
@if($ogImage)
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
@endif

@if($seoPost && $seoPost->published_at)
    <meta property="article:published_time" content="{{ $seoPost->published_at->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $seoPost->updated_at->toIso8601String() }}">
    @if($seoPost->author)
        <meta property="article:author" content="{{ $seoPost->author->name }}">
    @endif
@endif

<meta name="twitter:card" content="{{ $twitterCard }}">
<meta name="twitter:title" content="{{ $twitterTitle }}">
@if($twitterDescription)
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($twitterDescription), 200) }}">
@endif
@if($twitterImage)
    <meta name="twitter:image" content="{{ $twitterImage }}">
@endif

@if($siteTwitter = rj_get_option('social_twitter'))
    <meta name="twitter:site" content="{{ '@' . ltrim($siteTwitter, '@') }}">
@endif

@if($seoPost)
    @php
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $schemaType,
            'headline' => Str::limit($seoPost->title, 110),
            'url' => $canonical ?: $currentUrl,
        ];

        if ($metaDescription) {
            $schema['description'] = Str::limit(strip_tags($metaDescription), 200);
        }

        if ($metaImage) {
            $schema['image'] = $metaImage;
        }

        if ($seoPost->published_at) {
            $schema['datePublished'] = $seoPost->published_at->toIso8601String();
            $schema['dateModified'] = $seoPost->updated_at->toIso8601String();
        }

        if ($seoPost->author) {
            $schema['author'] = [
                '@type' => 'Person',
                'name' => $seoPost->author->name,
            ];
        }

        $schema['publisher'] = [
            '@type' => 'Organization',
            'name' => $siteName,
        ];

        if ($logo = rj_get_option('site_logo')) {
            $schema['publisher']['logo'] = [
                '@type' => 'ImageObject',
                'url' => asset($logo),
            ];
        }
    @endphp
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif