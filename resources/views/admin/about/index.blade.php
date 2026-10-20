@extends('admin.layouts.app')

@section('title', __('RJ About'))

@section('content')
@php
    $iconPaths = [
        'info'     => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'code'     => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
        'palette'  => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
        'settings' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
        'puzzle'   => 'M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z',
        'terminal' => 'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'link'     => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',
        'star'     => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
        'shield'   => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'document' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    ];

    $iconFor = fn ($slug) => $iconPaths[$categories[$slug]['icon'] ?? 'document'] ?? $iconPaths['document'];

    $itemsList = collect($items)->values();
    $prevItem = $nextItem = null;
    $content = '';
    $toc = [];
    $readMinutes = 1;

    if ($activeItem) {
        $index = $itemsList->search(fn ($i) => $i->id === $activeItem->id);
        if ($index !== false) {
            $prevItem = $itemsList->get($index - 1 < 0 ? -1 : $index - 1);
            $nextItem = $itemsList->get($index + 1);
        }

        $content = (string) $activeItem->content;

        // Literal "\n" sequences inside code blocks -> real line breaks
        $content = preg_replace_callback('/<pre\b[^>]*>.*?<\/pre>/is', fn ($m) => str_replace(['\\r\\n', '\\n'], "\n", $m[0]), $content);

        // Anchor ids on h2/h3 + table of contents
        $used = [];
        $content = preg_replace_callback('/<(h[23])([^>]*)>(.*?)<\/\1>/is', function ($m) use (&$toc, &$used) {
            $text = trim(strip_tags($m[3]));
            $slug = \Illuminate\Support\Str::slug($text) ?: 'section';
            $base = $slug;
            $n = 2;
            while (in_array($slug, $used, true)) {
                $slug = $base . '-' . $n++;
            }
            $used[] = $slug;
            $toc[] = ['id' => $slug, 'text' => $text];

            return '<' . $m[1] . ' id="' . $slug . '"' . $m[2] . '>' . $m[3] . '</' . $m[1] . '>';
        }, $content);

        $readMinutes = max(1, (int) ceil(str_word_count(strip_tags($content)) / 200));
    }
@endphp

