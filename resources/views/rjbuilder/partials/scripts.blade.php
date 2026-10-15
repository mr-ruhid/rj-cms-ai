{{-- Data from PHP (must be outside @verbatim so Blade can process it) --}}
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
};
</script>

@verbatim
<script>
// IntersectionObserver must live outside Alpine's reactive proxy.
let rjObserver = null;

function builder() {
    const L = window.RJ;

    const SVG = (inner) => '<svg fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">' + inner + '</svg>';

    const store = {
        get(key, fallback) { try { const v = localStorage.getItem(key); return v === null ? fallback : JSON.parse(v); } catch (e) { return fallback; } },
        set(key, value) { try { localStorage.setItem(key, JSON.stringify(value)); } catch (e) { /* storage unavailable */ } },
    };

    return {
        widgets: L.widgets,
        categories: L.categories || {},
        blocks: L.blocks,
        selectedBlock: null,
        widgetSearch: '',
        catFilter: 'all',
        saving: false,
        saved: true,
        savedSnap: '',
        history: [],
        historyIndex: -1,
        clipboard: null,
        mediaPickerTarget: null,
        dragging: null,
        dragTarget: null,
        showLayoutPicker: false,
        _inited: false,

        // panels
        panel: 'add',
        panelOpen: true,
        editTab: 'content',
        collapsed: {},
        navCollapsed: {},
        favs: store.get('rj.favs', []),
        recents: store.get('rj.recent', []),
        prefs: Object.assign({ outlines: true, autosave: false, zoom: 100 }, store.get('rj.prefs', {})),

        // view
        device: 'desktop',
        preview: false,
        previewRun: 0,
        animPlayId: null,
        toast: { show: false, message: '', type: 'success' },

        templateType: L.templateType,
        baseLocale: L.baseLocale,
        locales: (function (l) { return Array.isArray(l) ? l : Object.keys(l || {}); })(L.locales),
        activeLocale: L.baseLocale,
        translatableFields: ['text', 'alt', 'caption', 'title', 'subtitle', 'description', 'label', 'button_text', 'content', 'placeholder'],

        devices: [
            { id: 'desktop', label: 'Desktop', svg: SVG('<path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>') },
            { id: 'tablet',  label: 'Tablet',  svg: SVG('<rect x="5" y="2" width="14" height="20" rx="2"/><path d="M11 18h2"/>') },
            { id: 'mobile',  label: 'Mobile',  svg: SVG('<rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/>') },
        ],

        layoutPresets: [
            { key: '1col',   title: '1 Column',   rows: [[1]] },
            { key: '2col',   title: '2 Columns',  rows: [[1, 1]] },
            { key: '3col',   title: '3 Columns',  rows: [[1, 1, 1]] },
            { key: '4col',   title: '4 Columns',  rows: [[1, 1, 1, 1]] },
            { key: '2row',   title: '2 Rows',     rows: [[1], [1]] },
            { key: '1plus2', title: '1 + 2',      rows: [[1], [1, 1]] },
            { key: '2row3',  title: '2 Rows × 3', rows: [[1, 1, 1], [1, 1, 1]] },
        ],

        shortcuts: [
            ['Ctrl S', 'Save'], ['Ctrl Z', 'Undo'], ['Ctrl Shift Z', 'Redo'], ['Ctrl D', 'Duplicate'],
            ['Ctrl C / V', 'Copy / Paste'], ['Del', 'Delete'], ['Alt ↑ / ↓', 'Move up / down'], ['P', 'Preview'], ['Esc', 'Deselect / exit preview'],
        ],

        advancedSchema: [
            { name: '_animation', label: 'Entrance animation', type: 'select', default: 'none', options: {
                none: 'None', fadeIn: 'Fade In', fadeInUp: 'Fade In Up', fadeInDown: 'Fade In Down',
                fadeInLeft: 'Fade In Left', fadeInRight: 'Fade In Right', zoomIn: 'Zoom In',
                bounceIn: 'Bounce In', flipInX: 'Flip In X', rotateIn: 'Rotate In',
                slideUp: 'Slide Reveal', blurIn: 'Blur In', pulse: 'Pulse',
            }},
            { name: '_animation_duration', label: 'Duration', type: 'select', default: 'md', options: { sm: 'Fast (300ms)', md: 'Normal (600ms)', lg: 'Slow (1s)', xl: 'Very slow (1.5s)' } },
            { name: '_animation_delay', label: 'Delay', type: 'select', default: '0', options: { '0': 'None', '200ms': '200ms', '400ms': '400ms', '600ms': '600ms', '1s': '1s' } },
            { name: '_hover', label: 'Hover effect', type: 'select', default: 'none', options: { none: 'None', grow: 'Grow', lift: 'Lift + shadow', glow: 'Glow', tilt: '3D tilt', float: 'Float' } },
            { name: '_hide_desktop', label: 'Hide on desktop', type: 'toggle' },
            { name: '_hide_tablet', label: 'Hide on tablet', type: 'toggle' },
            { name: '_hide_mobile', label: 'Hide on mobile', type: 'toggle' },
            { name: '_css_class', label: 'Custom CSS class', type: 'text' },
        ],

        /* ───────── getters ───────── */
        get canUndo() { return this.historyIndex > 0; },
        get canRedo() { return this.historyIndex < this.history.length - 1; },

        get shownPanel() {
            if (this.panel === 'edit' && !this.selectedBlock) return 'add';
            return this.panel;
        },

        get currentBlock() {
            if (!this.selectedBlock) return null;
            return this.findBlock(this.selectedBlock);
        },

        get currentSchema() {
            if (!this.currentBlock) return [];
            const w = this.widgets.find(x => x.id === this.currentBlock.type);
            return w ? (w.schema || []) : [];
        },

        get currentSettings() {
            if (!this.currentBlock) return {};
            return this.currentBlock.settings || {};
        },

        get activeFields() {
            if (this.editTab === 'advanced') return this.advancedSchema;
            const wantStyle = this.editTab === 'style';
            return this.currentSchema.filter(f => this.isStyleField(f) === wantStyle);
        },

        get breadcrumb() {
            if (!this.selectedBlock) return [];
            return this.findPath(this.selectedBlock) || [];
        },

        get categoryList() {
            const keys = Object.keys(this.categories);
            const used = new Set(this.widgets.map(w => w.category));
            const list = keys.filter(k => used.has(k)).map(k => ({ key: k, label: this.categories[k].label || k, icon: this.categories[k].icon || '' }));
            const extra = [...used].filter(k => !keys.includes(k));
            if (extra.length) list.push({ key: '__other', label: 'Other', icon: '•' });
            return list;
        },

        get widgetSections() {
            const q = this.widgetSearch.trim().toLowerCase();
            const match = (w) => !q || w.name.toLowerCase().includes(q) || w.id.toLowerCase().includes(q);
            const byId = (id) => this.widgets.find(w => w.id === id);
            const out = [];

            if (!q && this.catFilter === 'all') {
                out.push({ key: '__fav', kind: 'fav', icon: '☆', label: this.t('Favourite elements'), items: this.favs.map(byId).filter(Boolean) });
                const rec = this.recents.map(byId).filter(Boolean);
                if (rec.length) out.push({ key: '__rec', kind: 'rec', icon: '◷', label: this.t('Recently used'), items: rec });
            }

            const known = Object.keys(this.categories);
            this.categoryList.forEach(c => {
                if (this.catFilter !== 'all' && this.catFilter !== c.key) return;
                const items = this.widgets.filter(w => (c.key === '__other' ? !known.includes(w.category) : w.category === c.key) && match(w));
                if (items.length) out.push({ key: c.key, kind: 'cat', icon: c.icon, label: c.label, items });
            });

            return out;
        },

        get sectionTemplates() {
            if (!this.hasWidget('heading') || !this.hasWidget('text')) return [];
            const list = [
                { key: 'hero', title: 'Hero', desc: 'Large headline with a short intro.' },
                { key: 'features', title: 'Three features', desc: 'Three columns, each with a title and text.' },
                { key: 'cta', title: 'Call to action', desc: 'Dark banner with a headline and one line.' },
            ];
            if (this.hasWidget('image')) list.splice(2, 0, { key: 'split', title: 'Image + text', desc: 'Picture on the left, copy on the right.' });
            return list;
        },

        get historyList() {
            return this.history.map((h, i) => ({ i, label: h.label, t: h.t })).reverse();
        },

        get deviceLabel() {
            const d = this.devices.find(x => x.id === this.device);
            return d ? d.label : 'Desktop';
        },

        get deviceWidthLabel() {
            return { desktop: this.t('Full width'), tablet: '820 px', mobile: '420 px' }[this.device];
        },

        get canvasStyle() {
            const z = this.prefs.zoom;
            const zoom = (!this.preview && z !== 100) ? ` zoom:${z / 100};` : '';
            if (this.preview) {
                if (this.device === 'desktop') return 'max-width:100%;' + 'background:#fff; min-height:100%;';
                const w = this.device === 'tablet' ? '820px' : '420px';
                return `max-width:${w}; background:#fff; box-shadow:0 0 0 1px #cfd3db, 0 20px 50px rgba(17,24,39,.18); margin-top:24px; margin-bottom:24px; border-radius:18px; overflow:hidden;`;
            }
            if (this.device === 'desktop') return 'max-width:1152px;' + zoom;
            const w = this.device === 'tablet' ? '820px' : '420px';
            return `max-width:${w}; padding:24px 12px;${zoom}`;
        },

        /* ───────── lifecycle ───────── */
        init() {
            if (this._inited) return;
            this._inited = true;

            this.savedSnap = JSON.stringify(this.blocks);
            this.pushHistory(this.t('Initial state'));

            this.$watch('activeLocale', () => { if (this.preview) this.runPreviewAnimations(); });
            this.$watch('selectedBlock', (v) => { if (v) this.editTab = this.editTab || 'content'; });

            setTimeout(() => { window.__builder = this; }, 50);
            window.__builder = this;

            setInterval(() => {
                if (this.prefs.autosave && !this.saved && !this.saving && !this.preview) this.save(true);
            }, 30000);
        },

        t(s) { return (L.i18n && L.i18n[s]) || s; },

        onKey(e) {
            const t = e.target;
            const typing = t && (['INPUT', 'TEXTAREA', 'SELECT'].includes(t.tagName) || t.isContentEditable);
            const mod = e.ctrlKey || e.metaKey;
            const key = (e.key || '').toLowerCase();

            if (mod && key === 's') { e.preventDefault(); this.save(); return; }
            if (key === 'escape') {
                if (this.preview) this.togglePreview();
                else if (!typing) this.selectedBlock = null;
                return;
            }
            if (typing) return;
            if (!mod && !e.altKey && key === 'p') { e.preventDefault(); this.togglePreview(); return; }
            if (this.preview) return;

            if (mod && key === 'z') { e.preventDefault(); e.shiftKey ? this.redo() : this.undo(); return; }
            if (mod && key === 'y') { e.preventDefault(); this.redo(); return; }
            if (mod && key === 'd' && this.selectedBlock) { e.preventDefault(); this.duplicateSelected(); return; }
            if (mod && key === 'c' && this.selectedBlock && !String(window.getSelection()).length) { e.preventDefault(); this.copySelected(); return; }
            if (mod && key === 'v') { e.preventDefault(); this.pasteBlock(); return; }
            if (e.altKey && key === 'arrowup' && this.selectedBlock) { e.preventDefault(); this.moveUp(this.selectedBlock); return; }
            if (e.altKey && key === 'arrowdown' && this.selectedBlock) { e.preventDefault(); this.moveDown(this.selectedBlock); return; }
            if ((key === 'delete' || key === 'backspace') && this.selectedBlock) { e.preventDefault(); this.removeSelected(); }
        },

        showToast(message, type) {
            this.toast = { show: true, message, type: type || 'success' };
            clearTimeout(this._toastTimer);
            this._toastTimer = setTimeout(() => { this.toast.show = false; }, 2200);
        },

        /* ───────── panels / favourites / prefs ───────── */
        openPanel(name) {
            if (this.shownPanel === name && this.panelOpen) { this.panelOpen = false; return; }
            this.panel = name;
            this.panelOpen = true;
        },

        isFav(id) { return this.favs.includes(id); },
        toggleFav(id) {
            this.favs = this.isFav(id) ? this.favs.filter(x => x !== id) : [...this.favs, id];
            store.set('rj.favs', this.favs);
        },
        touchRecent(id) {
            if (id === 'container') return;
            this.recents = [id, ...this.recents.filter(x => x !== id)].slice(0, 6);
            store.set('rj.recent', this.recents);
        },
        isCollapsed(key) { return !this.widgetSearch && !!this.collapsed[key]; },
        toggleSec(key) { this.collapsed = Object.assign({}, this.collapsed, { [key]: !this.collapsed[key] }); },
        setPref(key, value) {
            this.prefs = Object.assign({}, this.prefs, { [key]: value });
            store.set('rj.prefs', this.prefs);
        },

        ago(ts) {
            const s = Math.round((Date.now() - ts) / 1000);
            if (s < 10) return this.t('now');
            if (s < 60) return s + 's';
            if (s < 3600) return Math.floor(s / 60) + 'm';
            return Math.floor(s / 3600) + 'h';
        },

        /* ───────── helpers ───────── */
        esc(s) {
            return String(s == null ? '' : s)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        },

        isStyleField(f) {
            if (f.tab) return f.tab === 'style';
            return /color|background|bg|padding|margin|gap|border|radius|rounded|size|align|width|height|font|weight|shadow|spacing|opacity/i.test(f.name);
        },

        isTranslatableField(name) {
            if (this.templateType !== 'page') return false;
            return this.translatableFields.includes(name);
        },

        getBlockField(block, name) {
            const s = block.settings || {};
            if (!this.isTranslatableField(name) || this.activeLocale === this.baseLocale) return s[name];
            const tr = (s.translations || {})[this.activeLocale] || {};
            return tr[name] !== undefined ? tr[name] : s[name];
        },

        getBlockText(block) {
            return this.getBlockField(block, 'text') || '';
        },

        setBlockField(block, name, value) {
            const s = block.settings = block.settings || {};
            if (!this.isTranslatableField(name) || this.activeLocale === this.baseLocale) {
                s[name] = value;
                return;
            }
            if (!s.translations) s.translations = {};
            if (!s.translations[this.activeLocale]) s.translations[this.activeLocale] = {};
            s.translations[this.activeLocale][name] = value;
        },

        fieldValue(field) {
            const b = this.currentBlock;
            if (b) {
                const v = this.getBlockField(b, field.name);
                if (v !== undefined && v !== null) return v;
            }
            return field.default !== undefined ? field.default : '';
        },

        isDefault(field) {
            const b = this.currentBlock;
            if (!b) return true;
            const v = this.getBlockField(b, field.name);
            if (v === undefined || v === null) return true;
            const d = field.default !== undefined ? field.default : '';
            return String(v) === String(d) || (field.type === 'toggle' && !v && !d);
        },

        resetField(field) {
            const b = this.currentBlock;
            if (!b) return;
            if (this.isTranslatableField(field.name) && this.activeLocale !== this.baseLocale) {
                const tr = (b.settings.translations || {})[this.activeLocale];
                if (tr) delete tr[field.name];
            } else {
                this.setBlockField(b, field.name, field.default !== undefined ? field.default : '');
            }
            this.markDirty();
            this.commitSoon(this.t('Edited') + ' ' + this.blockLabel(b.type));
        },

        colorHex(v) {
            return /^#[0-9a-f]{6}$/i.test(v || '') ? v : '#000000';
        },

        hasWidget(id) { return this.widgets.some(w => w.id === id); },

        blockLabel(type) {
            const w = this.widgets.find(x => x.id === type);
            return w ? w.name : type;
        },

        currentBlockTypeLabel() {
            return this.currentBlock ? this.blockLabel(this.currentBlock.type) : '';
        },

        generateId() {
            return 'blk_' + Math.random().toString(36).slice(2, 11);
        },

        findBlock(id, list) {
            list = list || this.blocks;
            for (const b of list) {
                if (b.id === id) return b;
                if (b.children && b.children.length) {
                    const found = this.findBlock(id, b.children);
                    if (found) return found;
                }
            }
            return null;
        },

        findBlockAndParent(id, list, parent) {
            list = list || this.blocks;
            parent = parent || null;
            for (let i = 0; i < list.length; i++) {
                if (list[i].id === id) return { block: list[i], parent: parent, index: i, list: list };
                if (list[i].children && list[i].children.length) {
                    const found = this.findBlockAndParent(id, list[i].children, list[i]);
                    if (found) return found;
                }
            }
            return null;
        },

        findPath(id, list, trail) {
            list = list || this.blocks;
            trail = trail || [];
            for (const b of list) {
                const next = trail.concat([b]);
                if (b.id === id) return next;
                if (b.children && b.children.length) {
                    const found = this.findPath(id, b.children, next);
                    if (found) return found;
                }
            }
            return null;
        },

        emptySlot() {
            return this.createBlock('container', { columns: '1' });
        },

        /* ───────── layouts & ready-made sections ───────── */
        addLayout(preset) {
            const presets = {
                '1col': { cols: 1 },
                '2col': { cols: 2 },
                '3col': { cols: 3 },
                '4col': { cols: 4 },
                '2row': { cols: 1, rows: 2 },
                '1plus2': { special: true },
                '2row3': { rows: 2, rowCols: 3 },
            };

            const p = presets[preset];
            if (!p) return;

            const container = this.createBlock('container', {
                columns: String(p.cols || 1),
                gap: 'md',
                padding: 'md',
                align: 'stretch',
                width: 'boxed',
            });

            container.children = [];

            if (p.special) {
                container.settings.columns = '1';
                const topRow = this.createBlock('container', { columns: '1' });
                topRow.children = [this.emptySlot()];
                container.children.push(topRow);

                const bottomRow = this.createBlock('container', { columns: '2' });
                bottomRow.children = [this.emptySlot(), this.emptySlot()];
                container.children.push(bottomRow);
            } else if (p.rowCols) {
                container.settings.columns = '1';
                for (let r = 0; r < p.rows; r++) {
                    const row = this.createBlock('container', { columns: String(p.rowCols) });
                    row.children = [];
                    for (let c = 0; c < p.rowCols; c++) row.children.push(this.emptySlot());
                    container.children.push(row);
                }
            } else if (p.rows && p.rows > 1) {
                container.settings.columns = '1';
                for (let r = 0; r < p.rows; r++) container.children.push(this.emptySlot());
            } else {
                for (let i = 0; i < p.cols; i++) container.children.push(this.emptySlot());
            }

            this.blocks.push(container);
            this.selectedBlock = container.id;
            this.markDirty();
            this.pushHistory(this.t('Added') + ' ' + this.t('Add New Section'));
            this.scrollToBlock(container.id);
        },

        addBlank() {
            const container = this.createBlock('container', {
                columns: '1',
                gap: 'md',
                padding: 'md',
                width: 'boxed',
            });
            container.children = [this.emptySlot()];
            this.blocks.push(container);
            this.selectedBlock = container.id;
            this.markDirty();
            this.pushHistory(this.t('Added') + ' ' + this.t('Add New Section'));
        },

        addSection(key) {
            const mk = (type, s, kids) => { const b = this.createBlock(type, s); b.children = kids || []; return b; };
            const slot = (...kids) => mk('container', { columns: '1' }, kids);
            const wrap = (cols, extra, slots) => mk('container', Object.assign({ columns: String(cols), gap: 'lg', padding: 'xl', align: 'center', width: 'boxed' }, extra), slots);
            let section;

            if (key === 'hero') {
                section = wrap(1, { background: '#f5f3ff' }, [slot(
                    mk('heading', { level: 'h1', align: 'center', text: 'Your headline goes here' }),
                    mk('text', { align: 'center', size: 'lg', text: 'Explain what you offer in one or two clear sentences.' })
                )]);
            } else if (key === 'features') {
                const col = (title) => slot(
                    mk('heading', { level: 'h3', align: 'center', text: title }),
                    mk('text', { align: 'center', text: 'A short description of this point.' })
                );
                section = wrap(3, { align: 'start' }, [col('First feature'), col('Second feature'), col('Third feature')]);
            } else if (key === 'split') {
                section = wrap(2, {}, [
                    slot(mk('image', { align: 'center', width: 'full', rounded: 'lg' })),
                    slot(
                        mk('heading', { level: 'h2', align: 'left', text: 'A heading for this section' }),
                        mk('text', { align: 'left', text: 'Tell the story behind the picture in a few sentences.' })
                    ),
                ]);
            } else if (key === 'cta') {
                section = wrap(1, { background: '#1a1030' }, [slot(
                    mk('heading', { level: 'h2', align: 'center', color: '#ffffff', text: 'Ready to get started?' }),
                    mk('text', { align: 'center', color: '#cbd5e1', text: 'Add one sentence that tells people what to do next.' })
                )]);
            }
            if (!section) return;

            this.blocks.push(section);
            this.selectedBlock = section.id;
            this.markDirty();
            this.pushHistory(this.t('Added') + ' ' + (this.sectionTemplates.find(s => s.key === key) || {}).title);
            this.scrollToBlock(section.id);
        },

        createBlock(widgetId, settingsOverride) {
            const w = this.widgets.find(x => x.id === widgetId);
            const defaults = w ? JSON.parse(JSON.stringify(w.defaults || {})) : {};
            const settings = Object.assign({}, defaults, settingsOverride || {});

            return {
                id: this.generateId(),
                type: widgetId,
                settings: settings,
                children: [],
            };
        },

        quickAdd(widgetId) {
            this.touchRecent(widgetId);
            if (this.selectedBlock) {
                const found = this.findBlockAndParent(this.selectedBlock);
                const block = found ? found.block : null;

                if (block && block.type === 'container') {
                    if (!block.children) block.children = [];
                    const newBlock = this.createBlock(widgetId);
                    const emptyIndex = block.children.findIndex(c => c.type === 'container' && (!c.children || c.children.length === 0));
                    if (emptyIndex === -1) block.children.push(newBlock);
                    else block.children[emptyIndex] = newBlock;
                    this.afterInsert(newBlock);
                    return;
                }

                if (block && found) {
                    const newBlock = this.createBlock(widgetId);
                    found.list.splice(found.index + 1, 0, newBlock);
                    this.afterInsert(newBlock);
                    return;
                }
            }
            this.insertAt(widgetId, this.blocks, this.blocks.length);
        },

        afterInsert(block) {
            this.selectedBlock = block.id;
            this.markDirty();
            this.pushHistory(this.t('Added') + ' ' + this.blockLabel(block.type));
            this.playAnimation(block.id);
            this.scrollToBlock(block.id);
        },

        insertAt(widgetId, list, index) {
            this.touchRecent(widgetId);
            const block = this.createBlock(widgetId);
            list.splice(index, 0, block);
            this.afterInsert(block);
        },

        /* ───────── drag & drop (new + move) ───────── */
        startDragNew(widgetId, event) {
            this.dragging = { type: 'new', widgetId };
            event.dataTransfer.effectAllowed = 'copy';
            event.dataTransfer.setData('text/plain', widgetId);
        },

        startDragMove(id, event) {
            this.dragging = { type: 'move', id };
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', id);
            const wrap = event.target.closest('[data-block-id]');
            if (wrap && event.dataTransfer.setDragImage) event.dataTransfer.setDragImage(wrap, 24, 16);
        },

        onDragOver(event, parentKey, index) {
            event.preventDefault();
            this.dragTarget = parentKey + ':' + index;
        },

        detach(found) {
            const { parent, index, list, block } = found;
            const cols = parent ? (parseInt((parent.settings || {}).columns) || 1) : 1;
            if (parent && parent.type === 'container' && cols > 1) list.splice(index, 1, this.emptySlot());
            else list.splice(index, 1);
            return block;
        },

        onDrop(event, parentKey, index) {
            event.preventDefault();
            this.dragTarget = null;
            const d = this.dragging;
            this.dragging = null;
            if (!d || parentKey !== 'root') return;

            if (d.type === 'new') {
                this.insertAt(d.widgetId, this.blocks, index);
                return;
            }

            const found = this.findBlockAndParent(d.id);
            if (!found) return;
            const sameRoot = found.parent === null;
            const oldIndex = found.index;
            const block = this.detach(found);
            let target = index;
            if (sameRoot && oldIndex < index) target--;
            this.blocks.splice(target, 0, block);
            this.selectedBlock = block.id;
            this.markDirty();
            this.pushHistory(this.t('Moved') + ' ' + this.blockLabel(block.type));
        },

        dropInto(containerId, slotIndex) {
            const d = this.dragging;
            this.dragging = null;
            this.dragTarget = null;
            document.querySelectorAll('.rj-drop').forEach(el => el.classList.remove('rj-drop'));
            if (!d) return;

            const container = this.findBlock(containerId);
            if (!container) return;

            let block;
            if (d.type === 'new') {
                this.touchRecent(d.widgetId);
                block = this.createBlock(d.widgetId);
            } else {
                const found = this.findBlockAndParent(d.id);
                if (!found) return;
                if (d.id === containerId || this.findBlock(containerId, found.block.children || [])) return;
                block = this.detach(found);
            }

            if (!container.children) container.children = [];
            while (container.children.length <= slotIndex) container.children.push(this.emptySlot());

            const slot = container.children[slotIndex];
            if (slot.type === 'container') {
                if (!slot.children) slot.children = [];
                slot.children.push(block);
            } else {
                const wrap = this.emptySlot();
                wrap.children = [slot, block];
                container.children[slotIndex] = wrap;
            }

            this.selectedBlock = block.id;
            this.markDirty();
            this.pushHistory((d.type === 'new' ? this.t('Added') : this.t('Moved')) + ' ' + this.blockLabel(block.type));
            if (d.type === 'new') this.playAnimation(block.id);
        },

        endDrag() {
            this.dragging = null;
            this.dragTarget = null;
            document.querySelectorAll('.rj-drop').forEach(el => el.classList.remove('rj-drop'));
        },

        /* ───────── block actions ───────── */
        selectBlock(id) {
            if (this.preview) return;
            const m = document.getElementById('rj-main');
            const top = m ? m.scrollTop : 0;
            this.selectedBlock = id;
            this.panel = 'edit';
            this.panelOpen = true;
            this.$nextTick(() => { if (m) m.scrollTop = top; });
        },

        selectFromNav(id) {
            this.selectedBlock = id;
            this.scrollToBlock(id);
        },

        openEdit(id) {
            this.selectedBlock = id;
            this.panel = 'edit';
            this.panelOpen = true;
            this.scrollToBlock(id);
        },

        selectParent(id) {
            const found = this.findBlockAndParent(id);
            if (found && found.parent) this.selectBlock(found.parent.id);
        },

        scrollToBlock(id) {
            this.$nextTick(() => {
                const el = document.querySelector('#rj-canvas [data-block-id="' + id + '"]');
                if (el && el.scrollIntoView) el.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            });
        },

        autoScroll(e) {
            if (!this.dragging) return;
            const m = document.getElementById('rj-main');
            if (!m) return;
            const r = m.getBoundingClientRect();
            const edge = 90;
            if (e.clientY < r.top + edge) m.scrollTop -= Math.ceil((r.top + edge - e.clientY) / 3);
            else if (e.clientY > r.bottom - edge) m.scrollTop += Math.ceil((e.clientY - (r.bottom - edge)) / 3);
        },

        removeBlock(id) {
            const found = this.findBlockAndParent(id);
            if (!found) return;
            const label = this.blockLabel(found.block.type);
            found.list.splice(found.index, 1);
            if (this.selectedBlock === id) this.selectedBlock = null;
            this.markDirty();
            this.pushHistory(this.t('Deleted') + ' ' + label);
        },

        duplicateBlock(id) {
            const found = this.findBlockAndParent(id);
            if (!found) return;
            const copy = JSON.parse(JSON.stringify(found.block));
            this.reassignIds(copy);
            found.list.splice(found.index + 1, 0, copy);
            this.selectedBlock = copy.id;
            this.markDirty();
            this.pushHistory(this.t('Duplicated') + ' ' + this.blockLabel(copy.type));
        },

        reassignIds(block) {
            block.id = this.generateId();
            if (block.children && block.children.length) {
                for (const child of block.children) this.reassignIds(child);
            }
        },

        copySelected() {
            if (!this.currentBlock) return;
            this.clipboard = JSON.stringify(this.currentBlock);
            this.showToast(this.t('Copied'));
        },

        pasteBlock() {
            if (!this.clipboard) { this.showToast(this.t('Nothing to paste'), 'error'); return; }
            const copy = JSON.parse(this.clipboard);
            this.reassignIds(copy);
            const found = this.selectedBlock ? this.findBlockAndParent(this.selectedBlock) : null;
            if (found) found.list.splice(found.index + 1, 0, copy);
            else this.blocks.push(copy);
            this.selectedBlock = copy.id;
            this.markDirty();
            this.pushHistory(this.t('Pasted') + ' ' + this.blockLabel(copy.type));
            this.scrollToBlock(copy.id);
        },

        moveUp(id) {
            const found = this.findBlockAndParent(id);
            if (!found || found.index === 0) return;
            [found.list[found.index - 1], found.list[found.index]] = [found.list[found.index], found.list[found.index - 1]];
            this.markDirty();
            this.pushHistory(this.t('Moved') + ' ' + this.blockLabel(found.block.type));
        },

        moveDown(id) {
            const found = this.findBlockAndParent(id);
            if (!found || found.index === found.list.length - 1) return;
            [found.list[found.index + 1], found.list[found.index]] = [found.list[found.index], found.list[found.index + 1]];
            this.markDirty();
            this.pushHistory(this.t('Moved') + ' ' + this.blockLabel(found.block.type));
        },

        updateSetting(name, value) {
            if (!this.currentBlock) return;
            this.setBlockField(this.currentBlock, name, value);
            this.markDirty();
            this.commitSoon(this.t('Edited') + ' ' + this.blockLabel(this.currentBlock.type));

            if (name.startsWith('_animation')) {
                const id = this.currentBlock.id;
                clearTimeout(this._animTimer);
                this._animTimer = setTimeout(() => this.playAnimation(id), 250);
            }
        },

        duplicateSelected() { if (this.selectedBlock) this.duplicateBlock(this.selectedBlock); },
        removeSelected() { if (this.selectedBlock) this.removeBlock(this.selectedBlock); },

        /* ───────── inline editing (double-click heading/text) ───────── */
        startInline(el, id) {
            if (this.preview) return;
            const b = this.findBlock(id);
            if (!b) return;

            this.selectedBlock = id;
            el.contentEditable = 'true';
            el.style.outline = '2px solid #ec4899';
            el.style.outlineOffset = '2px';
            el.focus();

            const range = document.createRange();
            range.selectNodeContents(el);
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);

            const onKey = (e) => {
                if (e.key === 'Escape' || (e.key === 'Enter' && !e.shiftKey && b.type === 'heading')) {
                    e.preventDefault();
                    el.blur();
                }
            };
            const done = () => {
                el.removeEventListener('blur', done);
                el.removeEventListener('keydown', onKey);
                el.contentEditable = 'false';
                el.style.outline = '';
                const txt = el.innerText.replace(/\n+$/, '');
                if (this.getBlockText(b) !== txt) {
                    this.setBlockField(b, 'text', txt);
                    this.markDirty();
                    this.pushHistory(this.t('Edited') + ' ' + this.blockLabel(b.type));
                }
            };
            el.addEventListener('blur', done);
            el.addEventListener('keydown', onKey);
        },

        openMediaPicker(fieldName) {
            this.mediaPickerTarget = fieldName;
            const mockInput = document.createElement('input');
            mockInput.type = 'hidden';
            const handler = (e) => {
                this.updateSetting(this.mediaPickerTarget, e.detail.url);
                window.removeEventListener('media-selected', handler);
            };
            window.addEventListener('media-selected', handler);
            window.dispatchEvent(new CustomEvent('open-media-picker', { detail: { value: mockInput } }));
        },

        /* ───────── device + preview + animation playback ───────── */
        setDevice(d) {
            this.device = d;
            if (this.preview) {
                this.previewRun++;
                this.runPreviewAnimations();
            }
        },

        togglePreview() {
            this.preview = !this.preview;
            this.selectedBlock = null;
            this.previewRun++;
            this.$nextTick(() => {
                const m = document.getElementById('rj-main');
                if (m) m.scrollTop = 0;
            });
            this.runPreviewAnimations();
        },

        replayPreview() {
            this.previewRun++;
            const m = document.getElementById('rj-main');
            if (m) m.scrollTop = 0;
            this.runPreviewAnimations();
        },

        runPreviewAnimations() {
            if (rjObserver) { rjObserver.disconnect(); rjObserver = null; }
            if (!this.preview) return;

            this.$nextTick(() => requestAnimationFrame(() => {
                const els = document.querySelectorAll('#rj-canvas .rj-wait');
                const reveal = (el) => { el.classList.remove('rj-wait'); el.classList.add('rj-play'); };

                if (!('IntersectionObserver' in window)) { els.forEach(reveal); return; }

                rjObserver = new IntersectionObserver((entries) => {
                    entries.forEach(en => {
                        if (!en.isIntersecting) return;
                        reveal(en.target);
                        rjObserver.unobserve(en.target);
                    });
                }, { root: document.getElementById('rj-main'), threshold: 0, rootMargin: '0px 0px -8% 0px' });

                els.forEach(el => rjObserver.observe(el));
            }));
        },

        durationMs(settings) {
            return { sm: 300, md: 600, lg: 1000, xl: 1500 }[settings._animation_duration || 'md'] || 600;
        },

        delayMs(settings) {
            const d = settings._animation_delay || '0';
            if (d === '0') return 0;
            return d.endsWith('ms') ? parseInt(d) : Math.round(parseFloat(d) * 1000);
        },

        playAnimation(id) {
            const b = id ? this.findBlock(id) : null;
            if (!b || !b.settings || !b.settings._animation || b.settings._animation === 'none') return;
            this.animPlayId = null;
            setTimeout(() => {
                this.animPlayId = id;
                clearTimeout(this._playTimer);
                this._playTimer = setTimeout(() => { this.animPlayId = null; },
                    this.durationMs(b.settings) + this.delayMs(b.settings) + 200);
            }, 30);
        },

        /* ───────── rendering ───────── */
        toolbarBtn(icon, title, action, danger) {
            const hover = danger ? 'rgba(239,68,68,0.5)' : 'rgba(255,255,255,0.15)';
            return `<button type="button" title="${title}" onclick="event.stopPropagation(); ${action}" style="width:22px;height:22px;border-radius:9999px;background:transparent;border:0;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;line-height:1;" onmouseover="this.style.background='${hover}'" onmouseout="this.style.background='transparent'">${icon}</button>`;
        },

        renderBlockHtml(block, parentKey, index) {
            const w = this.widgets.find(x => x.id === block.type);
            if (!w) return `<div style="padding:12px; font-size:12px; color:#dc2626;">${this.esc(this.t('Unknown widget'))}</div>`;

            const s = block.settings || {};
            const hiddenHere = !!s['_hide_' + this.device];
            const hasAnim = s._animation && s._animation !== 'none';

            if (this.preview && hiddenHere) return '';

            let inner = block.type === 'container' ? this.renderContainer(block) : this.renderSimpleWidget(block);

            if (this.preview && inner === '') return '';

            if (hasAnim) {
                if (this.preview) inner = this.animationWrapper(inner, s, 'rj-wait');
                else if (this.animPlayId === block.id) inner = this.animationWrapper(inner, s, 'rj-play');
            }

            if (this.preview && s._hover && s._hover !== 'none') {
                inner = `<div class="rj-h rj-h-${this.esc(s._hover)}">${inner}</div>`;
            }

            const customClass = s._css_class ? ' ' + this.esc(s._css_class) : '';

            if (this.preview) return `<div class="${customClass.trim()}" data-run="${this.previewRun}">${inner}</div>`;

            const isSelected = this.selectedBlock === block.id;
            const bid = block.id;
            const hiddenNote = hiddenHere
                ? `<div style="position:absolute;top:6px;right:8px;z-index:20;font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;background:#1a1030;color:#fbbf24;border-radius:9999px;padding:2px 8px;">${this.esc(this.t('Hidden on'))} ${this.esc(this.device)}</div>`
                : '';

            return `
                <div class="rj-block${customClass}" data-block-id="${bid}" style="position:relative; ${hiddenHere ? 'opacity:.35;' : ''}" onclick="event.stopPropagation(); window.__builder.selectBlock('${bid}')">
                    ${hiddenNote}
                    <div style="position:absolute;top:-14px;left:50%;transform:translateX(-50%);z-index:30;display:flex;align-items:center;gap:2px;background:#1a1030;color:#fff;border-radius:9999px;padding:2px 6px;box-shadow:0 4px 12px rgba(0,0,0,0.2);opacity:${isSelected ? 1 : 0};pointer-events:${isSelected ? 'auto' : 'none'};transition:opacity 0.15s;white-space:nowrap;">
                        <span style="font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;padding:0 4px;color:#a855f7;">${this.esc(this.blockLabel(block.type))}${hasAnim ? ' ✦' : ''}</span>
                        <span draggable="true" ondragstart="event.stopPropagation(); window.__builder.startDragMove('${bid}', event)" title="Drag to move" style="width:22px;height:22px;display:flex;align-items:center;justify-content:center;cursor:grab;font-size:13px;">⠿</span>
                        ${this.toolbarBtn('⇱', this.esc(this.t('Select parent')), `window.__builder.selectParent('${bid}')`)}
                        ${this.toolbarBtn('↑', this.esc(this.t('Move up')), `window.__builder.moveUp('${bid}')`)}
                        ${this.toolbarBtn('↓', this.esc(this.t('Move down')), `window.__builder.moveDown('${bid}')`)}
                        ${this.toolbarBtn('⧉', this.esc(this.t('Duplicate')), `window.__builder.duplicateBlock('${bid}')`)}
                        ${this.toolbarBtn('✕', this.esc(this.t('Delete')), `window.__builder.removeBlock('${bid}')`, true)}
                    </div>
                    <div class="rj-ring" style="${isSelected ? 'outline:2px solid #7c3aed; outline-offset:-2px;' : ''} border-radius:12px;">${inner}</div>
                </div>
            `;
        },

        renderContainer(block) {
            const settings = block.settings || {};
            const cols = parseInt(settings.columns) || 1;
            const gapMap = { none: '0', sm: '0.5rem', md: '1rem', lg: '1.5rem', xl: '2rem' };
            const paddingMap = { none: '0', sm: '0.5rem', md: '1rem', lg: '1.5rem', xl: '2.5rem' };
            const bg = settings.background || 'transparent';
            const gap = gapMap[settings.gap] || '1rem';
            const padding = paddingMap[settings.padding] || '1rem';
            const minHeight = settings.min_height && settings.min_height !== 'auto' ? ({ sm: '200px', md: '400px', lg: '600px' }[settings.min_height] || 'auto') : 'auto';
            const border = settings.border === 'solid' ? '2px solid #e5e7eb' : settings.border === 'dashed' ? '2px dashed #e5e7eb' : 'none';
            const alignMap = { start: 'flex-start', center: 'center', end: 'flex-end', stretch: 'stretch' };
            const align = alignMap[settings.align] || 'stretch';
            const isFullWidth = settings.width === 'full';

            const children = block.children || [];
            const bid = block.id;

            if (this.preview && children.every(c => !c || (c.type === 'container' && (!c.children || !c.children.length) && (!c.settings || !c.settings.background || c.settings.background === 'transparent'))) && bg === 'transparent' && minHeight === 'auto' && border === 'none') {
                return '';
            }

            let effCols = cols;
            if (this.device === 'tablet') effCols = Math.min(cols, 2);
            if (this.device === 'mobile') effCols = 1;

            const slotCount = Math.max(cols, children.length);
            let slotsHtml = '';

            for (let i = 0; i < slotCount; i++) {
                const child = children[i];

                if (this.preview) {
                    slotsHtml += `<div style="min-width:0;">${child ? this.renderBlockHtml(child, bid, i) : ''}</div>`;
                    continue;
                }

                const inner = child
                    ? this.renderBlockHtml(child, bid, i)
                    : `<div style="min-height:80px; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#c3c7d0; pointer-events:none;">
                           <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                           <span style="font-size:9px; text-transform:uppercase; letter-spacing:.08em; margin-top:2px;">${this.esc(this.t('Drop here'))} · ${i + 1}</span>
                       </div>`;

                slotsHtml += `
                    <div style="position:relative; min-height:80px; min-width:0; border-radius:8px; transition:all .15s; border:2px ${child ? 'solid transparent' : 'dashed #e5e7eb'};"
                         ondragover="event.preventDefault(); event.stopPropagation(); this.classList.add('rj-drop');"
                         ondragleave="this.classList.remove('rj-drop');"
                         ondrop="event.preventDefault(); event.stopPropagation(); window.__builder.dropInto('${bid}', ${i});">
                        ${inner}
                    </div>
                `;
            }

            const escBg = String(bg).replace(/[<>"']/g, '');
            const chrome = `background:${escBg}; padding:${padding}; border:${border}; min-height:${minHeight};`;
            const outerStyle = isFullWidth
                ? `${chrome} width:100%; margin:0;`
                : `${chrome} max-width:1100px; margin:0 auto; width:100%;`;
            const radius = (this.preview && isFullWidth) ? '0' : '12px';

            return `
                <div style="border-radius:${radius}; ${outerStyle}">
                    <div style="display:grid; grid-template-columns: repeat(${effCols}, minmax(0, 1fr)); gap:${gap}; align-items:${align};">
                        ${slotsHtml}
                    </div>
                </div>
            `;
        },

        renderSimpleWidget(block) {
            const settings = block.settings || {};
            const esc = (s) => this.esc(s);
            const bid = block.id;
            const inline = this.preview ? '' : `class="rj-inline-edit" ondblclick="event.stopPropagation(); window.__builder.startInline(this, '${bid}')"`;
            const mobile = this.device === 'mobile';

            if (block.type === 'heading') {
                const level = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].includes(settings.level) ? settings.level : 'h2';
                const sizes = { h1: '2rem', h2: '1.5rem', h3: '1.25rem', h4: '1.125rem', h5: '1rem', h6: '0.875rem' };
                const mobileSizes = { h1: '1.6rem', h2: '1.3rem', h3: '1.15rem', h4: '1.05rem', h5: '1rem', h6: '0.875rem' };
                const aligns = { left: 'left', center: 'center', right: 'right' };
                const size = (mobile ? mobileSizes : sizes)[level];
                return `<${level} ${inline} style="font-size:${size}; font-weight:700; text-align:${aligns[settings.align] || 'left'}; color:${esc(settings.color || '#111827')}; padding:0.75rem 1rem; margin:0;">${esc(this.getBlockField(block, 'text') || 'Heading')}</${level}>`;
            }

            if (block.type === 'text') {
                const aligns = { left: 'left', center: 'center', right: 'right', justify: 'justify' };
                const sizes = { sm: '0.875rem', base: '1rem', lg: '1.125rem', xl: '1.25rem' };
                return `<p ${inline} style="font-size:${sizes[settings.size] || '1rem'}; text-align:${aligns[settings.align] || 'left'}; color:${esc(settings.color || '#374151')}; padding:0.75rem 1rem; line-height:1.6; white-space:pre-wrap; margin:0;">${esc(this.getBlockField(block, 'text') || 'Text')}</p>`;
            }

            if (block.type === 'image') {
                if (!settings.url) {
                    if (this.preview) return '';
                    return `<div style="padding:2rem 1rem; text-align:center; color:#9ca3af; font-size:0.75rem; background:#f3f4f6; border-radius:8px; margin:0.75rem 1rem;">
                                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="display:block; margin:0 auto 4px;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                                ${esc(this.t('Image'))}
                            </div>`;
                }
                const aligns = { left: '0 auto 0 0', center: '0 auto', right: '0 0 0 auto' };
                const widths = { auto: 'auto', full: '100%', half: '50%', third: '33.333%' };
                const rounded = { none: '0', sm: '4px', md: '8px', lg: '16px', full: '9999px' };
                const width = mobile ? '100%' : (widths[settings.width] || '100%');
                return `<figure style="padding:0.75rem 1rem; margin:0;"><img src="${esc(settings.url)}" alt="${esc(this.getBlockField(block, 'alt') || '')}" style="width:${width}; max-width:100%; border-radius:${rounded[settings.rounded] || '8px'}; margin:${aligns[settings.align] || '0 auto'}; display:block;"></figure>`;
            }

            const w = this.widgets.find(x => x.id === block.type);
            if (this.preview) return '';
            return `<div style="padding:14px 16px; margin:0.5rem 1rem; display:flex; align-items:center; gap:12px; border:1px dashed #d1d5db; border-radius:10px; background:#fafafa; color:#6b7280; font-size:12px;">
                        <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; color:#8b5cf6; flex-shrink:0;" class="rj-ico">${w && w.icon ? w.icon : ''}</span>
                        <span style="font-weight:600; color:#374151;">${esc(w ? w.name : block.type)}</span>
                    </div>`;
        },

        animationWrapper(html, settings, stateClass) {
            const dur = settings._animation_duration || 'md';
            const delay = settings._animation_delay || '0';
            const durations = { sm: '300ms', md: '600ms', lg: '1000ms', xl: '1500ms' };
            let style = `animation-duration:${durations[dur] || '600ms'};`;
            if (delay !== '0') style += ` animation-delay:${delay};`;
            return `<div class="rj-a rj-a-${this.esc(settings._animation)} ${stateClass}" style="${style}">${html}</div>`;
        },

        /* ───────── layers panel ───────── */
        toggleNav(id) {
            this.navCollapsed = Object.assign({}, this.navCollapsed, { [id]: !this.navCollapsed[id] });
        },

        navigatorHtml() {
            const nc = this.navCollapsed;
            const walk = (list, depth) => list.map(b => {
                const sel = this.selectedBlock === b.id;
                const isContainer = b.type === 'container';
                const s = b.settings || {};
                const hasKids = b.children && b.children.length;
                const open = !nc[b.id];
                const hint = isContainer
                    ? `${parseInt(s.columns) || 1} col`
                    : this.esc(String(this.getBlockField(b, 'text') || this.getBlockField(b, 'alt') || '').slice(0, 22));
                const anim = s._animation && s._animation !== 'none' ? ' <span style="color:#a78bfa;">✦</span>' : '';
                const hidden = (s._hide_desktop || s._hide_tablet || s._hide_mobile) ? ' <span style="color:#fbbf24;" title="Hidden on some devices">◌</span>' : '';
                const caret = hasKids
                    ? `<span class="rj-caret" data-open="${open}" onclick="event.stopPropagation(); window.__builder.toggleNav('${b.id}')">▶</span>`
                    : '<span class="rj-caret"></span>';

                const row = `
                    <div class="rj-nav" data-sel="${sel}" style="margin-left:${depth * 14}px;"
                         onclick="event.stopPropagation(); window.__builder.selectFromNav('${b.id}')"
                         ondblclick="event.stopPropagation(); window.__builder.openEdit('${b.id}')">
                        ${caret}
                        <span style="width:14px; text-align:center; color:#6d7080;">${isContainer ? '▣' : '◦'}</span>
                        <span class="rj-nlabel">${this.esc(this.blockLabel(b.type))}${anim}${hidden}</span>
                        <span class="rj-nhint">${hint}</span>
                        <span class="rj-nact">
                            <span title="${this.esc(this.t('Edit'))}" onclick="event.stopPropagation(); window.__builder.openEdit('${b.id}')">✎</span>
                            <span title="${this.esc(this.t('Duplicate'))}" onclick="event.stopPropagation(); window.__builder.duplicateBlock('${b.id}')">⧉</span>
                            <span title="${this.esc(this.t('Delete'))}" onclick="event.stopPropagation(); window.__builder.removeBlock('${b.id}')">✕</span>
                        </span>
                    </div>`;
                const kids = hasKids && open ? walk(b.children, depth + 1) : '';
                return row + kids;
            }).join('');
            return walk(this.blocks, 0);
        },

        /* ───────── history & saving ───────── */
        markDirty() { this.saved = false; },

        commitSoon(label) {
            this._pendingLabel = label || this.t('Edited');
            clearTimeout(this._historyTimer);
            this._historyTimer = setTimeout(() => this.pushHistory(this._pendingLabel), 500);
        },

        pushHistory(label) {
            clearTimeout(this._historyTimer);
            const snap = JSON.stringify(this.blocks);
            const cur = this.history[this.historyIndex];
            if (cur && cur.snap === snap) return;
            this.history = this.history.slice(0, this.historyIndex + 1);
            this.history.push({ snap, label: label || this.t('Edited'), t: Date.now() });
            this.historyIndex = this.history.length - 1;
            if (this.history.length > 80) { this.history.shift(); this.historyIndex--; }
        },

        restoreHistory(i) {
            this.historyIndex = i;
            this.blocks = JSON.parse(this.history[i].snap);
            this.selectedBlock = null;
            this.saved = this.history[i].snap === this.savedSnap;
        },

        undo() {
            this.pushHistory(this._pendingLabel);
            if (!this.canUndo) return;
            this.restoreHistory(this.historyIndex - 1);
        },

        redo() {
            if (!this.canRedo) return;
            this.restoreHistory(this.historyIndex + 1);
        },

        goToHistory(i) {
            this.pushHistory(this._pendingLabel);
            if (i === this.historyIndex || !this.history[i]) return;
            this.restoreHistory(i);
        },

        async save(silent) {
            if (this.saving) return;
            clearTimeout(this._historyTimer);
            this.pushHistory(this._pendingLabel);
            this.saving = true;
            const snap = JSON.stringify(this.blocks);
            try {
                const res = await fetch(L.updateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': L.csrf,
                        'X-HTTP-Method-Override': 'PUT',
                    },
                    body: JSON.stringify({
                        name: L.name,
                        content: { blocks: this.blocks },
                        status: L.status,
                    }),
                });
                if (res.ok || res.redirected) {
                    this.savedSnap = snap;
                    this.saved = JSON.stringify(this.blocks) === snap;
                    this.showToast(silent === true ? this.t('Auto-saved') : this.t('Saved'));
                } else if (res.status === 419) {
                    this.showToast(this.t('Session expired — reload the page and try again'), 'error');
                } else {
                    this.showToast(this.t('Save failed') + ' (' + res.status + ')', 'error');
                }
            } catch (e) {
                console.error(e);
                this.showToast(this.t('Network error — not saved'), 'error');
            } finally {
                this.saving = false;
            }
        },
    };
}

