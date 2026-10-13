@verbatim
<script>
function builderRender() {
    return {
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

            if (block.type === 'logo') {
                const url = settings.url || '';
                const alt = this.getBlockField(block, 'alt') || '';
                const link = settings.link || '/';
                const target = settings.target || '_self';
                const w = settings.width || 'auto';
                const cw = settings.custom_width || '';
                const h = settings.height || 'auto';
                const align = settings.align || 'left';

                if (!url) {
                    if (this.preview) return '';
                    return `<div style="padding:1.5rem 1rem; text-align:center; color:#9ca3af; font-size:0.75rem; background:#f3f4f6; border-radius:8px; margin:0.5rem 1rem;">
                                ${esc(this.t('Logo'))} — ${esc(this.t('Select Image'))}
                            </div>`;
                }

                const alignMap = { left: '0 auto 0 0', center: '0 auto', right: '0 0 0 auto' };
                const widthMap = { auto: 'auto', xs: '80px', sm: '120px', md: '160px', lg: '200px', xl: '280px' };
                const heightMap = { auto: 'auto', xs: '32px', sm: '48px', md: '64px', lg: '80px', xl: '120px' };

                const widthValue = w === 'custom' && cw !== '' ? cw : (widthMap[w] || 'auto');
                const heightValue = heightMap[h] || 'auto';

                let imgStyle = `display:block; max-width:100%; width:${widthValue};`;
                imgStyle += ` height:${heightValue};`;
                if (h !== 'auto') imgStyle += ' object-fit:contain;';

                let imgHtml = `<img src="${esc(url)}" alt="${esc(alt)}" style="${imgStyle}">`;

                if (link) {
                    imgHtml = `<a href="${esc(link)}" target="${esc(target)}" style="display:inline-block; text-decoration:none;">${imgHtml}</a>`;
                }

                return `<div style="display:inline-block; margin:${alignMap[align] || '0 auto 0 0'}; padding:0.5rem 1rem;">${imgHtml}</div>`;
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
                const align = settings.align || 'right';
                const gap = { none: '0', sm: '12px', md: '24px', lg: '36px', xl: '48px' }[settings.gap || 'md'];
                const fontSize = { sm: '13px', base: '15px', lg: '17px' }[settings.font_size || 'base'];
                const fontWeight = settings.font_weight || '500';
                const textColor = settings.text_color || '#111827';
                const hoverColor = settings.hover_color || '#7c3aed';
                const hoverStyle = settings.hover_style || 'underline';
                const dropdownBg = settings.dropdown_bg || '#ffffff';
                const dropdownBorder = settings.dropdown_border === 'yes';
                const mobileBehavior = settings.mobile_behavior || 'hamburger';

                const isNarrow = this.device === 'mobile' || this.device === 'tablet';
                const useHamburger = isNarrow && mobileBehavior === 'hamburger';

                const alignMap = { left: 'flex-start', center: 'center', right: 'flex-end' };
                const justifyMap = { left: 'flex-start', center: 'center', right: 'flex-end' };
                const flexDir = layout === 'vertical' ? 'column' : 'row';
                const alignItems = layout === 'vertical' ? alignMap[align] : 'center';
                const justifyContent = layout === 'vertical' ? 'flex-start' : justifyMap[align];

                const uid = 'rj-menu-' + Math.random().toString(36).slice(2, 9);

                const renderItems = (list, depth) => {
                    return list.map(it => {
                        const hasKids = it.children && it.children.length > 0;
                        const itemStyle = `display:inline-block; color:${textColor}; font-size:${fontSize}; font-weight:${fontWeight}; text-decoration:none; padding:8px 12px; border-radius:6px; border-bottom:2px solid transparent; transition:all .15s; white-space:nowrap;`;

                        return `<li class="rj-mi" style="position:relative; list-style:none;">
                            <a href="${esc(it.url || '#')}" target="${esc(it.target || '_self')}" class="rj-hover-item" style="${itemStyle}">${esc(it.label)}${hasKids ? ' <span style="font-size:9px; opacity:.6;">▾</span>' : ''}</a>
                            ${hasKids ? `<ul class="rj-dropdown" style="position:absolute; top:100%; left:0; min-width:180px; background:${dropdownBg}; ${dropdownBorder ? 'border:1px solid #e5e7eb;' : ''} border-radius:8px; box-shadow:0 12px 30px rgba(0,0,0,.12); padding:6px; margin:6px 0 0; opacity:0; visibility:hidden; transform:translateY(-4px); transition:all .15s; z-index:50; list-style:none;">${renderItems(it.children, depth + 1)}</ul>` : ''}
                        </li>`;
                    }).join('');
                };

                const desktopNav = `
                    <nav class="${uid}-nav" style="width:100%;">
                        <ul style="list-style:none; margin:0; padding:0; display:flex; flex-direction:${flexDir}; gap:${gap}; align-items:${alignItems}; justify-content:${justifyContent}; flex-wrap:wrap;">
                            ${renderItems(items, 0)}
                        </ul>
                    </nav>
                `;

                const hamburgerNav = `
                    <nav class="${uid}-nav" style="width:100%;">
                        <div style="position:relative; display:inline-block;">
                            <input type="checkbox" id="${uid}-toggle" class="rj-burger-toggle" style="display:none;">
                            <label for="${uid}-toggle" class="rj-burger-btn" style="display:inline-flex; align-items:center; justify-content:center; width:42px; height:42px; border-radius:10px; background:#f3f4f6; cursor:pointer; color:${textColor}; transition:all .15s;">
                                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </label>
                            <ul class="rj-burger-list" style="list-style:none; margin:0; padding:8px; position:absolute; top:calc(100% + 8px); left:0; min-width:200px; max-width:280px; background:${dropdownBg}; ${dropdownBorder ? 'border:1px solid #e5e7eb;' : ''} border-radius:10px; box-shadow:0 12px 30px rgba(0,0,0,.15); opacity:0; visibility:hidden; transform:translateY(-4px); transition:all .15s; z-index:50;">
                                ${renderItems(items, 0)}
                            </ul>
                        </div>
                    </nav>
                `;

                const baseCss = `
                    .${uid}-nav .rj-hover-item:hover { color:${hoverColor} !important; ${hoverStyle === 'underline' || hoverStyle === 'both' ? 'border-bottom-color:' + hoverColor + ' !important;' : ''} ${hoverStyle === 'background' || hoverStyle === 'both' ? 'background:' + hoverColor + '15 !important;' : ''} }
                    .${uid}-nav li:hover > .rj-dropdown { opacity:1 !important; visibility:visible !important; transform:translateY(0) !important; }
                    .${uid}-nav .rj-dropdown .rj-hover-item:hover { background:${hoverColor}10 !important; color:${hoverColor} !important; }
                    .${uid}-nav .rj-dropdown .rj-hover-item { display:block; }
                    .${uid}-nav .rj-burger-toggle:checked ~ .rj-burger-list { opacity:1 !important; visibility:visible !important; transform:translateY(0) !important; }
                    .${uid}-nav .rj-burger-btn:hover { background:${hoverColor}15; color:${hoverColor}; }
                    .${uid}-nav .rj-burger-list .rj-mi { display:block; width:100%; }
                    .${uid}-nav .rj-burger-list .rj-hover-item { display:block !important; width:100%; text-align:left; padding:10px 12px !important; }
                    .${uid}-nav .rj-burger-list .rj-dropdown { position:static !important; opacity:1 !important; visibility:visible !important; transform:none !important; box-shadow:none !important; margin:2px 0 4px 16px !important; padding-left:8px !important; border-left:2px solid ${hoverColor}40 !important; }
                `;

                const infoBar = !this.preview
                    ? `<div style="display:flex; align-items:center; gap:10px; color:#6b7280; font-size:11px; font-weight:600; margin-bottom:6px; text-transform:uppercase; letter-spacing:.05em;">
                           <span style="color:#8b5cf6;">${esc(menuName)}</span>
                           <span style="color:#c4c7d3;">·</span>
                           <span>${useHamburger ? 'Hamburger' : (layout === 'horizontal' ? 'Horizontal' : 'Vertical')} · ${align}</span>
                       </div>`
                    : '';

                return `<style>${baseCss}</style>
                    <div style="padding:0.5rem 1rem;">
                        ${infoBar}
                        ${useHamburger ? hamburgerNav : desktopNav}
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
            if (delay !== '0') style += ` animation-delay:${delay};`;
            return `<div class="rj-a rj-a-${this.esc(settings._animation)} ${stateClass}" style="${style}">${html}</div>`;
        },
    };
}
</script>
@endverbatim