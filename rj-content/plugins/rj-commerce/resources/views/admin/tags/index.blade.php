@extends('admin.layouts.app')

@section('title', __('Product Tags'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="tagManager()">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Product Tags') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage tags to help customers find products.') }}</p>
        </div>
        <button @click="openCreate()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('Add Tag') }}
        </button>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">{{ __('Name') }}</th>
                        <th class="px-6 py-4">{{ __('Slug') }}</th>
                        <th class="px-6 py-4">{{ __('Translations') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tags as $tag)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">
                                    {{ $tag->translation()?->name ?? __('(No Name)') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $tag->slug }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($tag->translations as $trans)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600 border border-gray-200">
                                            {{ $trans->locale }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEdit({{ $tag->toJson() }})" class="p-2 rounded-lg text-gray-400 hover:text-rj-purple hover:bg-rj-purple/10 transition" title="{{ __('Edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.commerce.tags.destroy', $tag) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this tag?') }}');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="{{ __('Delete') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                </div>
                                <h3 class="text-base font-semibold text-gray-900">{{ __('No tags yet') }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ __('Get started by creating your first product tag.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tags->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $tags->links() }}
            </div>
        @endif
    </div>

    <!-- Modal (Create / Edit) -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div x-show="showModal" x-transition.opacity class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showModal = false"></div>

        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900" x-text="isEditing ? '{{ __('Edit Tag') }}' : '{{ __('Add New Tag') }}'"></h3>
                <button @click="showModal = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="isEditing ? '/admin/commerce/tags/' + form.id : '{{ route('admin.commerce.tags.store') }}'" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <template x-if="isEditing">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Slug') }}</label>
                        <input type="text" name="slug" x-model="form.slug" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm font-mono" placeholder="{{ __('Leave empty to generate from name') }}">
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-sm font-semibold text-gray-900 border-b border-gray-100 pb-2">{{ __('Translations') }}</h4>

                        <template x-for="locale in locales" :key="locale">
                            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                                <div class="w-16 flex-shrink-0">
                                    <span class="inline-flex items-center justify-center px-2 py-1 rounded bg-gray-100 text-gray-600 text-xs font-bold uppercase tracking-wider border border-gray-200 w-full" x-text="locale"></span>
                                </div>
                                <div class="flex-1 w-full">
                                    <input type="text" :name="`translations[${locale}][name]`" x-model="form.translations[locale].name" class="w-full rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm" placeholder="{{ __('Tag name') }}">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-200 transition">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-bold hover:opacity-90 transition shadow-md">
                        <span x-text="isEditing ? '{{ __('Update Tag') }}' : '{{ __('Create Tag') }}'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function tagManager() {
    return {
        showModal: false,
        isEditing: false,
        locales: @json($locales),
        form: {
            id: null,
            slug: '',
            translations: {}
        },
        init() {
            this.resetForm();
        },
        resetForm() {
            this.form = {
                id: null,
                slug: '',
                translations: {}
            };
            this.locales.forEach(locale => {
                this.form.translations[locale] = { name: '' };
            });
        },
        openCreate() {
            this.isEditing = false;
            this.resetForm();
            this.showModal = true;
        },
        openEdit(tag) {
            this.isEditing = true;
            this.form.id = tag.id;
            this.form.slug = tag.slug;

            this.locales.forEach(locale => {
                const trans = tag.translations.find(t => t.locale === locale);
                this.form.translations[locale] = { name: trans ? trans.name : '' };
            });

            this.showModal = true;
        }
    }
}
</script>
@endsection
