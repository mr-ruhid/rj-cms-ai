@verbatim
<script>
function builderPanels() {
    return {
        /* ───────── navigator panel ───────── */
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

        /* ───────── inline editing ───────── */
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

        /* ───────── media picker bridge ───────── */
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

        /* ───────── device + preview ───────── */
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

        /* ───────── animation playback ───────── */
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
    };
}
</script>
@endverbatim