/* ───────── Posts widget ───────── */
.rj-posts {
    --rj-posts-gap: 20px;
    --rj-posts-radius: 8px;
    --rj-posts-bg: #ffffff;
    --rj-posts-title-color: #111827;
    --rj-posts-title-size: 18px;
    --rj-posts-text-color: #6b7280;
    --rj-posts-meta-color: #9ca3af;
}

.rj-posts-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--rj-posts-gap);
}

.rj-posts-cols-2 .rj-posts-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.rj-posts-cols-3 .rj-posts-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
.rj-posts-cols-4 .rj-posts-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }

.rj-posts-list .rj-posts-grid {
    grid-template-columns: 1fr;
}

.rj-posts-list .rj-post-card {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 20px;
    align-items: stretch;
}

.rj-posts-list .rj-post-image {
    height: 100%;
    min-height: 160px;
}

.rj-posts-masonry .rj-posts-grid {
    display: block;
    column-count: 3;
    column-gap: var(--rj-posts-gap);
}

.rj-posts-masonry.rj-posts-cols-2 .rj-posts-grid { column-count: 2; }
.rj-posts-masonry.rj-posts-cols-3 .rj-posts-grid { column-count: 3; }
.rj-posts-masonry.rj-posts-cols-4 .rj-posts-grid { column-count: 4; }

