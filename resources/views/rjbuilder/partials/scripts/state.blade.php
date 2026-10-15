<script>
function builderState() {
    const L = window.RJ;

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

        panel: 'add',
        panelOpen: true,
        editTab: 'content',
        collapsed: {},
        navCollapsed: {},
        favs: rjStore.get('rj.favs', []),
        recents: rjStore.get('rj.recent', []),
        prefs: Object.assign({ outlines: true, autosave: false, zoom: 100 }, rjStore.get('rj.prefs', {})),

        device: 'desktop',
        preview: false,
        previewRun: 0,
        animPlayId: null,
        toast: { show: false, message: '', type: 'success' },

        fieldDevices: {},

        templateType: L.templateType,
        baseLocale: L.baseLocale,
        locales: (function (l) { return Array.isArray(l) ? l : Object.keys(l || {}); })(L.locales),
        activeLocale: L.baseLocale,
        translatableFields: ['text', 'alt', 'caption', 'title', 'subtitle', 'description', 'label', 'button_text', 'content', 'placeholder'],

        menuOptions: (function () {
            const m = L.menus || {};
            return Object.keys(m).map(id => ({ id: id, label: m[id] }));
        })(),

        menuTrees: L.menuItems || {},

        formOptions: (window.RJ_FORMS || []).map(f => ({ id: f.id, name: f.name })),

        termsByTaxonomy: window.RJ_TERMS || {},
        termSearch: {},
        termsTaxonomy: {},

        tags: L.tags || [],
        tagCategories: L.tagCategories || {},
        dynamicTagPicker: { open: false, targetField: null, options: {} },

        globalColors: L.globalColors || [],
        globalFonts: L.globalFonts || [],
        globalSpacing: L.globalSpacing || [],
        globalWidgets: L.globalWidgets || [],

        ai: {
            enabled: (L.ai && L.ai.enabled) || false,
            model: (L.ai && L.ai.model) || '',
            urls: (L.ai && L.ai.urls) || {},
            busy: false,
            panelOpen: false,
            tab: 'generate',
            generate: { prompt: '', context: '', tone: 'professional', length: 'medium', result: '' },
            improve: { mode: 'improve', result: '' },
            translate: { targetLocale: '', result: '' },
            suggest: { description: '', result: [] },
        },

        devices: [
            { id: 'desktop', label: 'Desktop', svg: rjSVG('<path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>') },
            { id: 'tablet',  label: 'Tablet',  svg: rjSVG('<rect x="5" y="2" width="14" height="20" rx="2"/><path d="M11 18h2"/>') },
            { id: 'mobile',  label: 'Mobile',  svg: rjSVG('<rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/>') },
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
            { name: '_css_id', label: 'CSS ID', type: 'text' },
            { name: '_css_class', label: 'Custom CSS class', type: 'text' },
        ],

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

        get globalColorList() {
            return (this.globalColors || []).map(c => ({
                id: c.id,
                name: c.name || c.id,
                color: c.color || '#000000',
            }));
        },

        get globalFontList() {
            return (this.globalFonts || []).map(f => ({
                id: f.id,
                name: f.name || f.id,
                family: f.family || 'Inter, sans-serif',
                weight: f.weight || '400',
            }));
        },

        get globalSpacingList() {
            return (this.globalSpacing || []).map(s => ({
                id: s.id,
                name: s.name || s.id,
                value: s.value || '0',
            }));
        },

        get globalWidgetList() {
            return (this.globalWidgets || []).map(g => ({
                id: g.id,
                name: g.name,
                slug: g.slug,
                type: g.type,
                description: g.description || '',
            }));
        },

        get dynamicTagList() {
            return (this.tags || []).map(t => ({
                id: t.id,
                name: t.name,
                category: t.category,
                icon: t.icon,
                description: t.description || '',
                options: t.options || [],
            }));
        },

        get dynamicTagGrouped() {
            const groups = {};
            const cats = this.tagCategories || {};

            this.dynamicTagList.forEach(tag => {
                const key = tag.category || 'other';
                if (!groups[key]) {
                    groups[key] = {
                        key,
                        label: (cats[key] && cats[key].label) || key,
                        icon: (cats[key] && cats[key].icon) || '•',
                        items: [],
                    };
                }
                groups[key].items.push(tag);
            });

            return Object.values(groups);
        },

        get taxonomyList() {
            const map = this.termsByTaxonomy || {};
            return Object.keys(map).map(slug => ({
                slug,
                label: slug.charAt(0).toUpperCase() + slug.slice(1).replace(/[-_]/g, ' '),
            }));
        },

        get postTypeList() {
            return (window.RJ_POST_TYPES || []).map(pt => ({
                id: pt.id,
                slug: pt.slug,
                label: pt.name || pt.slug,
                singular: pt.singular_name || pt.slug,
            }));
        },

        get canvasStyle() {
            const z = this.prefs.zoom;
            const zoom = (!this.preview && z !== 100) ? ` zoom:${z / 100};` : '';

            if (this.preview) {
                if (this.device === 'desktop') {
                    return 'max-width:100%; min-width:900px; background:#fff; min-height:100%;';
                }
                const w = this.device === 'tablet' ? '820px' : '420px';
                return `max-width:${w}; background:#fff; box-shadow:0 0 0 1px #cfd3db, 0 20px 50px rgba(17,24,39,.18); margin-top:24px; margin-bottom:24px; border-radius:18px; overflow:visible;`;
            }

            if (this.device === 'desktop') {
                return 'max-width:1152px; min-width:900px;' + zoom;
            }
            const w = this.device === 'tablet' ? '820px' : '420px';
            return `max-width:${w}; padding:24px 12px;${zoom}`;
        },

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
                if (this.dynamicTagPicker.open) { this.closeTagPicker(); return; }
                if (this.ai.panelOpen) { this.closeAiPanel(); return; }
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

        openPanel(name) {
            if (this.shownPanel === name && this.panelOpen) { this.panelOpen = false; return; }
            this.panel = name;
            this.panelOpen = true;
        },

        isFav(id) { return this.favs.includes(id); },
        toggleFav(id) {
            this.favs = this.isFav(id) ? this.favs.filter(x => x !== id) : [...this.favs, id];
            rjStore.set('rj.favs', this.favs);
        },
        touchRecent(id) {
            if (id === 'container') return;
            this.recents = [id, ...this.recents.filter(x => x !== id)].slice(0, 6);
            rjStore.set('rj.recent', this.recents);
        },
        isCollapsed(key) { return !this.widgetSearch && !!this.collapsed[key]; },
        toggleSec(key) { this.collapsed = Object.assign({}, this.collapsed, { [key]: !this.collapsed[key] }); },
        setPref(key, value) {
            this.prefs = Object.assign({}, this.prefs, { [key]: value });
            rjStore.set('rj.prefs', this.prefs);
        },

        ago(ts) {
            const s = Math.round((Date.now() - ts) / 1000);
            if (s < 10) return this.t('now');
            if (s < 60) return s + 's';
            if (s < 3600) return Math.floor(s / 60) + 'm';
            return Math.floor(s / 3600) + 'h';
        },

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

        isResponsiveField(field) {
            const types = ['text', 'number', 'range', 'dimensions'];
            if (types.includes(field.type)) return true;
            return /font_size|line_height|letter_spacing|align|padding|margin|gap|column_widths|min_height/i.test(field.name);
        },

        getFieldDevice(name) {
            return this.fieldDevices[name] || 'desktop';
        },

        setFieldDevice(name, device) {
            this.fieldDevices = Object.assign({}, this.fieldDevices, { [name]: device });
        },

        cycleFieldDevice(name) {
            const cur = this.getFieldDevice(name);
            const next = { desktop: 'tablet', tablet: 'mobile', mobile: 'desktop' }[cur] || 'desktop';
            this.setFieldDevice(name, next);
        },

        fieldKey(name) {
            const dev = this.getFieldDevice(name);
            return dev === 'desktop' ? name : name + '_' + dev;
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
            const key = this.fieldKey(field.name);

            if (b) {
                let v = this.getBlockField(b, key);
                if (v !== undefined && v !== null && v !== '') return v;

                if (key !== field.name) {
                    v = this.getBlockField(b, field.name);
                    if (v !== undefined && v !== null) return v;
                }
            }
            return field.default !== undefined ? field.default : '';
        },

        isDefault(field) {
            const b = this.currentBlock;
            if (!b) return true;
            const key = this.fieldKey(field.name);
            const v = this.getBlockField(b, key);
            if (v === undefined || v === null || v === '') return key === field.name;
            const d = field.default !== undefined ? field.default : '';

            if (field.type === 'dimensions') {
                const empty = !v || typeof v !== 'object' ||
                    ((v.top === '' || v.top == null) && (v.right === '' || v.right == null) &&
                     (v.bottom === '' || v.bottom == null) && (v.left === '' || v.left == null));
                return empty;
            }

            if (field.type === 'toggle') return !v && !d;

            return String(v) === String(d);
        },

        resetField(field) {
            const b = this.currentBlock;
            if (!b) return;
            const key = this.fieldKey(field.name);

            if (this.isTranslatableField(key) && this.activeLocale !== this.baseLocale) {
                const tr = (b.settings.translations || {})[this.activeLocale];
                if (tr) delete tr[key];
            } else {
                const def = field.default !== undefined ? field.default : '';
                const copy = (def && typeof def === 'object') ? JSON.parse(JSON.stringify(def)) : def;
                this.setBlockField(b, key, copy);
            }
            this.markDirty();
            this.commitSoon(this.t('Edited') + ' ' + this.blockLabel(b.type));
        },

        displayField(block, name) {
            const value = this.getBlockField(block, name);

            if (this.isDynamicValue(value)) {
                return '⚡ ' + this.getDynamicTagLabel(value);
            }

            return value || '';
        },

        openTagPicker(field) {
            this.dynamicTagPicker = {
                open: true,
                targetField: field.name,
                options: {},
            };
        },

        closeTagPicker() {
            this.dynamicTagPicker = { open: false, targetField: null, options: {} };
        },

        applyTag(tag) {
            if (!this.currentBlock || !this.dynamicTagPicker.targetField) return;

            const field = this.dynamicTagPicker.targetField;
            const key = this.fieldKey(field);

            const payload = {
                source: 'dynamic',
                tag: tag.id,
                options: Object.assign({}, this.dynamicTagPicker.options),
            };

            this.setBlockField(this.currentBlock, key, payload);
            this.markDirty();
            this.commitSoon(this.t('Edited') + ' ' + this.blockLabel(this.currentBlock.type));
            this.closeTagPicker();
        },

        clearTag(field) {
            if (!this.currentBlock) return;
            const key = this.fieldKey(field);
            this.setBlockField(this.currentBlock, key, '');
            this.markDirty();
            this.commitSoon(this.t('Edited') + ' ' + this.blockLabel(this.currentBlock.type));
        },

        isDynamicValue(value) {
            return value && typeof value === 'object' && value.source === 'dynamic' && value.tag;
        },

        getDynamicTagLabel(value) {
            if (!this.isDynamicValue(value)) return '';
            const tag = this.dynamicTagList.find(t => t.id === value.tag);
            return tag ? tag.name : value.tag;
        },

        getTermsTaxonomy(fieldName) {
            if (this.termsTaxonomy[fieldName]) return this.termsTaxonomy[fieldName];

            const field = this.currentSchema.find(f => f.name === fieldName);
            if (field && field.taxonomy) return field.taxonomy;

            return this.taxonomyList[0]?.slug || 'category';
        },

        setTermsTaxonomy(fieldName, slug) {
            this.termsTaxonomy = Object.assign({}, this.termsTaxonomy, { [fieldName]: slug });
        },

        getTermIds(fieldName) {
            const value = this.currentSettings[fieldName] || '';

            if (Array.isArray(value)) {
                return value.map(Number).filter(x => !isNaN(x) && x > 0);
            }

            return String(value)
                .split(',')
                .map(x => x.trim())
                .filter(x => x !== '')
                .map(Number)
                .filter(x => !isNaN(x) && x > 0);
        },

        isTermSelected(fieldName, termId) {
            return this.getTermIds(fieldName).includes(Number(termId));
        },

        toggleTerm(fieldName, termId) {
            const ids = this.getTermIds(fieldName);
            const nid = Number(termId);
            const idx = ids.indexOf(nid);

            if (idx === -1) ids.push(nid);
            else ids.splice(idx, 1);

            this.updateSetting(fieldName, ids.join(','));
        },

        clearTerms(fieldName) {
            this.updateSetting(fieldName, '');
        },

        selectedTermCount(fieldName) {
            return this.getTermIds(fieldName).length;
        },

        filteredTermsFor(fieldName) {
            const slug = this.getTermsTaxonomy(fieldName);
            const terms = (this.termsByTaxonomy || {})[slug] || [];
            const q = (this.termSearch[fieldName] || '').trim().toLowerCase();

            if (!q) return terms;

            return terms.filter(t => String(t.name || '').toLowerCase().includes(q));
        },

        insertGlobalWidget(id) {
            const gw = this.globalWidgetList.find(w => w.id === id);
            if (!gw) return;

            const block = {
                id: this.generateId(),
                type: gw.type,
                settings: {},
                children: [],
                global_id: gw.id,
                global_name: gw.name,
            };

            if (this.selectedBlock) {
                const found = this.findBlockAndParent(this.selectedBlock);
                if (found && found.parent && found.parent.type === 'container') {
                    found.list.splice(found.index + 1, 0, block);
                } else {
                    this.blocks.push(block);
                }
            } else {
                this.blocks.push(block);
            }

            this.selectedBlock = block.id;
            this.markDirty();
            this.pushHistory(this.t('Added') + ' ' + gw.name);
            this.scrollToBlock(block.id);
        },

        unlinkGlobalWidget(id) {
            const found = this.findBlockAndParent(id);
            if (!found) return;
            delete found.block.global_id;
            delete found.block.global_name;
            this.markDirty();
            this.pushHistory(this.t('Unlinked global widget'));
        },

        openAiPanel(tab) {
            this.ai.panelOpen = true;
            if (tab) this.ai.tab = tab;
        },

        closeAiPanel() {
            this.ai.panelOpen = false;
        },

        async aiGenerateText() {
            if (!this.ai.enabled || this.ai.busy) return;
            if (!this.ai.generate.prompt.trim()) return;

            this.ai.busy = true;
            this.ai.generate.result = '';

            try {
                const res = await fetch(this.ai.urls.generateText, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': L.csrf,
                    },
                    body: JSON.stringify({
                        prompt: this.ai.generate.prompt,
                        context: this.ai.generate.context,
                        tone: this.ai.generate.tone,
                        length: this.ai.generate.length,
                        language: this.activeLocale || this.baseLocale,
                    }),
                });

                const data = await res.json();

                if (data.success) {
                    this.ai.generate.result = data.text || '';
                } else {
                    this.showToast(data.error || this.t('AI request failed.'), 'error');
                }
            } catch (e) {
                this.showToast(this.t('Network error'), 'error');
            } finally {
                this.ai.busy = false;
            }
        },

        async aiImproveText(mode) {
            if (!this.ai.enabled || this.ai.busy) return;
            if (!this.currentBlock) return;

            const field = (this.currentBlock.type === 'heading' || this.currentBlock.type === 'text') ? 'text' : null;
            if (!field) return;

            const current = String(this.getBlockField(this.currentBlock, field) || '').trim();

            if (!current || this.isDynamicValue(current)) {
                this.showToast(this.t('No text to improve.'), 'error');
                return;
            }

            this.ai.busy = true;

            try {
                const res = await fetch(this.ai.urls.improveText, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': L.csrf,
                    },
                    body: JSON.stringify({
                        text: current,
                        mode,
                        language: this.activeLocale || this.baseLocale,
                    }),
                });

                const data = await res.json();

                if (data.success && data.text) {
                    this.setBlockField(this.currentBlock, field, data.text);
                    this.markDirty();
                    this.commitSoon(this.t('AI improved'));
                    this.showToast(this.t('AI improved'));
                } else {
                    this.showToast(data.error || this.t('AI request failed.'), 'error');
                }
            } catch (e) {
                this.showToast(this.t('Network error'), 'error');
            } finally {
                this.ai.busy = false;
            }
        },

        async aiTranslateText() {
            if (!this.ai.enabled || this.ai.busy) return;
            if (!this.currentBlock || !this.ai.translate.targetLocale) return;

            const field = (this.currentBlock.type === 'heading' || this.currentBlock.type === 'text') ? 'text' : null;
            if (!field) return;

            const current = String(this.getBlockField(this.currentBlock, field) || '').trim();

            if (!current || this.isDynamicValue(current)) {
                this.showToast(this.t('No text to translate.'), 'error');
                return;
            }

            this.ai.busy = true;

            try {
                const res = await fetch(this.ai.urls.translateText, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': L.csrf,
                    },
                    body: JSON.stringify({
                        text: current,
                        target_locale: this.ai.translate.targetLocale,
                    }),
                });

                const data = await res.json();

                if (data.success && data.text) {
                    this.setBlockField(this.currentBlock, field, data.text);
                    this.markDirty();
                    this.commitSoon(this.t('AI translated'));
                    this.showToast(this.t('AI translated'));
                } else {
                    this.showToast(data.error || this.t('AI request failed.'), 'error');
                }
            } catch (e) {
                this.showToast(this.t('Network error'), 'error');
            } finally {
                this.ai.busy = false;
            }
        },

        async aiSuggestSection() {
            if (!this.ai.enabled || this.ai.busy) return;
            if (!this.ai.suggest.description.trim()) return;

            this.ai.busy = true;
            this.ai.suggest.result = [];

            try {
                const res = await fetch(this.ai.urls.suggestSection, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': L.csrf,
                    },
                    body: JSON.stringify({
                        description: this.ai.suggest.description,
                        language: this.activeLocale || this.baseLocale,
                    }),
                });

                const data = await res.json();

                if (data.success && Array.isArray(data.blocks)) {
                    this.ai.suggest.result = data.blocks;
                } else {
                    this.showToast(data.error || this.t('AI request failed.'), 'error');
                }
            } catch (e) {
                this.showToast(this.t('Network error'), 'error');
            } finally {
                this.ai.busy = false;
            }
        },

        aiInsertSection(blocks) {
            if (!Array.isArray(blocks) || !blocks.length) return;

            blocks.forEach(b => this.blocks.push(b));

            this.selectedBlock = blocks[0].id || null;
            this.markDirty();
            this.pushHistory(this.t('AI section inserted'));
            this.showToast(this.t('AI section inserted'));
            this.closeAiPanel();
            this.scrollToBlock(this.selectedBlock);
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
    };
}
</script>