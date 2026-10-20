@extends('admin.layouts.app')

@section('title', __('Site Health'))

@section('content')
@php
    $i18n = [
        'excellent' => __('Excellent'),
        'good' => __('Good'),
        'fair' => __('Fair'),
        'poor' => __('Poor'),
        'warning' => __('Warning'),
        'critical' => __('Critical'),
        'passing' => __('Passing'),
        'issuesCritical' => __(':n critical issue(s) need fixing first.'),
        'issuesWarning' => __(':n warning(s) are worth a look.'),
        'allGood' => __('Everything looks healthy.'),
        'refreshFailed' => __('Refresh failed.'),
        'refreshed' => __('Checks updated.'),
        'networkError' => __('Network error.'),
        'aiFailed' => __('AI analysis failed.'),
        'copyFailed' => __('Could not copy.'),
        'aiOff' => __('AI is not configured'),
        'analyze' => __('AI Analysis'),
        'analyzing' => __('Analyzing...'),
        'expandAll' => __('Expand all'),
        'collapseAll' => __('Collapse all'),
        'copy' => __('Copy'),
        'copied' => __('Copied'),
        'severity' => [
            'critical' => __('Critical'),
            'high' => __('High'),
            'medium' => __('Medium'),
            'low' => __('Low'),
        ],
    ];

    $urls = [
        'analyze' => route('admin.tools.health.analyze'),
        'refresh' => route('admin.tools.health.refresh'),
    ];
@endphp

