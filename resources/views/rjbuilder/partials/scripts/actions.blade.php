@verbatim
<script>
function builderActions() {
    return {
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

                const source = found.block;
                if (d.id === containerId) return;
                if (source.children && this.findBlock(containerId, source.children)) return;

                block = this.detach(found);
            }

            if (!container.children) container.children = [];
            while (container.children.length <= slotIndex) container.children.push(this.emptySlot());

            const slot = container.children[slotIndex];

            const isEmptyContainer = slot && slot.type === 'container' &&
                (!slot.children || slot.children.length === 0) &&
                (!slot.settings || (!slot.settings.background && !slot.settings.bg_type && (!slot.settings.padding_dim || slot.settings.padding_dim.top === '')));

            if (isEmptyContainer) {
                if (!slot.children) slot.children = [];
                slot.children.push(block);
            } else if (slot && slot.type === 'container') {
                const cols = parseInt(slot.settings?.columns) || 1;
                if (cols > 1 && slot.children && slot.children.length < cols) {
                    slot.children.push(block);
                } else {
                    if (!slot.children) slot.children = [];
                    slot.children.push(block);
                }
            } else {
                const wrap = this.emptySlot();
                wrap.children = [slot, block];
                container.children[slotIndex] = wrap;
            }

            this.selectedBlock = block.id;
            this.markDirty();
            this.pushHistory((d.type === 'new' ? this.t('Added') : this.t('Moved')) + ' ' + this.blockLabel(block.type));
            if (d.type === 'new') this.playAnimation(block.id);
            this.scrollToBlock(block.id);
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
            const key = this.fieldKey(name);
            this.setBlockField(this.currentBlock, key, value);
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
                const res = await fetch(window.RJ.updateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.RJ.csrf,
                        'X-HTTP-Method-Override': 'PUT',
                    },
                    body: JSON.stringify({
                        name: window.RJ.name,
                        content: { blocks: this.blocks },
                        status: window.RJ.status,
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
</script>
@endverbatim