<style>
    /* ---------- Layout (self-contained, independent of the Tailwind build) ---------- */
    .ra-wrap { display: grid; grid-template-columns: 300px minmax(0, 1fr); gap: 24px; align-items: start; padding-bottom: 48px; }
    @media (max-width: 1023px) { .ra-wrap { grid-template-columns: minmax(0, 1fr); } }

    .ra-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; box-shadow: 0 1px 2px rgba(0,0,0,.05); overflow: hidden; }
    .ra-side { position: sticky; top: 16px; }
    @media (max-width: 1023px) { .ra-side { position: static; } }

    .ra-side-head { display: flex; align-items: center; gap: 10px; padding: 16px 20px; border-bottom: 1px solid #f3f4f6; font-size: 14px; font-weight: 700; color: #111827; }
    .ra-side-head svg { width: 18px; height: 18px; }

    .ra-search { position: relative; padding: 12px 12px 4px; }
    .ra-search svg { position: absolute; left: 24px; top: 50%; margin-top: -2px; transform: translateY(-50%); width: 16px; height: 16px; color: #9ca3af; pointer-events: none; }
    .ra-search input { width: 100%; box-sizing: border-box; padding: 9px 12px 9px 36px; font-size: 13px; color: #111827; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; outline: none; transition: border-color .15s, background .15s; }
    .ra-search input:focus { background: #fff; border-color: #9ca3af; }

    .ra-nav { display: flex; flex-direction: column; gap: 2px; padding: 8px; max-height: calc(100vh - 210px); overflow-y: auto; }
    @media (max-width: 1023px) { .ra-nav { max-height: 340px; } }
    .ra-link { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 12px; font-size: 14px; font-weight: 500; color: #4b5563; text-decoration: none; transition: background .15s, color .15s; }
    .ra-link:hover { background: #f9fafb; color: #111827; }
    .ra-link svg { width: 18px; height: 18px; flex-shrink: 0; color: #9ca3af; transition: color .15s; }
    .ra-link span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ra-link.is-active { font-weight: 600; box-shadow: inset 3px 0 0 currentColor; }
    .ra-link.is-active svg { color: inherit; }
    .ra-empty-search { padding: 20px 12px; font-size: 13px; color: #9ca3af; text-align: center; }

    /* ---------- Article header ---------- */
    .ra-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; padding: 28px 40px 24px; border-bottom: 1px solid #f3f4f6; background: #fafafa; }
    @media (max-width: 640px) { .ra-head { padding: 22px 20px 18px; } }
    .ra-title { margin: 0; font-size: 28px; line-height: 1.2; font-weight: 800; letter-spacing: -.02em; color: #111827; }
    @media (max-width: 640px) { .ra-title { font-size: 22px; } }
    .ra-meta { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
    .ra-chip { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; font-size: 12px; font-weight: 500; color: #6b7280; background: #fff; border: 1px solid #e5e7eb; border-radius: 999px; }
    .ra-chip svg { width: 14px; height: 14px; color: #9ca3af; }
    .ra-icon { flex-shrink: 0; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 14px; background: #f3f4f6; background: color-mix(in srgb, currentColor 12%, transparent); }
    .ra-icon svg { width: 24px; height: 24px; }

    /* ---------- Table of contents ---------- */
    .ra-toc { padding: 16px 40px; border-bottom: 1px solid #f3f4f6; }
    @media (max-width: 640px) { .ra-toc { padding: 14px 20px; } }
    .ra-toc-label { margin-bottom: 8px; font-size: 12px; font-weight: 600; color: #9ca3af; }
    .ra-toc-list { display: flex; flex-wrap: wrap; gap: 8px; }
    .ra-toc-list a { padding: 5px 12px; font-size: 13px; font-weight: 500; color: #4b5563; background: #f3f4f6; border-radius: 999px; text-decoration: none; transition: background .15s, color .15s; }
    .ra-toc-list a:hover { background: #e5e7eb; color: #111827; }

    /* ---------- Article body ---------- */
    .ra-body { padding: 32px 40px 40px; }
    @media (max-width: 640px) { .ra-body { padding: 22px 20px 28px; } }

    .rj-doc-content { max-width: 820px; }
    .rj-doc-content > :first-child { margin-top: 0; }
    .rj-doc-content h2 { scroll-margin-top: 90px; font-size: 1.5rem; font-weight: 800; letter-spacing: -.01em; color: #111827; margin: 2.25rem 0 1rem; padding-bottom: .5rem; border-bottom: 1px solid #f3f4f6; }
    .rj-doc-content h3 { scroll-margin-top: 90px; font-size: 1.2rem; font-weight: 700; color: #111827; margin: 2rem 0 .75rem; }
    .rj-doc-content p { font-size: .9375rem; color: #4b5563; line-height: 1.8; margin: 0 0 1rem; }
    .rj-doc-content strong { color: #111827; font-weight: 600; }
    .rj-doc-content a { color: inherit; text-decoration: underline; text-underline-offset: 3px; }
    .rj-doc-content ul { list-style: disc; padding-left: 1.5rem; margin: 0 0 1.25rem; }
    .rj-doc-content ol { list-style: decimal; padding-left: 1.5rem; margin: 0 0 1.25rem; }
    .rj-doc-content li { font-size: .9375rem; color: #4b5563; line-height: 1.75; margin-bottom: .4rem; padding-left: .25rem; }
    .rj-doc-content li::marker { color: #9ca3af; }
    .rj-doc-content hr { border: 0; border-top: 1px solid #e5e7eb; margin: 2rem 0; }
    .rj-doc-content blockquote { margin: 0 0 1.25rem; padding: .75rem 1.25rem; border-left: 4px solid #d1d5db; background: #f9fafb; border-radius: 0 12px 12px 0; color: #4b5563; }
    .rj-doc-content blockquote p:last-child { margin-bottom: 0; }
    .rj-doc-content img { max-width: 100%; height: auto; border-radius: 12px; border: 1px solid #e5e7eb; margin: 1rem 0; }
    .rj-doc-content table { width: 100%; border-collapse: collapse; margin: 0 0 1.5rem; font-size: .875rem; display: block; overflow-x: auto; }
    .rj-doc-content th { text-align: left; font-weight: 600; color: #374151; background: #f9fafb; padding: 10px 14px; border: 1px solid #e5e7eb; white-space: nowrap; }
    .rj-doc-content td { padding: 10px 14px; color: #4b5563; border: 1px solid #e5e7eb; vertical-align: top; }

    .rj-doc-content code { background: #f3f4f6; color: #be185d; padding: .125rem .4rem; border-radius: .375rem; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: .8125rem; word-break: break-word; }
    .rj-doc-content pre { background: #111827; color: #e5e7eb; padding: 1.125rem 1.25rem; border-radius: .875rem; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: .8125rem; line-height: 1.7; overflow-x: auto; margin: 0 0 1.25rem; white-space: pre; border: 1px solid #1f2937; }
    .rj-doc-content pre code { background: transparent; color: inherit; padding: 0; font-size: inherit; word-break: normal; }

    .ra-code { position: relative; margin-bottom: 1.25rem; }
    .ra-code pre { margin-bottom: 0; padding-right: 5.5rem; }
    .ra-copy { position: absolute; top: 10px; right: 10px; padding: 4px 10px; font-size: 11px; font-weight: 600; color: #d1d5db; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12); border-radius: 8px; cursor: pointer; transition: background .15s, color .15s; }
    .ra-copy:hover { background: rgba(255,255,255,.16); color: #fff; }

    /* ---------- Prev / Next ---------- */
    .ra-pager { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding: 20px 40px 32px; border-top: 1px solid #f3f4f6; }
    @media (max-width: 640px) { .ra-pager { grid-template-columns: 1fr; padding: 16px 20px 24px; } }
    .ra-pager a { display: block; padding: 14px 18px; border: 1px solid #e5e7eb; border-radius: 14px; text-decoration: none; transition: border-color .15s, background .15s; }
    .ra-pager a:hover { border-color: #9ca3af; background: #f9fafb; }
    .ra-pager small { display: block; font-size: 12px; font-weight: 500; color: #9ca3af; margin-bottom: 2px; }
    .ra-pager strong { display: block; font-size: 14px; font-weight: 600; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ra-pager .is-next { text-align: right; grid-column: 2; }
    @media (max-width: 640px) { .ra-pager .is-next { grid-column: 1; } }

    /* ---------- Empty state ---------- */
    .ra-none { padding: 80px 24px; text-align: center; }
    .ra-none-icon { width: 80px; height: 80px; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; border-radius: 999px; background: #f9fafb; }
    .ra-none-icon svg { width: 40px; height: 40px; color: #d1d5db; }
    .ra-none h3 { margin: 0; font-size: 18px; font-weight: 700; color: #111827; }
    .ra-none p { margin: 8px 0 0; font-size: 14px; color: #6b7280; }
</style>

<div class="ra-wrap">

    {{-- ============ SIDEBAR ============ --}}
    <aside class="ra-side" x-data="{
        q: '',
        titles: @js($itemsList->pluck('title')->map(fn ($t) => mb_strtolower($t))->values()),
        get found() {
            const s = this.q.trim().toLowerCase();
            return !s || this.titles.some(t => t.includes(s));
        }
    }">
        <div class="ra-card">
            <div class="ra-side-head">
                <svg class="text-rj-purple" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                {{ __('Documentation') }}
            </div>

            <div class="ra-search">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="search" x-model="q" placeholder="{{ __('Search docs...') }}" aria-label="{{ __('Search docs...') }}">
            </div>

            <nav class="ra-nav">
                @foreach ($itemsList as $item)
                    @php $isActive = $activeItem && $activeItem->id === $item->id; @endphp
                    <a href="{{ url('/admin/about/' . $item->slug) }}"
                       x-show="!q.trim() || @js(mb_strtolower($item->title)).includes(q.trim().toLowerCase())"
                       @if ($isActive) aria-current="page" @endif
                       class="ra-link {{ $isActive ? 'is-active text-rj-purple bg-rj-purple/10' : '' }}">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconFor($item->slug) }}"/></svg>
                        <span>{{ $item->title }}</span>
                    </a>
                @endforeach

                <div class="ra-empty-search" x-show="!found" x-cloak>
                    {{ __('Nothing found.') }}
                </div>
            </nav>
        </div>
    </aside>

    {{-- ============ ARTICLE ============ --}}
    <main style="min-width:0;">
        <div class="ra-card" style="min-height:500px;">
            @if ($activeItem)
                <div class="ra-head">
                    <div style="min-width:0;">
                        <h1 class="ra-title">{{ $activeItem->title }}</h1>
                        <div class="ra-meta">
                            <span class="ra-chip">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ __('Last updated') }}: {{ optional($activeItem->updated_at)->format('M d, Y') }}
                            </span>
                            <span class="ra-chip">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $readMinutes }} {{ __('min read') }}
                            </span>
                        </div>
                    </div>
                    <div class="ra-icon text-rj-purple">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconFor($activeItem->slug) }}"/></svg>
                    </div>
                </div>

                @if (count($toc) >= 2)
                    <div class="ra-toc">
                        <div class="ra-toc-label">{{ __('On this page') }}</div>
                        <div class="ra-toc-list">
                            @foreach ($toc as $entry)
                                <a href="#{{ $entry['id'] }}">{{ $entry['text'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="ra-body">
                    <div class="rj-doc-content">
                        {!! $content !!}
                    </div>
                </div>

                @if ($prevItem || $nextItem)
                    <div class="ra-pager">
                        @if ($prevItem)
                            <a href="{{ url('/admin/about/' . $prevItem->slug) }}">
                                <small>&larr; {{ __('Previous') }}</small>
                                <strong>{{ $prevItem->title }}</strong>
                            </a>
                        @endif
                        @if ($nextItem)
                            <a href="{{ url('/admin/about/' . $nextItem->slug) }}" class="is-next">
                                <small>{{ __('Next') }} &rarr;</small>
                                <strong>{{ $nextItem->title }}</strong>
                            </a>
                        @endif
                    </div>
                @endif
            @else
                <div class="ra-none">
                    <div class="ra-none-icon">
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3>{{ __('No documentation found') }}</h3>
                    <p>{{ __('Run the seeder to populate the knowledge base.') }}</p>
                </div>
            @endif
        </div>
    </main>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels = { copy: @js(__('Copy')), copied: @js(__('Copied')) };

    document.querySelectorAll('.rj-doc-content pre').forEach(function (pre) {
        const wrap = document.createElement('div');
        wrap.className = 'ra-code';
        pre.parentNode.insertBefore(wrap, pre);
        wrap.appendChild(pre);

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'ra-copy';
        btn.textContent = labels.copy;

        btn.addEventListener('click', async function () {
            const text = pre.innerText;
            let ok = false;

            try {
                await navigator.clipboard.writeText(text);
                ok = true;
            } catch (e) {
                const ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                try { ok = document.execCommand('copy'); } catch (_) { ok = false; }
                document.body.removeChild(ta);
            }

            if (ok) {
                btn.textContent = labels.copied;
                setTimeout(function () { btn.textContent = labels.copy; }, 1800);
            }
        });

        wrap.appendChild(btn);
    });
});
</script>
@endsection