<div class="sh" x-data="healthTool(@js($boot), @js($i18n), @js($urls))" @keydown.escape.window="drawer = false">

    {{-- HEADER --}}
    <header class="sh-head">
        <div>
            <h2 class="sh-title">{{ __('Site Health') }}</h2>
            <p class="sh-sub">
                {{ __('Automated checks and AI-powered analysis of your site.') }}
                <span class="sh-muted">· {{ __('Last checked') }}: <span x-text="checkedAt"></span></span>
            </p>
        </div>
        <div class="sh-actions">
            <button type="button" class="sh-btn" @click="refresh()" :disabled="refreshing">
                <svg :class="refreshing ? 'sh-spin' : ''" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                {{ __('Refresh') }}
            </button>
            <button type="button" class="sh-btn sh-btn-primary" @click="openAi()" :disabled="!aiEnabled || aiLoading" :title="!aiEnabled ? t.aiOff : ''">
                <svg x-show="!aiLoading" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <svg x-show="aiLoading" x-cloak class="sh-spin" width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                <span x-text="aiLoading ? t.analyzing : t.analyze"></span>
            </button>
        </div>
    </header>

    @if (!$aiEnabled)
        <div class="sh-notice">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p>
                {{ __('AI analysis is disabled. Configure OpenRouter API key in Settings → AI to unlock detailed analysis and actionable fixes.') }}
                <a href="{{ route('admin.settings.ai') }}">{{ __('Configure now') }}</a>
            </p>
        </div>
    @endif

    {{-- OVERVIEW --}}
    <section class="sh-card sh-overview">
        <div class="sh-ring">
            <svg viewBox="0 0 120 120" aria-hidden="true">
                <circle cx="60" cy="60" r="52" fill="none" stroke="var(--sh-line)" stroke-width="9"/>
                <circle cx="60" cy="60" r="52" fill="none" :stroke="scoreColor" stroke-width="9" stroke-linecap="round"
                        :stroke-dasharray="circumference" :stroke-dashoffset="dashOffset" class="sh-ring-fg" transform="rotate(-90 60 60)"/>
            </svg>
            <div class="sh-ring-text">
                <strong :style="'color:' + scoreColor" x-text="summary.score"></strong>
                <span>/ 100</span>
            </div>
        </div>

        <div class="sh-overview-main">
            <div>
                <h3 class="sh-verdict-title"><span x-text="scoreLabel"></span> <small><span x-text="summary.total"></span> {{ __('checks performed') }}</small></h3>
                <p class="sh-sub" x-text="verdict"></p>
            </div>

            <div class="sh-bar" role="img">
                <i class="sh-bg-good" :style="'width:' + pct('good') + '%'"></i>
                <i class="sh-bg-warning" :style="'width:' + pct('warning') + '%'"></i>
                <i class="sh-bg-critical" :style="'width:' + pct('critical') + '%'"></i>
            </div>

            <div class="sh-stats">
                <template x-for="s in stats" :key="s.key">
                    <button type="button" class="sh-stat" :class="status === s.key ? 'is-active sh-ring-' + s.key : ''" @click="toggleStatus(s.key)" :aria-pressed="status === s.key">
                        <span class="sh-stat-label"><i class="sh-dot" :class="'sh-bg-' + s.key"></i><span x-text="s.label"></span></span>
                        <strong x-text="summary[s.key]"></strong>
                    </button>
                </template>
            </div>
        </div>
    </section>

    {{-- BODY --}}
    <div class="sh-body">

        <aside class="sh-aside">
            <nav class="sh-card sh-nav">
                <template x-for="cat in categories" :key="cat.key">
                    <a :href="'#cat-' + cat.key" @click.prevent="jump(cat.key)" class="sh-nav-item">
                        <span class="sh-nav-name">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-html="icon(cat.icon)"></svg>
                            <span x-text="cat.label"></span>
                        </span>
                        <span class="sh-nav-badges">
                            <em class="sh-pill sh-pill-critical" x-show="cat.counts.critical > 0" x-text="cat.counts.critical"></em>
                            <em class="sh-pill sh-pill-warning" x-show="cat.counts.warning > 0" x-text="cat.counts.warning"></em>
                            <svg x-show="cat.counts.critical === 0 && cat.counts.warning === 0" width="16" height="16" fill="none" stroke="var(--sh-good)" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                    </a>
                </template>
            </nav>
        </aside>

        <div class="sh-main">
            <div class="sh-toolbar">
                <div class="sh-search">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
                    <input type="search" x-model.debounce.150ms="query" placeholder="{{ __('Search checks...') }}">
                </div>
                <button type="button" class="sh-btn sh-btn-ghost" x-show="status !== 'all' || query" x-cloak @click="status = 'all'; query = ''">{{ __('Clear filters') }}</button>
                <button type="button" class="sh-btn sh-btn-ghost" @click="toggleAll()" x-text="allCollapsed ? t.expandAll : t.collapseAll"></button>
            </div>

            <div class="sh-card sh-empty" x-show="visibleCategories.length === 0" x-cloak>
                <p>{{ __('No checks match these filters.') }}</p>
                <button type="button" class="sh-link" @click="status = 'all'; query = ''">{{ __('Clear filters') }}</button>
            </div>

            <template x-for="cat in visibleCategories" :key="cat.key">
                <section :id="'cat-' + cat.key" class="sh-card sh-cat">
                    <button type="button" class="sh-cat-head" @click="collapsed[cat.key] = !collapsed[cat.key]" :aria-expanded="!collapsed[cat.key]">
                        <span class="sh-cat-name">
                            <span class="sh-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-html="icon(cat.icon)"></svg></span>
                            <strong x-text="cat.label"></strong>
                            <small x-text="visibleItems(cat).length + ' / ' + cat.items.length"></small>
                        </span>
                        <svg class="sh-chevron" :class="collapsed[cat.key] ? '' : 'is-open'" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <ul class="sh-list" x-show="!collapsed[cat.key]">
                        <template x-for="item in visibleItems(cat)" :key="item.id">
                            <li class="sh-row">
                                <i class="sh-dot sh-row-dot" :class="'sh-bg-' + item.status"></i>
                                <div class="sh-row-main">
                                    <div class="sh-row-top">
                                        <span class="sh-row-label" x-text="item.label"></span>
                                        <span class="sh-row-msg" x-text="item.message"></span>
                                    </div>
                                    <p class="sh-note" :class="'sh-note-' + item.status" x-show="item.recommendation" x-text="item.recommendation"></p>
                                </div>
                                <span class="sh-badge" :class="'sh-badge-' + item.status" x-text="st[item.status].label"></span>
                            </li>
                        </template>
                    </ul>
                </section>
            </template>
        </div>
    </div>

    {{-- AI DRAWER --}}
    <div class="sh-overlay" x-show="drawer" x-cloak role="dialog" aria-modal="true">
        <div class="sh-backdrop" x-show="drawer" x-transition.opacity @click="drawer = false"></div>

        <div class="sh-drawer" x-show="drawer"
             x-transition:enter="sh-slide" x-transition:enter-start="sh-slide-out" x-transition:enter-end="sh-slide-in"
             x-transition:leave="sh-slide" x-transition:leave-start="sh-slide-in" x-transition:leave-end="sh-slide-out">

            <div class="sh-drawer-head">
                <div>
                    <h3>{{ __('AI Health Analysis') }}</h3>
                    <p class="sh-muted"><span x-text="aiData && aiData.model ? aiData.model : aiModel"></span><span x-show="aiCached" x-cloak> · {{ __('cached result') }}</span></p>
                </div>
                <div class="sh-actions">
                    <button type="button" class="sh-btn sh-btn-ghost" x-show="aiData && !aiLoading" x-cloak @click="runAi(true)">{{ __('Re-analyze') }}</button>
                    <button type="button" class="sh-btn sh-btn-ghost sh-btn-icon" @click="drawer = false" aria-label="{{ __('Close') }}">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div class="sh-drawer-body">

                <div x-show="aiLoading" x-cloak>
                    <p class="sh-sub">{{ __('Reviewing your health checks and ranking what to fix first...') }}</p>
                    <div class="sh-skel"></div><div class="sh-skel"></div><div class="sh-skel"></div>
                </div>

                <div class="sh-error" x-show="aiError && !aiLoading" x-cloak>
                    <p x-text="aiError"></p>
                    <button type="button" class="sh-btn" @click="runAi(true)">{{ __('Try again') }}</button>
                </div>

                <template x-if="aiData && !aiLoading && !aiError">
                    <div class="sh-ai">
                        <div class="sh-verdict" :class="overallClass(aiData.overall)">
                            <div class="sh-verdict-meta">
                                <span class="sh-tag" x-text="aiData.overall"></span>
                                <span>{{ __('Score') }}: <strong x-text="aiData.score"></strong></span>
                            </div>
                            <h4 x-text="aiData.headline"></h4>
                            <p x-text="aiData.summary"></p>
                        </div>

                        <div x-show="aiData.priorities && aiData.priorities.length">
                            <h4 class="sh-h4">{{ __('Action Plan') }}</h4>
                            <ol class="sh-plan">
                                <template x-for="(p, idx) in aiData.priorities" :key="idx">
                                    <li class="sh-step" :class="'sh-step-' + p.severity">
                                        <div class="sh-step-top">
                                            <span class="sh-badge" :class="'sh-sev-' + p.severity" x-text="t.severity[p.severity]"></span>
                                            <span class="sh-muted" x-show="p.category" x-text="p.category"></span>
                                            <span class="sh-muted sh-step-n" x-text="'#' + (idx + 1)"></span>
                                        </div>
                                        <h5 x-text="p.title"></h5>
                                        <p x-show="p.why" x-text="p.why"></p>
                                        <div class="sh-code" x-show="p.action">
                                            <div class="sh-code-head">
                                                <span>{{ __('Action to take') }}</span>
                                                <button type="button" @click="copy(p.action, idx)" x-text="copiedIdx === idx ? t.copied : t.copy"></button>
                                            </div>
                                            <pre x-text="p.action"></pre>
                                        </div>
                                    </li>
                                </template>
                            </ol>
                        </div>

                        <div class="sh-wins" x-show="aiData.quick_wins && aiData.quick_wins.length">
                            <h4 class="sh-h4">{{ __('Quick Wins') }}</h4>
                            <ul>
                                <template x-for="(w, i) in aiData.quick_wins" :key="i">
                                    <li><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg><span x-text="w"></span></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </template>
            </div>

            <div class="sh-drawer-foot">
                <span class="sh-muted">{{ __('AI suggestions can be wrong. Review commands before running them.') }}</span>
                <button type="button" class="sh-btn sh-btn-dark" @click="drawer = false">{{ __('Close') }}</button>
            </div>
        </div>
    </div>

    <div class="sh-toast" :class="toast.type === 'error' ? 'is-error' : ''" x-show="toast.show" x-cloak x-transition x-text="toast.message"></div>
