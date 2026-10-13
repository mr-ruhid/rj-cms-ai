<div x-data="mediaPicker()" x-init="init()" @open-media-picker.window="open($event.detail)">

    <div x-show="isOpen" x-cloak class="fixed inset-y-0 right-0 z-[9999] flex items-center justify-center p-4"
         style="left: 16rem;"
         @keydown.escape.window="close()">

        <div x-show="isOpen" x-transition.opacity
             @click="close()"
             class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div x-show="isOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Media Library') }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ __('Select a file or upload a new one.') }}</p>
                </div>

                <button type="button" @click="close()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="px-6 py-3 border-b border-gray-100 flex items-center gap-3 flex-wrap flex-shrink-0">
                <div class="relative flex-1 min-w-[200px]">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model.debounce.300ms="search" @input="loadMedia()"
                           placeholder="{{ __('Search...') }}"
                           class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                </div>

                <select x-model="filter" @change="loadMedia()"
                        class="rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <option value="">{{ __('All types') }}</option>
                    <option value="image">{{ __('Images') }}</option>
                    <option value="video">{{ __('Videos') }}</option>
                    <option value="audio">{{ __('Audio') }}</option>
                    <option value="document">{{ __('Documents') }}</option>
                </select>

                <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    {{ __('Upload') }}
                    <input type="file" class="hidden" accept="image/*" @change="uploadFile($event)">
                </label>
            </div>

            <div class="flex-1 overflow-y-auto rj-scrollbar p-4">

                <div x-show="loading" class="py-12 text-center">
                    <svg class="w-8 h-8 mx-auto text-rj-purple animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                </div>

                <div x-show="!loading && items.length === 0" x-cloak class="py-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="mt-3 text-sm text-gray-500">{{ __('No media found.') }}</p>
                </div>

                <div x-show="!loading && items.length > 0" x-cloak
                     class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    <template x-for="item in items" :key="item.id">
                        <button type="button"
                                @click="select(item)"
                                :class="selected && selected.id === item.id ? 'ring-2 ring-rj-purple ring-offset-2' : 'hover:border-rj-purple'"
                                class="relative rounded-lg overflow-hidden border border-gray-200 transition text-left group">
                            <div class="aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
                                <template x-if="item.type === 'image'">
                                    <img :src="item.url" :alt="item.original_name" class="w-full h-full object-cover group-hover:scale-105 transition">
                                </template>
                                <template x-if="item.type !== 'image'">
                                    <div class="flex flex-col items-center justify-center p-3">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        <div class="text-[10px] text-gray-600 mt-2 truncate w-full text-center" x-text="item.original_name"></div>
                                    </div>
                                </template>
                            </div>

                            <div class="p-2 bg-white">
                                <div class="text-xs text-gray-900 truncate" x-text="item.original_name"></div>
                                <div class="text-[10px] text-gray-500 mt-0.5" x-text="item.size_human"></div>
                            </div>

                            <div x-show="selected && selected.id === item.id" x-cloak
                                 class="absolute top-2 right-2 w-6 h-6 rounded-full bg-rj-purple flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </button>
                    </template>
                </div>

                <div x-show="!loading && total > items.length" x-cloak class="mt-4 text-center">
                    <button type="button" @click="loadMore()"
                            class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50 transition">
                        {{ __('Load more') }}
                    </button>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between gap-4 flex-shrink-0">
                <div class="text-xs text-gray-500">
                    <span x-show="!selected">{{ __('No file selected.') }}</span>
                    <span x-show="selected" x-cloak class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="selected?.original_name"></span>
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" @click="close()"
                            class="px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50 transition">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" @click="confirm()" :disabled="!selected"
                            class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ __('Select') }}
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
function mediaPicker() {
    return {
        isOpen: false,
        target: null,
        search: '',
        filter: '',
        items: [],
        total: 0,
        page: 1,
        loading: false,
        selected: null,

        init() {},

        open(detail) {
            this.target = detail.value;
            this.selected = null;
            this.items = [];
            this.page = 1;
            this.isOpen = true;
            this.loadMedia();
        },

        close() {
            this.isOpen = false;
            this.selected = null;
        },

        async loadMedia(append = false) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: this.page,
                    search: this.search || '',
                    type: this.filter || '',
                });
                const res = await fetch(`{{ route('admin.media.picker') }}?${params}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.items = append ? [...this.items, ...data.items] : data.items;
                this.total = data.total;
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },

        loadMore() {
            this.page++;
            this.loadMedia(true);
        },

        select(item) {
            this.selected = item;
        },

        confirm() {
            if (!this.selected || !this.target) return;
            this.target.value = this.selected.url;
            window.dispatchEvent(new CustomEvent('media-selected', {
                detail: { url: this.selected.url, item: this.selected, target: this.target }
            }));
            this.close();
        },

        async uploadFile(event) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('files[]', file);

            this.loading = true;
            try {
                const res = await fetch(`{{ route('admin.media.store') }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData,
                });

                if (res.ok || res.redirected) {
                    this.page = 1;
                    await this.loadMedia();
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
                event.target.value = '';
            }
        }
    }
}
</script>