.rj-posts-masonry .rj-post-card {
    break-inside: avoid;
    margin-bottom: var(--rj-posts-gap);
    display: inline-block;
    width: 100%;
}

.rj-posts-carousel .rj-posts-grid {
    display: flex;
    gap: var(--rj-posts-gap);
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    padding-bottom: 12px;
}

.rj-posts-carousel .rj-post-card {
    flex: 0 0 calc((100% - (var(--rj-posts-gap) * 2)) / 3);
    scroll-snap-align: start;
    min-width: 240px;
}

.rj-posts-carousel.rj-posts-cols-2 .rj-post-card { flex-basis: calc((100% - var(--rj-posts-gap)) / 2); }
.rj-posts-carousel.rj-posts-cols-4 .rj-post-card { flex-basis: calc((100% - (var(--rj-posts-gap) * 3)) / 4); }

.rj-post-card {
    background: var(--rj-posts-bg);
    border-radius: var(--rj-posts-radius);
    overflow: hidden;
    transition: transform .25s, box-shadow .25s;
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.rj-posts-card-bordered .rj-post-card {
    border: 1px solid #e5e7eb;
}

.rj-posts-card-shadow .rj-post-card {
    box-shadow: 0 4px 16px rgba(17, 24, 39, .08);
}

.rj-posts-card-minimal .rj-post-card {
    background: transparent;
    border-radius: 0;
}

.rj-posts-hover-lift .rj-post-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(17, 24, 39, .12);
}