</div>

<script>
function healthTool(boot, t, urls) {
    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

    const ICONS = {
        server: '<path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>',
        folder: '<path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>',
        database: '<path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>',
        shield: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
        bolt: '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
        document: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        list: '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>',
        plug: '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M5 11h14M7 11v4a5 5 0 0010 0v-4M12 20v1"/>',
        circle: '<circle cx="12" cy="12" r="9"/>',
    };

    return {
        summary: boot.summary,
        categories: boot.categories,
        checkedAt: boot.checkedAt,
        aiEnabled: boot.aiEnabled,
        aiModel: boot.aiModel,
        t: t,

        circumference: 2 * Math.PI * 52,
        animatedScore: 0,

        status: 'all',
        query: '',
        collapsed: {},
        refreshing: false,

        drawer: false,
        aiLoading: false,
        aiError: '',
        aiData: null,
        aiCached: false,
        copiedIdx: null,

        toast: { show: false, message: '', type: 'success' },
        _toastTimer: null,

        st: {
            good: { label: t.passing },
            warning: { label: t.warning },
            critical: { label: t.critical },
        },

        init() {
            this.$nextTick(() => setTimeout(() => { this.animatedScore = this.summary.score; }, 80));
        },

        get stats() {
            return [
                { key: 'good', label: t.passing },
                { key: 'warning', label: t.warning },
                { key: 'critical', label: t.critical },
            ];
        },

        get dashOffset() {
            return this.circumference * ((100 - this.animatedScore) / 100);
        },

        get scoreColor() {
            const s = this.summary.score;
            if (s >= 90) return '#16a34a';
            if (s >= 70) return '#d97706';
            if (s >= 50) return '#ea580c';
            return '#dc2626';
        },

        get scoreLabel() {
            const s = this.summary.score;
            if (s >= 90) return t.excellent;
            if (s >= 70) return t.good;
            if (s >= 50) return t.fair;
            return t.poor;
        },

        get verdict() {
            if (this.summary.critical > 0) return t.issuesCritical.replace(':n', this.summary.critical);
            if (this.summary.warning > 0) return t.issuesWarning.replace(':n', this.summary.warning);
            return t.allGood;
        },

        get visibleCategories() {
            return this.categories.filter(c => this.visibleItems(c).length > 0);
        },

        get allCollapsed() {
            return this.categories.length > 0 && this.categories.every(c => this.collapsed[c.key]);
        },

        pct(key) {
            return this.summary.total ? (this.summary[key] / this.summary.total) * 100 : 0;
        },

        icon(name) {
            return ICONS[name] || ICONS.circle;
        },

        visibleItems(cat) {
            const q = this.query.trim().toLowerCase();
            return cat.items.filter(i => {
                if (this.status !== 'all' && i.status !== this.status) return false;
                if (!q) return true;
                return (i.label + ' ' + i.message + ' ' + (i.recommendation || '')).toLowerCase().includes(q);
            });
        },

        toggleStatus(key) {
            this.status = this.status === key ? 'all' : key;
        },

        toggleAll() {
            const collapse = !this.allCollapsed;
            const next = {};
            this.categories.forEach(c => { next[c.key] = collapse; });
            this.collapsed = next;
        },

        jump(key) {
            this.collapsed[key] = false;
            this.$nextTick(() => {
                document.getElementById('cat-' + key)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        },

        overallClass(overall) {
            if (overall === 'excellent' || overall === 'good') return 'is-good';
            if (overall === 'fair') return 'is-warning';
            return 'is-critical';
        },

        showToast(message, type) {
            this.toast = { show: true, message, type: type || 'success' };
            clearTimeout(this._toastTimer);
            this._toastTimer = setTimeout(() => { this.toast.show = false; }, 3500);
        },

        async request(url, body) {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(body || {}),
            });
            const data = await res.json().catch(() => ({}));
            return { ok: res.ok, data };
        },

        async refresh() {
            if (this.refreshing) return;
            this.refreshing = true;

            try {
                const { ok, data } = await this.request(urls.refresh);

                if (!ok || !data.success) {
                    this.showToast(data.message || t.refreshFailed, 'error');
                    return;
                }

                this.summary = data.summary;
                this.categories = data.categories;
                this.checkedAt = data.checked_at;
                this.animatedScore = data.summary.score;
                this.aiData = null;
                this.showToast(t.refreshed);
            } catch (e) {
                this.showToast(t.refreshFailed, 'error');
            } finally {
                this.refreshing = false;
            }
        },

        openAi() {
            if (!this.aiEnabled || this.aiLoading) return;
            this.drawer = true;
            if (!this.aiData) this.runAi(false);
        },

        async runAi(force) {
            if (this.aiLoading) return;

            this.drawer = true;
            this.aiLoading = true;
            this.aiError = '';
            if (force) this.aiData = null;

            try {
                const { ok, data } = await this.request(urls.analyze, { force: !!force });

                if (ok && data.success) {
                    this.aiData = data.data || null;
                    this.aiCached = !!data.cached;
                } else {
                    this.aiError = data.message || t.aiFailed;
                }
            } catch (e) {
                this.aiError = t.networkError;
            } finally {
                this.aiLoading = false;
            }
        },

        async copy(text, idx) {
            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(text);
                } else {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                }
                this.copiedIdx = idx;
                setTimeout(() => { if (this.copiedIdx === idx) this.copiedIdx = null; }, 1800);
            } catch (e) {
                this.showToast(t.copyFailed, 'error');
            }
        },
    };
}
</script>

