<script>
function builderRender() {
    return {
        cssv(v) { return String(v == null ? '' : v).replace(/[^#\w(),.%\s-]/g, ''); },

        resp(s, name, fallback) {
            const dev = this.device;
            if (dev === 'desktop') {
                const v = s[name];
                return (v !== undefined && v !== null && v !== '') ? v : fallback;
            }
            const key = name + '_' + dev;
            let v = s[key];
            if (v === undefined || v === null || v === '') v = s[name];
            return (v !== undefined && v !== null && v !== '') ? v : fallback;
        },

        hideClasses(s) {
            return (s._hide_desktop ? ' rj-hide-d' : '') + (s._hide_tablet ? ' rj-hide-t' : '') + (s._hide_mobile ? ' rj-hide-m' : '');
        },

        toolbarBtn(icon, title, action, danger) {
            const hover = danger ? 'rgba(239,68,68,0.5)' : 'rgba(255,255,255,0.15)';
            return `<button type="button" title="${title}" onclick="event.stopPropagation(); ${action}" style="width:22px;height:22px;border-radius:9999px;background:transparent;border:0;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;line-height:1;" onmouseover="this.style.background='${hover}'" onmouseout="this.style.background='transparent'">${icon}</button>`;
        },

        dimCss(s, key) {
            const v = s[key];
            if (!v || typeof v !== 'object') return '';
            const u = ['px','%','em','rem','vh','vw'].includes(v.unit) ? v.unit : 'px';
            const t = (v.top !== '' && v.top != null) ? v.top + u : '0';
            const r = (v.right !== '' && v.right != null) ? v.right + u : '0';
            const b = (v.bottom !== '' && v.bottom != null) ? v.bottom + u : '0';
            const l = (v.left !== '' && v.left != null) ? v.left + u : '0';
            if (t === '0' && r === '0' && b === '0' && l === '0') return '';
            return t + ' ' + r + ' ' + b + ' ' + l;
        },

        dimResp(s, name) {
            if (this.device === 'desktop') return this.dimCss(s, name);
            const key = name + '_' + this.device;
            const css = this.dimCss(s, key);
            if (css) return css;
            return this.dimCss(s, name);
        },

        typoCss(s) {
            let css = '';
            const fs = this.resp(s, 'font_size', '');
            const fw = this.resp(s, 'font_weight', '');
            const lh = this.resp(s, 'line_height', '');
            const ls = this.resp(s, 'letter_spacing', '');
            const tt = this.resp(s, 'text_transform', '');
            if (fs) css += `font-size:${this.cssv(fs)};`;
            if (fw) css += `font-weight:${this.cssv(fw)};`;
            if (lh) css += `line-height:${this.cssv(lh)};`;
            if (ls) css += `letter-spacing:${this.cssv(ls)};`;
            if (tt && tt !== 'none') css += `text-transform:${this.cssv(tt)};`;
            return css;
        },

        boxShadowCss(v) {
            if (!v || v === 'none') return '';
            const map = {
                sm: '0 1px 2px rgba(0,0,0,0.06)',
                md: '0 4px 12px rgba(0,0,0,0.1)',
                lg: '0 12px 32px rgba(0,0,0,0.16)',
                xl: '0 24px 60px rgba(0,0,0,0.22)',
            };
            return map[v] ? `box-shadow:${map[v]};` : '';
        },

        bgCss(s) {
            const type = s.bg_type || 'none';
            if (type === 'none') return '';

            if (type === 'gradient') {
                const c1 = this.cssv(s.gradient_color_1 || '#7c3aed');
                const c2 = this.cssv(s.gradient_color_2 || '#ec4899');
                const angle = parseInt(s.gradient_angle) || 135;
                return `background:linear-gradient(${angle}deg, ${c1}, ${c2});`;
            }

            let css = '';
            if (s.bg_color) css += `background-color:${this.cssv(s.bg_color)};`;
            if (s.bg_image) {
                css += `background-image:url('${this.esc(s.bg_image)}');`;
                css += `background-position:${this.cssv(s.bg_position || 'center')};`;
                css += `background-size:${this.cssv(s.bg_size || 'cover')};`;
                css += `background-repeat:${this.cssv(s.bg_repeat || 'no-repeat')};`;
            }
            return css;
        },

        hoverVars(s) {
            const vars = [];
            if (s._hover_text_color) vars.push(`--h-tc:${this.cssv(s._hover_text_color)}`);
            if (s._hover_bg_color) vars.push(`--h-bg:${this.cssv(s._hover_bg_color)}`);
            if (s._hover_border_color) vars.push(`--h-bc:${this.cssv(s._hover_border_color)}`);
            return vars.join(';');
        },

        selfCss(s) {
            let css = '';
            if (s.align_self && s.align_self !== 'auto') css += `align-self:${this.cssv(s.align_self)};`;
            if (s.order != null && s.order !== '' && String(s.order) !== '0') css += `order:${parseInt(s.order) || 0};`;
            return css;
        },

        cssIdAttr(s) {
            if (!s._css_id) return '';
            const id = String(s._css_id).replace(/[^a-zA-Z0-9_-]/g, '');
            return id ? ` id="${this.esc(id)}"` : '';
        },

        dimWidths(s) {
            const raw = (s.column_widths || '').trim();
            if (!raw) return null;
            const parts = raw.split(',').map(x => x.trim()).filter(x => x !== '');
            const nums = parts.map(x => parseFloat(x)).filter(x => !isNaN(x) && x > 0);
            if (!nums.length) return null;
            return nums;
        },

        renderBlockHtml(block, parentKey, index) {
            const w = this.widgets.find(x => x.id === block.type);
            if (!w) return `<div style="padding:12px; font-size:12px; color:#dc2626;">${this.esc(this.t('Unknown widget'))}</div>`;

            const s = block.settings || {};
            const hiddenHere = !!s['_hide_' + this.device];
            const hasAnim = s._animation && s._animation !== 'none';

            let inner = block.type === 'container' ? this.renderContainer(block) : this.renderSimpleWidget(block);

            if (this.preview && inner === '') return '';

            if (hasAnim) {
                if (this.preview) inner = this.animationWrapper(inner, s, 'rj-wait');
                else if (this.animPlayId === block.id) inner = this.animationWrapper(inner, s, 'rj-play');
            }

            const hoverVars = this.hoverVars(s);
            const hasHover = (s._hover && s._hover !== 'none') || hoverVars;
            if (hasHover) {
                const cls = (s._hover && s._hover !== 'none') ? ` rj-h rj-h-${this.esc(s._hover)}` : ' rj-h';
                const styleAttr = hoverVars ? ` style="${hoverVars}"` : '';
                inner = `<div class="${cls.trim()}"${styleAttr}>${inner}</div>`;
            }

            const self = this.selfCss(s);
            if (self) inner = `<div style="${self}">${inner}</div>`;

            const customClass = s._css_class ? ' ' + this.esc(s._css_class) : '';
            const cssId = this.cssIdAttr(s);

            if (this.preview) return `<div${cssId} class="${(customClass + this.hideClasses(s)).trim()}" data-run="${this.previewRun}">${inner}</div>`;

            const isSelected = this.selectedBlock === block.id;
            const bid = block.id;
            const hiddenNote = hiddenHere
                ? `<div style="position:absolute;top:6px;right:8px;z-index:20;font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;background:#1a1030;color:#fbbf24;border-radius:9999px;padding:2px 8px;">${this.esc(this.t('Hidden on'))} ${this.esc(this.device)}</div>`
                : '';

            return `
                <div class="rj-block${customClass}"${cssId ? cssId.replace(' id=', ' data-css-id=') : ''} data-block-id="${bid}" style="position:relative; ${hiddenHere ? 'opacity:.35;' : ''}" onclick="event.stopPropagation(); window.__builder.selectBlock('${bid}')">
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

            const pick = (v, fallback) => (v && v !== 'auto') ? (parseInt(v) || fallback) : fallback;
            const colsT = pick(settings.columns_tablet, Math.min(cols, 2));
            const colsM = pick(settings.columns_mobile, 1);
            const fit = settings.distribute === 'content';

            const gapMap = { none: '0', sm: '0.5rem', md: '1rem', lg: '1.5rem', xl: '2rem' };
            const bg = settings.background || 'transparent';
            const gap = gapMap[settings.gap] || '1rem';

            const padding = this.dimResp(settings, 'padding_dim') || gapMap[settings.padding] || '1rem';
            const margin = this.dimResp(settings, 'margin_dim') || '';

            let minHeight = 'auto';
            if (settings.min_height === 'custom') {
                minHeight = this.cssv(settings.min_height_custom || 'auto');
            } else if (settings.min_height === 'screen') {
                minHeight = '100vh';
            } else if (settings.min_height && settings.min_height !== 'auto') {
                minHeight = { sm: '200px', md: '400px', lg: '600px', xl: '800px' }[settings.min_height] || 'auto';
            }

            const border = settings.border === 'solid' ? '2px solid #e5e7eb' : settings.border === 'dashed' ? '2px dashed #e5e7eb' : 'none';
            const radiusMap = { none: '0', sm: '4px', md: '8px', lg: '16px', full: '9999px' };
            const radius = radiusMap[settings.border_radius] || '12px';

            const alignMap = { start: 'flex-start', center: 'center', end: 'flex-end', stretch: 'stretch', baseline: 'baseline' };
            const ai = alignMap[settings.align_items || settings.align] || 'stretch';

            const jcMap = { start: 'flex-start', center: 'center', end: 'flex-end', 'space-between': 'space-between', 'space-around': 'space-around', 'space-evenly': 'space-evenly' };
            const jc = jcMap[settings.justify_content] || 'flex-start';

            const dir = ['row', 'column', 'row-reverse', 'column-reverse'].includes(settings.flex_direction) ? settings.flex_direction : 'row';
            const wrap = ['nowrap', 'wrap', 'wrap-reverse'].includes(settings.flex_wrap) ? settings.flex_wrap : 'wrap';

            const isFullWidth = settings.width === 'full';

            const children = block.children || [];
            const bid = block.id;

            const bgCss = this.bgCss(settings);
            const shadow = this.boxShadowCss(settings.box_shadow);

            if (this.preview && children.every(c => !c || (c.type === 'container' && (!c.children || !c.children.length) && (!c.settings || !c.settings.background || c.settings.background === 'transparent'))) && !bgCss && minHeight === 'auto' && border === 'none') {
                return '';
            }

            const widths = this.dimWidths(settings);
            const slotCount = Math.max(cols, children.length);
            let slotsHtml = '';

            for (let i = 0; i < slotCount; i++) {
                const child = children[i];
                const flexBasis = (widths && widths[i]) ? `--sw:${widths[i]}%;` : '';
                const slotStyle = `min-width:0;${flexBasis}`;

                if (this.preview) {
                    slotsHtml += `<div class="rj-slot${child ? this.hideClasses(child.settings || {}) : ''}" style="${slotStyle}">${child ? this.renderBlockHtml(child, bid, i) : ''}</div>`;
                    continue;
                }

                const inner = child
                    ? this.renderBlockHtml(child, bid, i)
                    : `<div style="min-height:80px; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#c3c7d0; pointer-events:none;">
                           <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                           <span style="font-size:9px; text-transform:uppercase; letter-spacing:.08em; margin-top:2px;">${this.esc(this.t('Drop here'))} · ${i + 1}</span>
                       </div>`;

                slotsHtml += `
                    <div class="rj-slot" style="position:relative; min-height:80px; min-width:0; ${flexBasis} border-radius:8px; transition:all .15s; border:2px ${child ? 'solid transparent' : 'dashed #e5e7eb'};"
                         ondragover="event.preventDefault(); event.stopPropagation(); this.classList.add('rj-drop');"
                         ondragleave="this.classList.remove('rj-drop');"
                         ondrop="event.preventDefault(); event.stopPropagation(); window.__builder.dropInto('${bid}', ${i});">
                        ${inner}
                    </div>
                `;
            }

            const chrome = `${bgCss} padding:${padding}; ${margin ? 'margin:' + margin + ';' : ''} border:${border}; min-height:${minHeight}; ${shadow}`;
            const outerStyle = isFullWidth
                ? `${chrome} width:100%; margin:0;`
                : `${chrome} max-width:1100px; margin:0 auto; width:100%;`;
            const useRadius = (this.preview && isFullWidth) ? '0' : radius;

            return `
                <div style="border-radius:${useRadius}; ${outerStyle}">
                    <div class="rj-cg${fit ? ' rj-fit' : ''}" style="--c:${cols}; --ct:${colsT}; --cm:${colsM}; --g:${gap}; --al:${ai}; --jc:${jc}; --dir:${dir}; --wrap:${wrap};">
                        ${slotsHtml}
                    </div>
                </div>
            `;
        },

        renderSimpleWidget(block) {
            const settings = block.settings || {};
            const esc = (s) => this.esc(s);
            const bid = block.id;
            const inlineCls = this.preview ? '' : ' rj-inline-edit';
            const inlineAttr = this.preview ? '' : `ondblclick="event.stopPropagation(); window.__builder.startInline(this, '${bid}')"`;
            const noNav = `onclick="event.preventDefault()"`;

            const padCss = this.dimResp(settings, 'padding_dim');
            const marCss = this.dimResp(settings, 'margin_dim');
            const padAttr = padCss ? `padding:${padCss};` : '';
            const marAttr = marCss ? `margin:${marCss};` : '';
            const typo = this.typoCss(settings);
            const bgCss = this.bgCss(settings);
            const shadow = this.boxShadowCss(settings.box_shadow);

            if (block.type === 'heading') {
                const level = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].includes(settings.level) ? settings.level : 'h2';
                const sizes = { h1: '2rem', h2: '1.5rem', h3: '1.25rem', h4: '1.125rem', h5: '1rem', h6: '0.875rem' };
                const mobileSizes = { h1: '1.6rem', h2: '1.3rem', h3: '1.15rem', h4: '1.05rem', h5: '1rem', h6: '0.875rem' };
                const aligns = { left: 'left', center: 'center', right: 'right' };
                const align = this.resp(settings, 'align', 'left');
                const pad = padAttr || 'padding:0.75rem 1rem;';
                const sizeOverride = settings.font_size ? '' : `--fs:${sizes[level]}; --fsm:${mobileSizes[level]};`;
                return `<${level} class="rj-hd${inlineCls}" ${inlineAttr} style="${sizeOverride} text-align:${aligns[align] || 'left'}; color:${this.cssv(settings.color || '#111827')}; ${pad} ${marAttr} ${bgCss} ${shadow} ${typo}">${esc(this.displayField(block, 'text') || 'Heading')}</${level}>`;
            }

            if (block.type === 'text') {
                const aligns = { left: 'left', center: 'center', right: 'right', justify: 'justify' };
                const sizes = { sm: '0.875rem', base: '1rem', lg: '1.125rem', xl: '1.25rem' };
                const align = this.resp(settings, 'align', 'left');
                const pad = padAttr || 'padding:0.75rem 1rem;';
                const sizeOverride = settings.font_size ? '' : `font-size:${sizes[settings.size] || '1rem'};`;
                return `<p class="${inlineCls.trim()}" ${inlineAttr} style="${sizeOverride} text-align:${aligns[align] || 'left'}; color:${this.cssv(settings.color || '#374151')}; ${pad} ${marAttr} ${bgCss} ${shadow} line-height:1.6; white-space:pre-wrap; margin:0; ${typo}">${esc(this.displayField(block, 'text') || 'Text')}</p>`;
            }

            if (block.type === 'image') {
                const urlRaw = settings.url || '';
                const url = this.isDynamicValue(urlRaw) ? '' : urlRaw;

                if (!url) {
                    if (this.preview) return '';
                    const hint = this.isDynamicValue(urlRaw)
                        ? '⚡ ' + this.getDynamicTagLabel(urlRaw)
                        : esc(this.t('Image'));
                    return `<div style="padding:2rem 1rem; text-align:center; color:#9ca3af; font-size:0.75rem; background:#f3f4f6; border-radius:8px; margin:0.75rem 1rem;">
                                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="display:block; margin:0 auto 4px;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                                ${hint}
                            </div>`;
                }

                const aligns = { left: '0 auto 0 0', center: '0 auto', right: '0 0 0 auto' };
                const widthMap = { auto: 'auto', full: '100%', half: '50%', third: '33.333%' };
                const roundedMap = { none: '0', sm: '4px', md: '8px', lg: '16px', full: '9999px' };

                let w = widthMap[settings.width] || '100%';
                if (settings.width === 'custom' && settings.custom_width) w = this.cssv(settings.custom_width);

                const h = settings.height ? this.cssv(settings.height) : 'auto';
                const fit = ['cover', 'contain', 'fill', 'none'].includes(settings.object_fit) ? settings.object_fit : 'cover';
                const radius = roundedMap[settings.rounded] || '8px';
                const opacity = (settings.opacity != null && settings.opacity !== '') ? parseInt(settings.opacity) / 100 : 1;
                const align = this.resp(settings, 'align', 'center');

                let imgStyle = `width:${w}; height:${h}; object-fit:${h !== 'auto' ? fit : 'fill'};`;
                imgStyle += `border-radius:${radius};`;
                imgStyle += `opacity:${opacity};`;
                if (shadow) imgStyle += shadow;
                imgStyle += `margin:${aligns[align] || '0 auto'}; display:block;`;

                let imgHtml = `<img src="${esc(url)}" alt="${esc(this.getBlockField(block, 'alt') || '')}" style="${imgStyle}">`;

                const link = settings.link || '';
                if (link && !this.preview) {
                    imgHtml = `<a href="${esc(link)}" target="${esc(settings.target || '_self')}" ${noNav} style="display:inline-block; text-decoration:none;">${imgHtml}</a>`;
                } else if (link) {
                    imgHtml = `<a href="${esc(link)}" target="${esc(settings.target || '_self')}" style="display:inline-block; text-decoration:none;">${imgHtml}</a>`;
                }

                const pad = padAttr || 'padding:0.75rem 1rem;';
                return `<figure style="${pad} ${marAttr} ${bgCss} margin:0;">${imgHtml}</figure>`;
            }

            if (block.type === 'logo') {
                const urlRaw = settings.url || '';
                const url = this.isDynamicValue(urlRaw) ? '' : urlRaw;
                const alt = this.getBlockField(block, 'alt') || '';
                const link = settings.link || '/';
                const target = settings.target || '_self';
                const w = settings.width || 'auto';
                const cw = settings.custom_width || '';
                const h = settings.height || 'auto';
                const align = settings.align || 'left';

                if (!url) {
                    if (this.preview) return '';
                    const hint = this.isDynamicValue(urlRaw)
                        ? '⚡ ' + this.getDynamicTagLabel(urlRaw)
                        : esc(this.t('Logo')) + ' — ' + esc(this.t('Select Image'));
                    return `<div style="padding:1.5rem 1rem; text-align:center; color:#9ca3af; font-size:0.75rem; background:#f3f4f6; border-radius:8px; margin:0.5rem 1rem;">
                                ${hint}
                            </div>`;
                }

                const jcMap = { left: 'flex-start', center: 'center', right: 'flex-end' };
                const widthMap = { auto: 'auto', xs: '80px', sm: '120px', md: '160px', lg: '200px', xl: '280px' };
                const heightMap = { auto: 'auto', xs: '32px', sm: '48px', md: '64px', lg: '80px', xl: '120px' };

                const widthValue = w === 'custom' && cw !== '' ? this.cssv(cw) : (widthMap[w] || 'auto');
                const heightValue = heightMap[h] || 'auto';

                let imgStyle = `display:block; max-width:100%; width:${widthValue}; height:${heightValue};`;
                if (h !== 'auto') imgStyle += ' object-fit:contain;';

                let imgHtml = `<img src="${esc(url)}" alt="${esc(alt)}" style="${imgStyle}">`;
                if (link) {
                    imgHtml = `<a href="${esc(link)}" target="${esc(target)}" ${noNav} style="display:inline-block; text-decoration:none;">${imgHtml}</a>`;
                }

                const pad = padAttr || 'padding:0.5rem 1rem;';
                return `<div style="display:flex; justify-content:${jcMap[align] || 'flex-start'}; ${pad} ${marAttr} ${bgCss}">${imgHtml}</div>`;
            }

            if (block.type === 'menu') {
                const menuId = String(settings.menu_id || '');
                const menuOption = this.menuOptions.find(m => String(m.id) === menuId);
                const menuTree = this.menuTrees[menuId] || null;
                const menuName = menuOption ? menuOption.label : null;
                const items = menuTree ? (menuTree.items || []) : [];

                if (!menuId || !menuName) {
                    if (this.preview) return '';
                    return `<div style="padding:14px 16px; margin:0.5rem 1rem; display:flex; align-items:center; gap:12px; border:1px dashed #d1d5db; border-radius:10px; background:#fafafa; color:#6b7280; font-size:12px;">
                                <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; color:#8b5cf6; flex-shrink:0;" class="rj-ico">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                </span>
                                <span style="font-weight:600; color:#374151;">${esc(this.t('Menu'))}</span>
                                <span style="color:#9ca3af;">— ${esc(this.t('— Select menu —'))}</span>
                            </div>`;
                }

                const layout = settings.layout || 'horizontal';
                const align = ['left', 'center', 'right'].includes(settings.align) ? settings.align : 'right';
                const gap = { none: '0', sm: '12px', md: '24px', lg: '36px', xl: '48px' }[settings.gap || 'md'];
                const fontSize = { sm: '13px', base: '15px', lg: '17px' }[settings.font_size || 'base'];
                const hoverStyle = ['underline', 'background', 'both'].includes(settings.hover_style) ? settings.hover_style : 'underline';
                const mobileBehavior = settings.mobile_behavior || 'hamburger';
                const jc = { left: 'flex-start', center: 'center', right: 'flex-end' }[align];

                const vars = [
                    `--mt:${this.cssv(settings.text_color || '#111827')}`,
                    `--mh:${this.cssv(settings.hover_color || '#7c3aed')}`,
                    `--ma:${this.cssv(settings.active_color || '#7c3aed')}`,
                    `--mbg:${this.cssv(settings.dropdown_bg || '#ffffff')}`,
                    `--mbd:${settings.dropdown_border === 'yes' ? '1px solid #e5e7eb' : '0'}`,
                    `--fs:${fontSize}`,
                    `--fw:${this.cssv(settings.font_weight || '500')}`,
                    `--gap:${gap}`,
                    `--dir:${layout === 'vertical' ? 'column' : 'row'}`,
                    `--ai:${layout === 'vertical' ? jc : 'center'}`,
                    `--jc:${layout === 'vertical' ? 'flex-start' : jc}`,
                    `--mjc:${jc}`,
                ].join(';');

                const renderItems = (list) => list.map(it => {
                    const kids = it.children && it.children.length > 0;
                    return `<li class="rj-mi">
                        <a href="${esc(it.url || '#')}" target="${esc(it.target || '_self')}" class="rj-ml" ${noNav}>${esc(it.label)}${kids ? ' <span class="rj-car">▾</span>' : ''}</a>
                        ${kids ? `<ul class="rj-dd">${renderItems(it.children)}</ul>` : ''}
                    </li>`;
                }).join('');

                const toggle = `event.stopPropagation(); const m = this.closest('.rj-menu'); const o = m.classList.toggle('rj-open'); this.setAttribute('aria-expanded', o);`;

                const infoBar = !this.preview
                    ? `<div style="display:flex; align-items:center; gap:10px; color:#6b7280; font-size:11px; font-weight:600; margin-bottom:6px; text-transform:uppercase; letter-spacing:.05em;">
                           <span style="color:#8b5cf6;">${esc(menuName)}</span>
                           <span style="color:#c4c7d3;">·</span>
                           <span>${layout === 'horizontal' ? 'Horizontal' : 'Vertical'} · ${align}${mobileBehavior === 'hamburger' ? ' · ☰' : ''}</span>
                       </div>`
                    : '';

                const pad = padAttr || 'padding:0.5rem 1rem;';
                return `<div style="${pad} ${marAttr} ${bgCss}">
                    ${infoBar}
                    <div class="rj-menu" data-hover="${hoverStyle}" data-mobile="${esc(mobileBehavior)}" data-align="${align}" style="${vars}">
                        <nav class="rj-menu-d"><ul>${renderItems(items)}</ul></nav>
                        <nav class="rj-menu-m">
                            <div class="rj-bm">
                                <button type="button" class="rj-bb" aria-label="Menu" aria-expanded="false" onclick="${toggle}">
                                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                </button>
                                <ul class="rj-bl">${renderItems(items)}</ul>
                            </div>
                        </nav>
                    </div>
                </div>`;
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
            if (delay !== '0') style += ` animation-delay:${this.cssv(delay)};`;
            return `<div class="rj-a rj-a-${this.esc(settings._animation)} ${stateClass}" style="${style}">${html}</div>`;
        },
    };
}
</script>