.rj-post-image {
    display: block;
    width: 100%;
    overflow: hidden;
    background: #f3f4f6;
    position: relative;
}

.rj-posts:not(.rj-posts-list) .rj-post-image {
    aspect-ratio: var(--rj-post-ratio, 16 / 9);
}

.rj-posts .rj-post-image img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .4s;
}

.rj-posts-hover-lift .rj-post-card:hover .rj-post-image img {
    transform: scale(1.04);
}

.rj-post-body {
    padding: 16px 18px 20px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
    min-width: 0;
}

.rj-posts-card-minimal .rj-post-body {
    padding-left: 0;
    padding-right: 0;
}

.rj-post-cats {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 2px;
}

.rj-post-cat {
    display: inline-block;
    padding: 2px 8px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    background: rgba(124, 58, 237, .1);
    color: #7c3aed;
    border-radius: 9999px;
}

.rj-post-title {
    font-size: var(--rj-posts-title-size);
    font-weight: 700;
    line-height: 1.3;
    margin: 0;
    color: var(--rj-posts-title-color);
    word-break: break-word;
}

.rj-post-title a {
    color: inherit;
    text-decoration: none;
    transition: color .15s;
}

.rj-post-title a:hover {
    color: #7c3aed;
}

.rj-post-excerpt {
    font-size: 14px;
    line-height: 1.55;
    color: var(--rj-posts-text-color);
    margin: 0;
    word-break: break-word;
}

