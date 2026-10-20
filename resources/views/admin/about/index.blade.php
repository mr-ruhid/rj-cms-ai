@extends('admin.layouts.app')

@section('title', __('RJ About'))

@section('content')
<style>
    .rj-doc-content h3 { font-size: 1.125rem; font-weight: 700; color: #111827; margin-top: 1.5rem; margin-bottom: 0.75rem; }
    .rj-doc-content p { font-size: 0.875rem; color: #4b5563; line-height: 1.7; margin-bottom: 1rem; }
    .rj-doc-content ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1rem; }
    .rj-doc-content li { font-size: 0.875rem; color: #4b5563; margin-bottom: 0.25rem; }
    .rj-doc-content pre { background: #111827; color: #e5e7eb; padding: 1rem; border-radius: 0.75rem; font-family: ui-monospace, monospace; font-size: 0.75rem; overflow-x: auto; margin-bottom: 1rem; }
    .rj-doc-content code { background: #f3f4f6; color: #7c3aed; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-family: ui-monospace, monospace; font-size: 0.8rem; }
    .rj-doc-content pre code { background: transparent; color: inherit; padding: 0; }
</style>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start pb-12">

    <aside class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rj-purple" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    {{ __('Documentation') }}
                </h2>
            </div>
            
            <nav class="p-2 space-y-0.5">
                @foreach($items as $item)
                    @php
                        $isActive = $activeItem && $activeItem->id === $item->id;
                        $icon = $categories[$item->slug]['icon'] ?? 'document';
                    @endphp
                    <a href="{{ url('/admin/about/' . $item->slug) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors {{ $isActive ? 'bg-rj-purple/10 text-rj-purple font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                        <div class="flex-shrink-0 {{ $isActive ? 'text-rj-purple' : 'text-gray-400' }}">
                            @switch($icon)
                                @case('info')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @break
                                @case('code')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                    @break
                                @case('palette')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                                    @break
                                @case('settings')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    @break
                                @case('puzzle')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                                    @break
                                @case('terminal')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    @break
                                @case('link')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                    @break
                                @case('star')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    @break
                                @case('shield')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    @break
                                @default
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @endswitch
                        </div>
                        <span class="truncate">{{ $item->title }}</span>
                    </a>
                @endforeach
            </nav>
        </div>
    </aside>

    <main class="lg:col-span-3 min-w-0">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden min-h-[500px]">
            @if($activeItem)
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/30 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-extrabold text-gray-900">{{ $activeItem->title }}</h1>
                        <p class="text-sm text-gray-500 mt-1">{{ __('Last updated') }}: {{ $activeItem->updated_at->format('M d, Y') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-white shadow-sm border border-gray-100 flex items-center justify-center text-rj-purple">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <div class="p-8 rj-doc-content">
                    {!! $activeItem->content !!}
                </div>
            @else
                <div class="p-16 text-center flex flex-col items-center justify-center h-full">
                    <div class="w-20 h-20 rounded-full bg-gray-50 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">{{ __('No documentation found') }}</h3>
                    <p class="text-sm text-gray-500 mt-2">{{ __('Run the seeder to populate the knowledge base.') }}</p>
                </div>
            @endif
        </div>
    </main>

</div>
@endsection