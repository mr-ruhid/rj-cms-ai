@php
    $popupResolver = app(\App\Services\PopupResolver::class);
    $popups = $popupResolver->allToArray();
@endphp

@if(!empty($popups))
<div id="rj-popups" style="display:none;">
    @foreach($popups as $popup)
        <div class="rj-popup-data"
             data-popup-id="{{ $popup['id'] }}"
             data-trigger="{{ $popup['settings']['trigger'] }}"
             data-trigger-value="{{ $popup['settings']['trigger_value'] }}"
             data-delay="{{ $popup['settings']['delay'] }}"
             data-frequency="{{ $popup['settings']['frequency'] }}"
             data-position="{{ $popup['settings']['position'] }}"
             data-width="{{ $popup['settings']['width'] }}"
             data-overlay="{{ $popup['settings']['overlay'] ? '1' : '0' }}"
             data-overlay-color="{{ $popup['settings']['overlay_color'] }}"
             data-overlay-opacity="{{ $popup['settings']['overlay_opacity'] }}"
             data-close-overlay="{{ $popup['settings']['close_on_overlay'] ? '1' : '0' }}"
             data-close-button="{{ $popup['settings']['close_button'] ? '1' : '0' }}"
             data-animation="{{ $popup['settings']['animation'] }}"
             data-animation-duration="{{ $popup['settings']['animation_duration'] }}"
             data-auto-close="{{ $popup['settings']['auto_close'] }}">
            <template class="rj-popup-content">{!! $popup['html'] !!}</template>
        </div>
    @endforeach
</div>

<style>
    .rj-popup-overlay {
        position: fixed; inset: 0; z-index: 9998;
        display: none; align-items: center; justify-content: center;
        padding: 20px;
    }
    .rj-popup-overlay.rj-active { display: flex; }
    .rj-popup-overlay .rj-popup-bg {
        position: absolute; inset: 0;
    }
    .rj-popup-box {
        position: relative; z-index: 9999;
        background: #fff; border-radius: 16px;
        max-height: 90vh; overflow-y: auto;
        box-shadow: 0 25px 60px rgba(0,0,0,.3);
    }
    .rj-popup-box[data-width="sm"] { max-width: 400px; width: 100%; }
    .rj-popup-box[data-width="md"] { max-width: 560px; width: 100%; }
    .rj-popup-box[data-width="lg"] { max-width: 800px; width: 100%; }
    .rj-popup-box[data-width="xl"] { max-width: 1100px; width: 100%; }
    .rj-popup-box[data-width="full"] { max-width: 96vw; width: 100%; }

    .rj-popup-overlay[data-position="center"] { align-items: center; justify-content: center; }
    .rj-popup-overlay[data-position="top"] { align-items: flex-start; justify-content: center; }
    .rj-popup-overlay[data-position="bottom"] { align-items: flex-end; justify-content: center; }
    .rj-popup-overlay[data-position="top-left"] { align-items: flex-start; justify-content: flex-start; }
    .rj-popup-overlay[data-position="top-right"] { align-items: flex-start; justify-content: flex-end; }
    .rj-popup-overlay[data-position="bottom-left"] { align-items: flex-end; justify-content: flex-start; }
    .rj-popup-overlay[data-position="bottom-right"] { align-items: flex-end; justify-content: flex-end; }

    .rj-popup-close {
        position: absolute; top: 10px; right: 10px; z-index: 10;
        width: 32px; height: 32px; border: 0; border-radius: 50%;
        background: rgba(0,0,0,.08); color: #374151;
        font-size: 16px; cursor: pointer; display: flex;
        align-items: center; justify-content: center;
        transition: background .15s;
    }
    .rj-popup-close:hover { background: rgba(0,0,0,.18); }

    @keyframes rjPopupFadeIn { from { opacity: 0 } to { opacity: 1 } }
    @keyframes rjPopupFadeInUp { from { opacity: 0; transform: translateY(30px) } to { opacity: 1; transform: none } }
    @keyframes rjPopupFadeInDown { from { opacity: 0; transform: translateY(-30px) } to { opacity: 1; transform: none } }
    @keyframes rjPopupZoomIn { from { opacity: 0; transform: scale(.9) } to { opacity: 1; transform: none } }
    @keyframes rjPopupSlideInLeft { from { opacity: 0; transform: translateX(-50px) } to { opacity: 1; transform: none } }
    @keyframes rjPopupSlideInRight { from { opacity: 0; transform: translateX(50px) } to { opacity: 1; transform: none } }

    .rj-popup-box[data-animation="fadeIn"] { animation: rjPopupFadeIn var(--rj-popup-dur, 400ms) ease both; }
    .rj-popup-box[data-animation="fadeInUp"] { animation: rjPopupFadeInUp var(--rj-popup-dur, 400ms) ease both; }
    .rj-popup-box[data-animation="fadeInDown"] { animation: rjPopupFadeInDown var(--rj-popup-dur, 400ms) ease both; }
    .rj-popup-box[data-animation="zoomIn"] { animation: rjPopupZoomIn var(--rj-popup-dur, 400ms) ease both; }
    .rj-popup-box[data-animation="slideInLeft"] { animation: rjPopupSlideInLeft var(--rj-popup-dur, 400ms) ease both; }
    .rj-popup-box[data-animation="slideInRight"] { animation: rjPopupSlideInRight var(--rj-popup-dur, 400ms) ease both; }
</style>

