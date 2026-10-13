<script>
window.RJ = {
    updateUrl: @json(route('admin.rjbuilder.update', $template)),
    csrf: @json(csrf_token()),
    name: @json($template->name),
    status: @json($template->status),
    templateType: @json($templateType),
    baseLocale: @json($baseLocale),
    locales: @json($locales),
    widgets: @json($widgetsData),
    categories: @json($categoriesData),
    blocks: @json($blocksData),
    i18n: @json($i18n),
    menus: @json($menusData ?? []),
    menuItems: @json($menuItemsData ?? {}),
};
</script>

@verbatim
<script>
const rjSVG = (inner) => '<svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">' + inner + '</svg>';

const rjStore = {
    get(key, fallback) {
        try { const v = localStorage.getItem(key); return v === null ? fallback : JSON.parse(v); }
        catch (e) { return fallback; }
    },
    set(key, value) {
        try { localStorage.setItem(key, JSON.stringify(value)); } catch (e) { /* storage unavailable */ }
    },
};

let rjObserver = null;
</script>
@endverbatim