<style>
[x-cloak] { display: none !important; }

.sh {
    --sh-bg: #ffffff;
    --sh-soft: #f8fafc;
    --sh-line: #e5e7eb;
    --sh-text: #111827;
    --sh-muted: #6b7280;
    --sh-accent: #4f46e5;
    --sh-good: #16a34a;
    --sh-warning: #d97706;
    --sh-critical: #dc2626;
    --sh-radius: 12px;
    max-width: 1200px;
    margin: 0 auto;
    color: var(--sh-text);
    font-size: 14px;
    line-height: 1.5;
}
.sh *, .sh *::before, .sh *::after { box-sizing: border-box; }
.sh h2, .sh h3, .sh h4, .sh h5, .sh p, .sh ul, .sh ol { margin: 0; padding: 0; }
.sh ul, .sh ol { list-style: none; }
.sh button { font: inherit; cursor: pointer; }
.sh svg { flex-shrink: 0; }

.sh-muted { color: var(--sh-muted); font-size: 12.5px; }
.sh-sub { color: var(--sh-muted); font-size: 14px; margin-top: 2px; }
.sh-title { font-size: 22px; font-weight: 700; letter-spacing: -.01em; }

/* header */
.sh-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
.sh-actions { display: flex; align-items: center; gap: 8px; }