.rj-post-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: var(--rj-posts-meta-color);
    margin-top: auto;
    padding-top: 8px;
}

.rj-post-meta-sep {
    opacity: .5;
}

.rj-post-read-more {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 13px;
    font-weight: 600;
    color: #7c3aed;
    text-decoration: none;
    margin-top: 6px;
    transition: gap .15s;
}

.rj-post-read-more:hover {
    gap: 8px;
}

.rj-posts-loadmore {
    text-align: center;
    padding: 32px 0 8px;
}

.rj-posts-btn-load {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 28px;
    background: #7c3aed;
    color: #ffffff;
    border: 0;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: opacity .15s;
    font-family: inherit;
}

.rj-posts-btn-load:hover {
    opacity: .9;
}

.rj-posts-empty {
    width: 100%;
}

@media (max-width: 900px) {
    .rj-posts-cols-3 .rj-posts-grid,
    .rj-posts-cols-4 .rj-posts-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .rj-posts-list .rj-post-card {
        grid-template-columns: 1fr;
    }

    .rj-posts-carousel .rj-post-card {
        flex-basis: calc((100% - var(--rj-posts-gap)) / 2);
    }
}

@media (max-width: 600px) {
    .rj-posts-cols-2 .rj-posts-grid,
    .rj-posts-cols-3 .rj-posts-grid,
    .rj-posts-cols-4 .rj-posts-grid {
        grid-template-columns: 1fr;
    }

    .rj-posts-masonry .rj-posts-grid {
        column-count: 1;
    }

    .rj-posts-carousel .rj-post-card {
        flex-basis: 85%;
    }
}

</script>
@endverbatim