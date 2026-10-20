@extends('admin.layouts.app')

@section('title', __('Edit Menu'))

@section('content')
<style>
    [x-cloak]{display:none!important}
    .no-scrollbar::-webkit-scrollbar{display:none}
    .rjm{--p:var(--rj-purple,#7c3aed);--k:var(--rj-pink,#ec4899);--p-soft:color-mix(in srgb,var(--p) 10%,white);--p-faint:color-mix(in srgb,var(--p) 5%,white);--p-line:color-mix(in srgb,var(--p) 30%,white);--p-ring:color-mix(in srgb,var(--p) 18%,transparent);--p-glow:color-mix(in srgb,var(--p) 30%,transparent);--ink:#111827;--mut:#6b7280;--line:#e5e7eb;--soft:#f9fafb;display:flex;flex-direction:column;gap:24px;color:var(--ink)}
    .rjm *{box-sizing:border-box}
    .rjm svg{flex-shrink:0}

    /* top bar */
    .rjm-top{display:flex;align-items:center;gap:14px;flex-wrap:wrap}
    .rjm-back{width:42px;height:42px;border-radius:50%;background:#fff;border:1px solid var(--line);display:flex;align-items:center;justify-content:center;color:var(--mut);box-shadow:0 1px 2px rgba(0,0,0,.05);transition:.15s}
    .rjm-back:hover{color:var(--p);border-color:var(--p-line);transform:translateX(-2px)}
    .rjm-top h2{margin:0;font-size:22px;font-weight:800;letter-spacing:-.02em}
    .rjm-top p{margin:3px 0 0;font-size:13px;color:var(--mut)}
    .rjm-pill{display:inline-flex;align-items:center;gap:6px;background:var(--p-soft);color:var(--p);font-size:12px;font-weight:700;padding:3px 10px;border-radius:999px;margin-left:6px}

    .rjm-alert{padding:12px 16px;border-radius:12px;border:1px solid #fecaca;background:#fef2f2;color:#991b1b;font-size:14px}
    .rjm-alert ul{margin:0;padding-left:18px}

    .rjm-grid{display:grid;grid-template-columns:minmax(0,2fr) minmax(0,1fr);gap:24px;align-items:start}
    .rjm-col{display:flex;flex-direction:column;gap:24px;min-width:0}
    @@media (max-width:1023px){.rjm-grid{grid-template-columns:minmax(0,1fr)}}

    /* cards */
    .rjm-card{background:#fff;border:1px solid var(--line);border-radius:20px;box-shadow:0 1px 2px rgba(0,0,0,.04);overflow:hidden}
    .rjm-ch{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px 24px;border-bottom:1px solid #f3f4f6;background:linear-gradient(180deg,var(--p-faint),#fff)}
    .rjm-ch h3{margin:0;font-size:16px;font-weight:700}
    .rjm-ch p{margin:3px 0 0;font-size:13px;color:var(--mut)}
    .rjm-cb{padding:24px;display:flex;flex-direction:column;gap:18px}
    .rjm-count{background:#fff;border:1px solid var(--line);color:#4b5563;font-size:12px;font-weight:700;padding:4px 12px;border-radius:999px;white-space:nowrap}

    /* type tabs */
    .rjm-tabs{display:flex;gap:6px;padding:12px 16px;overflow-x:auto;scrollbar-width:none;-ms-overflow-style:none;border-bottom:1px solid #f3f4f6;user-select:none}
    .rjm-tab{display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border-radius:10px;font-size:13px;font-weight:600;color:var(--mut);background:transparent;border:1px solid transparent;white-space:nowrap;flex-shrink:0;transition:.15s;cursor:pointer;font-family:inherit}
    .rjm-tab svg{width:16px;height:16px}
    .rjm-tab:hover{background:var(--soft);color:#374151}
    .rjm-tab.on{background:var(--p-soft);color:var(--p);border-color:var(--p-line)}

    /* form controls */
    .rjm label.lb{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px}
    .rjm label.lb em{color:#ef4444;font-style:normal}
    .rjm input[type=text],.rjm select{width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:12px;font-size:14px;background:#fff;color:var(--ink);outline:none;transition:.15s;font-family:inherit}
    .rjm input[type=text]:focus,.rjm select:focus{border-color:var(--p);box-shadow:0 0 0 3px var(--p-ring)}
    .rjm input.mono{font-family:ui-monospace,Menlo,Consolas,monospace;background:var(--soft)}
    .rjm .err{font-size:12px;color:#ef4444;margin:4px 0 0}
    .rjm .hint{font-size:12px;color:var(--mut);margin:6px 0 0}
    .rjm-2{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px}
    .rjm-stack{display:flex;flex-direction:column;gap:16px}

    .rjm-trans-btn{display:inline-flex;align-items:center;gap:6px;border:0;background:transparent;color:var(--p);font-size:12px;font-weight:700;padding:0;cursor:pointer;font-family:inherit}
    .rjm-trans-btn:hover{color:var(--k)}
    .rjm-trans{margin-top:12px;padding-left:14px;border-left:2px solid var(--p-line);display:flex;flex-direction:column;gap:12px}
    .rjm-trans label{display:block;font-size:12px;font-weight:600;color:#4b5563;margin-bottom:4px}
    .rjm-trans label small{color:#9ca3af;font-weight:400;text-transform:uppercase}

    .rjm-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px 20px;border:0;border-radius:12px;font-size:14px;font-weight:700;color:#fff;background:linear-gradient(90deg,var(--p),var(--k));box-shadow:0 4px 12px var(--p-glow);cursor:pointer;transition:.15s;font-family:inherit}
    .rjm-btn:hover{opacity:.92;transform:translateY(-1px)}
    .rjm-btn:active{transform:scale(.99)}

    /* switch */
    .rjm-sw{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:14px 16px;border:1px solid var(--line);border-radius:14px;cursor:pointer;transition:.15s;background:#fff}
    .rjm-sw:hover{border-color:var(--p-line)}
    .rjm-sw.on{background:var(--p-faint);border-color:var(--p-line)}
    .rjm-sw b{display:block;font-size:14px}
    .rjm-sw span.d{display:block;font-size:12px;color:var(--mut);margin-top:2px}
    .rjm-sw input{position:absolute;opacity:0;width:0;height:0}
    .rjm-track{position:relative;width:46px;height:26px;border-radius:999px;background:#d1d5db;flex-shrink:0;transition:background .2s}
    .rjm-track:after{content:"";position:absolute;top:3px;left:3px;width:20px;height:20px;border-radius:50%;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.3);transition:transform .2s}
    .rjm-sw.on .rjm-track{background:var(--p)}
    .rjm-sw.on .rjm-track:after{transform:translateX(20px)}
    .rjm-sw input:focus-visible + .rjm-track{box-shadow:0 0 0 4px var(--p-ring)}
    .rjm-st{display:inline-block;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;padding:2px 8px;border-radius:999px;margin-left:8px;background:#f3f4f6;color:#6b7280}
    .rjm-sw.on .rjm-st{background:#d1fae5;color:#047857}

    /* structure */
    .rjm-tree{padding:18px;background:var(--soft)}
    .rjm-tree #menu-tree{display:flex;flex-direction:column;gap:8px}
    .rjm-empty{padding:52px 16px;text-align:center}
    .rjm-empty .ic{width:64px;height:64px;border-radius:50%;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;background:var(--p-soft);color:var(--p)}
    .rjm-empty b{display:block;font-size:14px}
    .rjm-empty span{font-size:12px;color:var(--mut)}
</style>

<div class="rjm" x-data="menuEditor()">

    {{-- TOP --}}
    <div class="rjm-top">
        <a href="{{ route('admin.menus.index') }}" class="rjm-back" aria-label="{{ __('Back') }}">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2>{{ $menu->name }}</h2>
            <p>{{ $menu->locationLabel() }} · {{ $menu->itemCount() }} {{ __('item(s)') }}</p>
        </div>
    </div>

    @if (session('error'))
        <div class="rjm-alert">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="rjm-alert">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="rjm-grid">

        {{-- ============ LEFT ============ --}}
        <div class="rjm-col">

            {{-- Add item --}}
            <div class="rjm-card">
                <div class="rjm-ch">
                    <div>
                        <h3>{{ __('Add Menu Item') }}</h3>
                        <p>{{ __('Choose a type and add it to the menu.') }}</p>
                    </div>
                </div>

                <div x-data="{
                         isDragging: false, isMoved: false, startX: 0, scrollLeft: 0,
                         scrollTabs(e) { if (e.deltaY !== 0) { this.$refs.tabContainer.scrollLeft += e.deltaY; e.preventDefault(); } },
                         startDrag(e) { this.isDragging = true; this.isMoved = false; this.startX = e.pageX - this.$refs.tabContainer.offsetLeft; this.scrollLeft = this.$refs.tabContainer.scrollLeft; },
                         stopDrag() { this.isDragging = false; },
                         doDrag(e) { if (!this.isDragging) return; const x = e.pageX - this.$refs.tabContainer.offsetLeft; const walk = (x - this.startX) * 2; if (Math.abs(x - this.startX) > 5) { this.isMoved = true; e.preventDefault(); } this.$refs.tabContainer.scrollLeft = this.scrollLeft - walk; }
                     }">
                    <div x-ref="tabContainer" class="rjm-tabs no-scrollbar"
                         @wheel="scrollTabs" @mousedown="startDrag" @mouseleave="stopDrag" @mouseup="stopDrag" @mousemove="doDrag"
                         :style="isDragging ? 'cursor:grabbing' : 'cursor:grab'">
                        @foreach (\App\Models\MenuItem::types() as $key => $meta)
                            <button type="button" class="rjm-tab" :class="activeType === '{{ $key }}' ? 'on' : ''"
                                    @click="if(!isMoved) { activeType = '{{ $key }}'; loadOptions('{{ $key }}') }">
                                @if($key === 'custom')
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                @elseif($key === 'page')
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @elseif($key === 'post')
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z M14 4v6h6M8 13h8M8 17h5"/></svg>
                                @elseif($key === 'category')
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                @elseif($key === 'tag')
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z M7 7h.01"/></svg>
                                @endif
                                <span>{{ $meta['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.menus.items.store', $menu) }}" class="rjm-cb">
                    @csrf
                    <input type="hidden" name="type" :value="activeType">

                    <div x-show="activeType === 'custom'" class="rjm-stack">
                        <div>
                            <label class="lb">{{ __('Label') }} <em>*</em></label>
                            <input type="text" name="label" value="{{ old('label') }}" placeholder="{{ __('e.g. Home') }}">
                            @error('label') <p class="err">{{ $message }}</p> @enderror
                        </div>

                        @php $locales = rj_available_locales(); @endphp
                        @if (count($locales) > 1)
                            <div x-data="{ openTranslations: false }">
                                <button type="button" class="rjm-trans-btn" @click="openTranslations = !openTranslations">
                                    <svg width="14" height="14" style="transition:transform .2s" :style="openTranslations ? 'transform:rotate(180deg)' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    {{ __('Translations') }} ({{ count($locales) - 1 }})
                                </button>
                                <div x-show="openTranslations" x-cloak x-collapse class="rjm-trans">
                                    @foreach ($locales as $locale)
                                        @if ($locale === config('app.locale')) @continue @endif
                                        <div>
                                            <label>{{ rj_lang_name($locale) }} <small>({{ $locale }})</small></label>
                                            <input type="text" name="translations[{{ $locale }}]" value="{{ old('translations.' . $locale) }}" placeholder="{{ __('Optional') }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div x-show="activeType !== 'custom'" x-cloak>
                        <label class="lb">{{ __('Select') }} <em>*</em></label>
                        <select name="object_id" x-model="selectedObjectId" @change="onObjectSelect($event)">
                            <option value="">{{ __('— Choose —') }}</option>
                            <template x-for="opt in options" :key="opt.id">
                                <option :value="opt.id" x-text="opt.label"></option>
                            </template>
                        </select>
                        <p class="hint" x-show="activeType !== 'custom' && options.length === 0 && !loadingOptions">{{ __('No items available yet.') }}</p>
                    </div>

                    <div>
                        <label class="lb">{{ __('URL') }}</label>
                        <input type="text" class="mono" name="url" value="{{ old('url') }}" x-model="url" placeholder="{{ __('https://example.com or /path') }}">
                    </div>

                    <div class="rjm-2">
                        <div>
                            <label class="lb">{{ __('Parent') }}</label>
                            <select name="parent_id">
                                <option value="">{{ __('— Top level —') }}</option>
                                @foreach ($menu->items->whereNull('parent_id') as $item)
                                    <option value="{{ $item->id }}" {{ old('parent_id') == $item->id ? 'selected' : '' }}>{{ $item->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="lb">{{ __('Target') }}</label>
                            <select name="target">
                                <option value="_self">{{ __('Same window') }}</option>
                                <option value="_blank">{{ __('New window') }}</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="rjm-btn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        {{ __('Add to Menu') }}
                    </button>
                </form>
            </div>

            {{-- Structure --}}
            <div class="rjm-card">
                <div class="rjm-ch">
                    <div>
                        <h3>{{ __('Menu Structure') }}</h3>
                        <p>{{ __('Drag items to reorder. Items with children are collapsible.') }}</p>
                    </div>
                    <span class="rjm-count">{{ $menu->itemCount() }} {{ __('items') }}</span>
                </div>

                @if ($menu->items->isEmpty())
                    <div class="rjm-empty">
                        <div class="ic">
                            <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </div>
                        <b>{{ __('No items yet.') }}</b>
                        <span>{{ __('Add your first item using the form above.') }}</span>
                    </div>
                @else
                    <div class="rjm-tree">
                        <div id="menu-tree">
                            @foreach ($menu->items->whereNull('parent_id') as $item)
                                @include('admin.menus.partials.item-row', ['item' => $item, 'depth' => 0])
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ============ RIGHT ============ --}}
        <div class="rjm-col">
            <form method="POST" action="{{ route('admin.menus.update', $menu) }}" class="rjm-card">
                @csrf
                @method('PUT')

                <div class="rjm-ch"><h3>{{ __('Menu Settings') }}</h3></div>

                <div class="rjm-cb">
                    <div>
                        <label class="lb">{{ __('Name') }}</label>
                        <input type="text" name="name" value="{{ $menu->name }}" required>
                    </div>

                    <div>
                        <label class="lb">{{ __('Location') }}</label>
                        <select name="location">
                            <option value="">{{ __('— Not assigned —') }}</option>
                            @foreach ($locations as $key => $label)
                                <option value="{{ $key }}" {{ $menu->location === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="lb">{{ __('Description') }}</label>
                        <input type="text" name="description" value="{{ $menu->description }}">
                    </div>

                    {{-- Active toggle --}}
                    <label class="rjm-sw" x-data="{ on: {{ $menu->is_active ? 'true' : 'false' }} }" :class="on ? 'on' : ''">
                        <div>
                            <b>
                                {{ __('Active Menu') }}
                                <span class="rjm-st" x-text="on ? @js(__('On')) : @js(__('Off'))"></span>
                            </b>
                            <span class="d">{{ __('Only one menu per location can be active.') }}</span>
                        </div>
                        <input type="checkbox" name="is_active" value="1" x-model="on">
                        <span class="rjm-track" aria-hidden="true"></span>
                    </label>
                </div>

                <div class="rjm-cb" style="padding-top:0">
                    <button type="submit" class="rjm-btn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ __('Save Settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleEdit(id) {
    const el = document.getElementById('edit-item-' + id);
    if (el) el.classList.toggle('hidden');
}
</script>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
function menuEditor() {
    return {
        activeType: 'custom',
        selectedObjectId: '',
        url: '',
        options: [],
        loadingOptions: false,

        async loadOptions(type) {
            this.options = [];
            this.selectedObjectId = '';
            this.url = '';

            if (type === 'custom') return;

            this.loadingOptions = true;
            try {
                const res = await fetch('{{ route('admin.menus.items.options') }}?type=' + type, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.options = await res.json();
            } catch (e) {
                console.error(e);
            } finally {
                this.loadingOptions = false;
            }
        },

        onObjectSelect(e) {
            const id = e.target.value;
            const opt = this.options.find(o => String(o.id) === String(id));
            this.url = opt ? opt.url : '';
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const tree = document.getElementById('menu-tree');
    if (!tree || typeof Sortable === 'undefined') return;

    Sortable.create(tree, {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'opacity-40',
        onEnd: function () {
            const items = [];
            tree.querySelectorAll('[data-id]').forEach((el, index) => {
                items.push({
                    id: parseInt(el.dataset.id),
                    parent_id: null,
                    sort_order: index,
                });
            });

            fetch('{{ route('admin.menus.items.reorder', $menu) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ items })
            });
        }
    });
});
</script>
@endsection