.sh-btn {
    display: inline-flex; align-items: center; gap: 8px; height: 38px; padding: 0 14px;
    border-radius: 8px; border: 1px solid var(--sh-line); background: var(--sh-bg); color: var(--sh-text);
    font-size: 13.5px; font-weight: 500; transition: background .15s, border-color .15s;
}
.sh-btn:hover:not(:disabled) { background: var(--sh-soft); border-color: #d1d5db; }
.sh-btn:disabled { opacity: .55; cursor: not-allowed; }
.sh-btn:focus-visible, .sh-stat:focus-visible, .sh-cat-head:focus-visible { outline: 2px solid var(--sh-accent); outline-offset: 2px; }
.sh-btn-primary { background: var(--sh-accent); border-color: var(--sh-accent); color: #fff; }
.sh-btn-primary:hover:not(:disabled) { background: #4338ca; border-color: #4338ca; }
.sh-btn-dark { background: #111827; border-color: #111827; color: #fff; }
.sh-btn-dark:hover:not(:disabled) { background: #1f2937; border-color: #1f2937; }
.sh-btn-ghost { border-color: transparent; background: transparent; color: var(--sh-muted); }
.sh-btn-icon { width: 38px; padding: 0; justify-content: center; }

.sh-spin { animation: sh-spin .8s linear infinite; }
@keyframes sh-spin { to { transform: rotate(360deg); } }

.sh-notice { display: flex; gap: 10px; align-items: flex-start; padding: 12px 14px; margin-bottom: 20px; border: 1px solid #c7d2fe; background: #eef2ff; color: #3730a3; border-radius: 10px; }
.sh-notice svg { margin-top: 2px; }
.sh-notice a { font-weight: 600; text-decoration: underline; margin-left: 4px; color: inherit; }

.sh-card { background: var(--sh-bg); border: 1px solid var(--sh-line); border-radius: var(--sh-radius); }

/* overview */
.sh-overview { display: grid; grid-template-columns: 170px 1fr; gap: 32px; align-items: center; padding: 24px 28px; margin-bottom: 24px; }
.sh-ring { position: relative; width: 160px; height: 160px; }
.sh-ring svg { width: 100%; height: 100%; }
.sh-ring-fg { transition: stroke-dashoffset 1.2s cubic-bezier(.4,0,.2,1), stroke .3s; }
.sh-ring-text { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
.sh-ring-text strong { font-size: 40px; line-height: 1; font-weight: 700; font-variant-numeric: tabular-nums; }
.sh-ring-text span { font-size: 12px; color: var(--sh-muted); margin-top: 4px; }

.sh-overview-main { display: flex; flex-direction: column; gap: 18px; min-width: 0; }
.sh-verdict-title { font-size: 18px; font-weight: 600; }
.sh-verdict-title small { font-size: 13px; font-weight: 400; color: var(--sh-muted); margin-left: 8px; }

.sh-bar { display: flex; height: 8px; border-radius: 999px; overflow: hidden; background: var(--sh-line); }
.sh-bar i { display: block; height: 100%; transition: width .7s; }
.sh-bg-good { background: var(--sh-good); }
.sh-bg-warning { background: #f59e0b; }
.sh-bg-critical { background: var(--sh-critical); }

.sh-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
.sh-stat { text-align: left; padding: 12px 14px; border: 1px solid var(--sh-line); border-radius: 10px; background: var(--sh-bg); transition: border-color .15s, background .15s; }
.sh-stat:hover { border-color: #cbd5e1; }
.sh-stat-label { display: flex; align-items: center; gap: 8px; color: var(--sh-muted); font-size: 13px; }
.sh-stat strong { display: block; font-size: 26px; font-weight: 700; line-height: 1.2; margin-top: 4px; font-variant-numeric: tabular-nums; }
.sh-stat.is-active.sh-ring-good { border-color: var(--sh-good); background: #f0fdf4; }
.sh-stat.is-active.sh-ring-warning { border-color: #f59e0b; background: #fffbeb; }
.sh-stat.is-active.sh-ring-critical { border-color: var(--sh-critical); background: #fef2f2; }
.sh-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

/* body */
.sh-body { display: grid; grid-template-columns: 240px 1fr; gap: 24px; align-items: start; }
.sh-aside { position: sticky; top: 16px; }
.sh-nav { padding: 6px; }
.sh-nav-item { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 9px 10px; border-radius: 8px; color: var(--sh-text); text-decoration: none; font-size: 13.5px; }
.sh-nav-item:hover { background: var(--sh-soft); }
.sh-nav-name { display: flex; align-items: center; gap: 10px; min-width: 0; color: #374151; }
.sh-nav-name span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.sh-nav-name svg { color: #9ca3af; }
.sh-nav-badges { display: flex; align-items: center; gap: 4px; }
.sh-pill { font-style: normal; font-size: 11.5px; font-weight: 600; min-width: 20px; text-align: center; padding: 1px 6px; border-radius: 6px; }
.sh-pill-critical { background: #fee2e2; color: #b91c1c; }
.sh-pill-warning { background: #fef3c7; color: #b45309; }

.sh-main { display: flex; flex-direction: column; gap: 16px; min-width: 0; }
.sh-toolbar { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.sh-search { position: relative; flex: 1; min-width: 200px; }
.sh-search svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
.sh-search input { width: 100%; height: 38px; padding: 0 12px 0 36px; border: 1px solid var(--sh-line); border-radius: 8px; background: var(--sh-bg); font: inherit; color: var(--sh-text); }
.sh-search input:focus { outline: 2px solid var(--sh-accent); outline-offset: -1px; border-color: transparent; }

.sh-empty { padding: 48px 16px; text-align: center; border-style: dashed; }
.sh-link { background: none; border: 0; color: var(--sh-accent); margin-top: 6px; }
.sh-link:hover { text-decoration: underline; }

.sh-cat { overflow: hidden; scroll-margin-top: 16px; }
.sh-cat-head { width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 18px; background: var(--sh-bg); border: 0; text-align: left; color: var(--sh-text); }
.sh-cat-head:hover { background: var(--sh-soft); }
.sh-cat-name { display: flex; align-items: center; gap: 12px; }
.sh-cat-name strong { font-size: 14.5px; font-weight: 600; }
.sh-cat-name small { color: var(--sh-muted); font-size: 12px; }
.sh-icon { width: 32px; height: 32px; border-radius: 8px; background: #f3f4f6; color: #4b5563; display: inline-flex; align-items: center; justify-content: center; }
.sh-chevron { color: #9ca3af; transition: transform .2s; }
.sh-chevron.is-open { transform: rotate(180deg); }

.sh-list { border-top: 1px solid var(--sh-line); }
.sh-row { display: flex; align-items: flex-start; gap: 12px; padding: 13px 18px; }
.sh-row + .sh-row { border-top: 1px solid #f1f5f9; }
.sh-row-dot { margin-top: 7px; }
.sh-row-main { flex: 1; min-width: 0; }
.sh-row-top { display: flex; flex-wrap: wrap; gap: 2px 14px; align-items: baseline; }
.sh-row-label { font-weight: 600; }
.sh-row-msg { color: var(--sh-muted); word-break: break-word; }
.sh-note { margin-top: 6px; padding-left: 10px; border-left: 2px solid; font-size: 12.5px; }
.sh-note-good { border-color: #86efac; color: #166534; }
.sh-note-warning { border-color: #fcd34d; color: #92400e; }
.sh-note-critical { border-color: #fca5a5; color: #991b1b; }

.sh-badge { display: inline-block; font-size: 12px; font-weight: 600; padding: 2px 8px; border-radius: 6px; white-space: nowrap; flex-shrink: 0; }
.sh-badge-good { background: #dcfce7; color: #166534; }
.sh-badge-warning { background: #fef3c7; color: #92400e; }
.sh-badge-critical { background: #fee2e2; color: #991b1b; }

/* drawer */
.sh-overlay { position: fixed; inset: 0; z-index: 9999; }
.sh-backdrop { position: absolute; inset: 0; background: rgba(17,24,39,.45); }
.sh-drawer { position: absolute; top: 0; right: 0; bottom: 0; width: min(560px, 100%); background: var(--sh-bg); box-shadow: -12px 0 40px rgba(0,0,0,.18); display: flex; flex-direction: column; }
.sh-slide { transition: transform .22s ease; }
.sh-slide-out { transform: translateX(100%); }
.sh-slide-in { transform: translateX(0); }
.sh-drawer-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 16px 20px; border-bottom: 1px solid var(--sh-line); }
.sh-drawer-head h3 { font-size: 16px; font-weight: 600; }
.sh-drawer-body { flex: 1; overflow-y: auto; padding: 20px; }
.sh-drawer-foot { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 12px 20px; border-top: 1px solid var(--sh-line); background: var(--sh-soft); }

.sh-skel { height: 96px; border-radius: 10px; background: #f1f5f9; margin-top: 12px; animation: sh-pulse 1.4s ease-in-out infinite; }
@keyframes sh-pulse { 50% { opacity: .5; } }

.sh-error { padding: 16px; border: 1px solid #fecaca; background: #fef2f2; border-radius: 10px; color: #991b1b; }
.sh-error .sh-btn { margin-top: 12px; }

.sh-ai { display: flex; flex-direction: column; gap: 24px; }
.sh-verdict { padding: 18px; border-radius: 10px; border: 1px solid; }
.sh-verdict.is-good { background: #f0fdf4; border-color: #bbf7d0; }
.sh-verdict.is-warning { background: #fffbeb; border-color: #fde68a; }
.sh-verdict.is-critical { background: #fef2f2; border-color: #fecaca; }
.sh-verdict-meta { display: flex; gap: 12px; align-items: center; font-size: 12.5px; color: #4b5563; margin-bottom: 8px; }
.sh-tag { background: rgba(255,255,255,.75); border: 1px solid rgba(0,0,0,.08); padding: 1px 8px; border-radius: 6px; font-weight: 600; text-transform: capitalize; }
.sh-verdict h4 { font-size: 16px; font-weight: 600; line-height: 1.35; }
.sh-verdict p { margin-top: 8px; color: #374151; }

.sh-h4 { font-size: 14px; font-weight: 600; margin-bottom: 12px; }
.sh-plan { display: flex; flex-direction: column; gap: 12px; }
.sh-step { padding: 16px; border: 1px solid var(--sh-line); border-left-width: 3px; border-radius: 10px; }
.sh-step-critical { border-left-color: var(--sh-critical); }
.sh-step-high { border-left-color: #ea580c; }
.sh-step-medium { border-left-color: #f59e0b; }
.sh-step-low { border-left-color: #9ca3af; }
.sh-step-top { display: flex; align-items: center; gap: 10px; }
.sh-step-n { margin-left: auto; }
.sh-step h5 { font-size: 14px; font-weight: 600; margin-top: 10px; }
.sh-step > p { margin-top: 4px; color: #4b5563; font-size: 13.5px; }
.sh-sev-critical { background: #fee2e2; color: #b91c1c; }
.sh-sev-high { background: #ffedd5; color: #c2410c; }
.sh-sev-medium { background: #fef3c7; color: #b45309; }
.sh-sev-low { background: #f3f4f6; color: #4b5563; }

.sh-code { margin-top: 12px; background: #111827; border-radius: 8px; overflow: hidden; }
.sh-code-head { display: flex; justify-content: space-between; align-items: center; padding: 6px 12px; border-bottom: 1px solid rgba(255,255,255,.1); font-size: 12px; color: #9ca3af; }
.sh-code-head button { background: none; border: 0; color: #d1d5db; font-size: 12px; }
.sh-code-head button:hover { color: #fff; }
.sh-code pre { margin: 0; padding: 12px; color: #f3f4f6; font: 12.5px/1.6 ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; white-space: pre-wrap; word-break: break-word; }

.sh-wins { padding: 16px; border: 1px solid #bbf7d0; background: #f0fdf4; border-radius: 10px; }
.sh-wins ul { display: flex; flex-direction: column; gap: 8px; }
.sh-wins li { display: flex; gap: 8px; align-items: flex-start; color: #166534; }
.sh-wins li svg { margin-top: 3px; color: var(--sh-good); }

/* toast */
.sh-toast { position: fixed; right: 24px; bottom: 24px; z-index: 10000; padding: 10px 16px; border-radius: 8px; background: #059669; color: #fff; font-weight: 500; box-shadow: 0 8px 24px rgba(0,0,0,.18); }
.sh-toast.is-error { background: #dc2626; }

@media (max-width: 900px) {
    .sh-overview { grid-template-columns: 1fr; justify-items: center; text-align: center; }
    .sh-overview-main { width: 100%; text-align: left; }
    .sh-body { grid-template-columns: 1fr; }
    .sh-aside { position: static; }
    .sh-nav { display: flex; overflow-x: auto; gap: 4px; }
    .sh-nav-item { flex-shrink: 0; }
}
@media (max-width: 520px) {
    .sh-stats { grid-template-columns: 1fr; }
    .sh-overview { padding: 18px; }
}
@media (prefers-reduced-motion: reduce) {
    .sh-ring-fg, .sh-bar i, .sh-slide, .sh-chevron { transition: none; }
    .sh-spin, .sh-skel { animation: none; }
}
</style>
@endsection