<script>
(function() {
    const STORAGE_KEY = 'rj_popup_seen';

    const getSeen = () => {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}'); }
        catch (e) { return {}; }
    };

    const setSeen = (id, mode) => {
        try {
            const seen = getSeen();
            seen[id] = { mode, t: Date.now() };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(seen));
        } catch (e) {}
    };

    const sessionSeen = (() => {
        try { return JSON.parse(sessionStorage.getItem(STORAGE_KEY) || '{}'); }
        catch (e) { return {}; }
    })();

    const setSessionSeen = (id) => {
        try {
            sessionSeen[id] = true;
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(sessionSeen));
        } catch (e) {}
    };

    const shouldShow = (node) => {
        const id = node.dataset.popupId;
        const freq = node.dataset.frequency;
        const seen = getSeen();

        if (freq === 'always') return true;
        if (freq === 'once_per_session') return !sessionSeen[id];
        if (freq === 'once_per_week') {
            const last = seen[id]?.t || 0;
            return (Date.now() - last) > (7 * 24 * 60 * 60 * 1000);
        }
        if (freq === 'once_per_day') {
            const last = seen[id]?.t || 0;
            return (Date.now() - last) > (24 * 60 * 60 * 1000);
        }
        if (freq === 'once') return !seen[id]?.t;
        return true;
    };

    const buildPopup = (node) => {
        const id = node.dataset.popupId;
        const overlay = document.createElement('div');
        overlay.className = 'rj-popup-overlay';
        overlay.dataset.position = node.dataset.position || 'center';
        overlay.dataset.popupId = id;

        const bg = document.createElement('div');
        bg.className = 'rj-popup-bg';

        const overlayColor = node.dataset.overlayColor || '#000000';
        const overlayOpacity = parseInt(node.dataset.overlayOpacity || '60') / 100;
        const hasOverlay = node.dataset.overlay === '1';

        if (hasOverlay) {
            const rgb = hexToRgb(overlayColor);
            bg.style.background = `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${overlayOpacity})`;
        }

        const box = document.createElement('div');
        box.className = 'rj-popup-box';
        box.dataset.width = node.dataset.width || 'md';
        box.dataset.animation = node.dataset.animation || 'fadeInUp';

        const durations = { sm: '200ms', md: '400ms', lg: '700ms', xl: '1000ms' };
        box.style.setProperty('--rj-popup-dur', durations[node.dataset.animationDuration] || '400ms');

        const template = node.querySelector('.rj-popup-content');
        if (template) {
            box.innerHTML = template.innerHTML;
        }

        if (node.dataset.closeButton === '1') {
            const close = document.createElement('button');
            close.className = 'rj-popup-close';
            close.type = 'button';
            close.setAttribute('aria-label', 'Close');
            close.innerHTML = '✕';
            close.addEventListener('click', () => hide(overlay, id, node.dataset.frequency));
            box.appendChild(close);
        }

        overlay.appendChild(bg);
        overlay.appendChild(box);

        if (node.dataset.closeOverlay === '1') {
            bg.addEventListener('click', () => hide(overlay, id, node.dataset.frequency));
        }

        document.body.appendChild(overlay);

        return overlay;
    };

    const hexToRgb = (hex) => {
        hex = (hex || '#000000').replace('#', '');
        if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
        const n = parseInt(hex, 16);
        return { r: (n >> 16) & 255, g: (n >> 8) & 255, b: n & 255 };
    };

    const show = (overlay, id, freq) => {
        overlay.classList.add('rj-active');
        document.body.style.overflow = 'hidden';
        if (freq === 'once_per_session') setSessionSeen(id);
        else setSeen(id, freq);

        const autoClose = parseInt(overlay.querySelector('.rj-popup-box')?.parentElement?.dataset?.autoClose || '0');
    };

    const hide = (overlay, id, freq) => {
        overlay.classList.remove('rj-active');
        document.body.style.overflow = '';
        setTimeout(() => overlay.remove(), 300);
    };

    const init = () => {
        const nodes = document.querySelectorAll('.rj-popup-data');

        nodes.forEach(node => {
            if (!shouldShow(node)) return;

            const trigger = node.dataset.trigger || 'page_load';
            const value = node.dataset.triggerValue || '';
            const delay = parseInt(node.dataset.delay || '0') * 1000;
            const id = node.dataset.popupId;
            const freq = node.dataset.frequency;

            if (trigger === 'page_load') {
                setTimeout(() => {
                    const overlay = buildPopup(node);
                    show(overlay, id, freq);
                }, delay);
            } else if (trigger === 'scroll_percent') {
                const percent = parseInt(value || '50');
                const handler = () => {
                    const scrolled = (window.scrollY + window.innerHeight) / document.body.scrollHeight * 100;
                    if (scrolled >= percent) {
                        window.removeEventListener('scroll', handler);
                        setTimeout(() => {
                            const overlay = buildPopup(node);
                            show(overlay, id, freq);
                        }, delay);
                    }
                };
                window.addEventListener('scroll', handler, { passive: true });
            } else if (trigger === 'after_inactivity') {
                let timer;
                const reset = () => {
                    clearTimeout(timer);
                    timer = setTimeout(() => {
                        const overlay = buildPopup(node);
                        show(overlay, id, freq);
                    }, parseInt(value || '10') * 1000);
                };
                ['mousemove', 'keydown', 'scroll', 'click'].forEach(ev => window.addEventListener(ev, reset, { passive: true }));
                reset();
            } else if (trigger === 'click_element') {
                const selector = value || '';
                if (selector) {
                    document.addEventListener('click', (e) => {
                        if (e.target.closest(selector)) {
                            e.preventDefault();
                            const overlay = buildPopup(node);
                            show(overlay, id, freq);
                        }
                    });
                }
            } else if (trigger === 'exit_intent') {
                const handler = (e) => {
                    if (e.clientY <= 0) {
                        document.removeEventListener('mouseleave', handler);
                        const overlay = buildPopup(node);
                        show(overlay, id, freq);
                    }
                };
                document.addEventListener('mouseleave', handler);
            }
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
@endif