<div data-id="{{ $item->id }}" class="group">
    <div class="flex items-center gap-3 bg-white rounded-lg border border-gray-200 p-3 hover:border-rj-purple/40 transition"
         style="margin-left: {{ $depth * 24 }}px;">

        <span class="drag-handle cursor-grab active:cursor-grabbing text-gray-300 hover:text-gray-500 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16M4 16h16"/></svg>
        </span>

        <div class="w-8 h-8 rounded-lg bg-rj-purple/10 flex items-center justify-center flex-shrink-0 text-sm">
            {{ \App\Models\MenuItem::types()[$item->type]['icon'] ?? '🔗' }}
        </div>

        <div class="min-w-0 flex-1">
            <div class="text-sm font-medium text-gray-900 truncate">{{ $item->label }}</div>
            <div class="text-xs text-gray-500 mt-0.5 truncate font-mono">
                {{ $item->resolvedUrl() }}
                @if ($item->translations && count($item->translations) > 0)
                    · 🌐 {{ count($item->translations) }} {{ __('translation(s)') }}
                @endif
            </div>
        </div>

        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 uppercase font-medium flex-shrink-0">
            {{ $item->type }}
        </span>

        @if (!$item->is_active)
            <span class="text-xs px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-medium flex-shrink-0">
                {{ __('Hidden') }}
            </span>
        @endif

        <div class="flex items-center gap-1 flex-shrink-0 opacity-0 group-hover:opacity-100 transition">
            <button type="button"
                    onclick="toggleEdit({{ $item->id }})"
                    class="w-7 h-7 rounded-lg text-gray-500 hover:bg-rj-purple/10 hover:text-rj-purple flex items-center justify-center transition"
                    title="{{ __('Edit') }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </button>

            <form method="POST" action="{{ route('admin.menus.items.destroy', [$menu, $item]) }}"
                  onsubmit="return confirm('{{ __('Delete this item?') }}')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-7 h-7 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 flex items-center justify-center transition"
                        title="{{ __('Delete') }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                </button>
            </form>
        </div>
    </div>

    <div id="edit-item-{{ $item->id }}" class="hidden mt-2 mb-2 p-4 rounded-lg bg-gray-50 border border-gray-200" style="margin-left: {{ $depth * 24 + 24 }}px;">
        <form method="POST" action="{{ route('admin.menus.items.update', [$menu, $item]) }}" class="space-y-3">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Label') }}</label>
                    <input type="text" name="label" value="{{ $item->label }}" required
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                </div>

                @if ($item->type === 'custom')
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('URL') }}</label>
                        <input type="text" name="url" value="{{ $item->url }}"
                               class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono">
                    </div>
                @endif
            </div>

            @if ($item->type === 'custom' && count(rj_available_locales()) > 1)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2 border-t border-gray-200">
                    @foreach (rj_available_locales() as $locale)
                        @if ($locale === config('app.locale')) @continue @endif
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                {{ strtoupper($locale) }}
                            </label>
                            <input type="text" name="translations[{{ $locale }}]"
                                   value="{{ $item->translations[$locale] ?? '' }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs">
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Target') }}</label>
                    <select name="target" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                        <option value="_self" {{ $item->target === '_self' ? 'selected' : '' }}>{{ __('Same window') }}</option>
                        <option value="_blank" {{ $item->target === '_blank' ? 'selected' : '' }}>{{ __('New window') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('CSS Class') }}</label>
                    <input type="text" name="css_class" value="{{ $item->css_class }}"
                           class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs font-mono">
                </div>

                <label class="flex items-center gap-2 cursor-pointer mt-5">
                    <input type="checkbox" name="is_active" value="1" {{ $item->is_active ? 'checked' : '' }}
                           class="rounded border-gray-300 text-rj-purple focus:ring-rj-purple">
                    <span class="text-sm text-gray-700">{{ __('Active') }}</span>
                </label>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-900 text-white text-xs font-medium hover:bg-gray-800 transition">
                    {{ __('Save') }}
                </button>
                <button type="button" onclick="toggleEdit({{ $item->id }})" class="text-xs text-gray-500 hover:text-gray-700">
                    {{ __('Cancel') }}
                </button>
            </div>
        </form>
    </div>

    @if ($item->children->count() > 0)
        @foreach ($item->children as $child)
            @include('admin.menus.partials.item-row', ['item' => $child, 'depth' => $depth + 1])
        @endforeach
    @endif
</div>