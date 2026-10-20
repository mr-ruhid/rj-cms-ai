@extends('admin.settings.layout')

@section('title', __('API & Integrations'))

@section('settings')
@php
    $endpointList = collect($availableEndpoints ?? [])->map(fn ($e) => [
        'method' => strtoupper($e['method'] ?? 'GET'),
        'uri'    => $e['uri'] ?? '',
        'desc'   => $e['desc'] ?? '',
    ])->values();
@endphp

<style>
    [x-cloak]{display:none!important}
    .rjapi{--p:var(--rj-purple,#7c3aed);--k:var(--rj-pink,#ec4899);--p-soft:color-mix(in srgb,var(--p) 10%,white);--p-faint:color-mix(in srgb,var(--p) 5%,white);--p-line:color-mix(in srgb,var(--p) 30%,white);--p-deep:color-mix(in srgb,var(--p) 65%,black);--p-ring:color-mix(in srgb,var(--p) 18%,transparent);--p-glow:color-mix(in srgb,var(--p) 30%,transparent);--p-dark:color-mix(in srgb,var(--p) 14%,#0b1020);--p-bar:color-mix(in srgb,var(--p) 22%,#111827);--p-edge:color-mix(in srgb,var(--p) 28%,#111827);--ink:#111827;--mut:#6b7280;--line:#e5e7eb;--soft:#f9fafb;display:flex;flex-direction:column;gap:24px;color:var(--ink)}
    .rjapi *{box-sizing:border-box}
    .rjapi svg{width:18px;height:18px;flex-shrink:0}
    .rjapi button{cursor:pointer;font-family:inherit}

    /* hero */
    .rjapi-hero{position:relative;overflow:hidden;border-radius:20px;padding:28px;color:#fff;background:linear-gradient(135deg,var(--p-deep) 0%,var(--p) 55%,var(--k) 125%)}
    .rjapi-hero:after{content:"";position:absolute;right:-60px;top:-60px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,.12)}
    .rjapi-hero h2{margin:0;font-size:22px;font-weight:800;letter-spacing:-.02em}
    .rjapi-hero p{margin:6px 0 0;font-size:14px;opacity:.85;max-width:560px}
    .rjapi-stats{display:flex;gap:12px;margin-top:18px;flex-wrap:wrap;position:relative;z-index:1}
    .rjapi-stat{background:rgba(255,255,255,.16);backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,.25);border-radius:12px;padding:10px 16px;min-width:110px}
    .rjapi-stat b{display:block;font-size:20px;font-weight:800;line-height:1.1}
    .rjapi-stat span{font-size:11px;opacity:.85;text-transform:uppercase;letter-spacing:.06em}

    /* alerts */
    .rjapi-alert{display:flex;gap:12px;align-items:flex-start;padding:12px 16px;border-radius:12px;font-size:14px;border:1px solid}
    .rjapi-alert.ok{background:#ecfdf5;border-color:#a7f3d0;color:#065f46}
    .rjapi-alert.err{background:#fef2f2;border-color:#fecaca;color:#991b1b}
    .rjapi-alert.warn{background:#fffbeb;border-color:#fde68a;color:#92400e;font-size:13px}
    .rjapi-alert ul{margin:0;padding-left:18px}

    /* cards */
    .rjapi-card{background:#fff;border:1px solid var(--line);border-radius:20px;box-shadow:0 1px 2px rgba(0,0,0,.04);overflow:hidden}
    .rjapi-head{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px 24px;border-bottom:1px solid #f3f4f6}
    .rjapi-head-l{display:flex;align-items:center;gap:14px;min-width:0}
    .rjapi-ico{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .rjapi-ico svg{width:20px;height:20px}
    .rjapi-ico.i{background:var(--p-soft);color:var(--p)}
    .rjapi-ico.e{background:color-mix(in srgb,var(--k) 10%,white);color:var(--k)}
    .rjapi-head h3{margin:0;font-size:16px;font-weight:700}
    .rjapi-head p{margin:2px 0 0;font-size:12px;color:var(--mut)}
    .rjapi-count{background:#f3f4f6;color:#4b5563;font-size:12px;font-weight:700;padding:4px 12px;border-radius:999px}
    .rjapi-body{padding:24px;display:flex;flex-direction:column;gap:20px}

    /* generate */
    .rjapi-gen{background:var(--soft);border:1px solid var(--line);border-radius:14px;padding:16px}
    .rjapi-gen label{display:block;font-size:13px;font-weight:700;margin-bottom:8px}
    .rjapi-row{display:flex;gap:12px;flex-wrap:wrap}
    .rjapi-input{position:relative;flex:1;min-width:220px}
    .rjapi-input svg{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#9ca3af;width:16px;height:16px}
    .rjapi-input input{width:100%;padding:11px 14px 11px 38px;border:1px solid #d1d5db;border-radius:12px;font-size:14px;background:#fff;outline:none;transition:.15s}
    .rjapi-input input:focus{border-color:var(--p);box-shadow:0 0 0 3px var(--p-ring)}
    .rjapi-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:11px 22px;border:0;border-radius:12px;font-size:14px;font-weight:600;color:#fff;background:linear-gradient(90deg,var(--p),var(--k));box-shadow:0 4px 12px var(--p-glow);transition:.15s}
    .rjapi-btn:hover{opacity:.92;transform:translateY(-1px)}
    .rjapi-btn:disabled{opacity:.5;cursor:not-allowed;transform:none}

    /* empty */
    .rjapi-empty{text-align:center;padding:44px 16px;border:2px dashed var(--line);border-radius:16px;background:linear-gradient(180deg,#fff,#fafafa)}
    .rjapi-empty .ic{width:60px;height:60px;border-radius:18px;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;background:var(--p-soft);color:color-mix(in srgb,var(--p) 65%,white)}
    .rjapi-empty .ic svg{width:28px;height:28px}
    .rjapi-empty b{display:block;font-size:14px}
    .rjapi-empty span{font-size:12px;color:var(--mut)}

    /* keys */
    .rjapi-key{border:1px solid var(--line);border-radius:16px;padding:18px;background:#fff;transition:.15s}
    .rjapi-key:hover{border-color:var(--p-line);box-shadow:0 6px 18px var(--p-ring)}
    .rjapi-key-top{display:flex;align-items:center;justify-content:space-between;gap:12px}
    .rjapi-av{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;background:linear-gradient(135deg,var(--p),var(--k));flex-shrink:0}
    .rjapi-key-id{display:flex;align-items:center;gap:12px;min-width:0}
    .rjapi-key-id h4{margin:0;font-size:14px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .rjapi-key-id p{margin:2px 0 0;font-size:12px;color:var(--mut)}
    .rjapi-revoke{display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:10px;border:1px solid #fee2e2;background:#fef2f2;color:#dc2626;font-size:12px;font-weight:600}
    .rjapi-revoke:hover{background:#fee2e2}
    .rjapi-fields{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px;margin-top:16px}
    .rjapi-field label{display:block;font-size:11px;font-weight:600;color:var(--mut);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px}
    .rjapi-val{display:flex;align-items:center;gap:4px;background:var(--soft);border:1px solid var(--line);border-radius:10px;padding:6px 6px 6px 12px}
    .rjapi-val code{flex:1;min-width:0;font-family:ui-monospace,Menlo,Consolas,monospace;font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .rjapi-ib{background:transparent;border:0;padding:6px;border-radius:8px;color:#9ca3af;display:flex}
    .rjapi-ib:hover{background:#fff;color:#374151}
    .rjapi-ib.done{color:#059669}

    /* code block */
    .rjapi-code{border-radius:16px;overflow:hidden;background:var(--p-dark);border:1px solid var(--p-edge)}
    .rjapi-code-bar{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 14px;background:var(--p-bar);border-bottom:1px solid var(--p-edge);flex-wrap:wrap}
    .rjapi-dots{display:flex;gap:6px}
    .rjapi-dots i{width:10px;height:10px;border-radius:50%;display:block}
    .rjapi-tabs{display:flex;gap:4px;background:var(--p-dark);padding:3px;border-radius:10px}
    .rjapi-tab{border:0;background:transparent;color:#9ca3af;font-size:12px;font-weight:600;padding:5px 12px;border-radius:8px}
    .rjapi-tab:hover{color:#fff}
    .rjapi-tab.on{background:var(--p);color:#fff}
    .rjapi-copy{display:inline-flex;align-items:center;gap:6px;border:0;background:transparent;color:#9ca3af;font-size:12px;font-weight:600;padding:6px 10px;border-radius:8px}
    .rjapi-copy:hover{background:rgba(255,255,255,.08);color:#fff}
    .rjapi-copy svg{width:14px;height:14px}
    .rjapi-pre{margin:0;padding:18px 20px;overflow-x:auto;font-family:ui-monospace,Menlo,Consolas,monospace;font-size:12.5px;line-height:1.75;color:color-mix(in srgb,var(--k) 35%,white);white-space:pre;min-height:96px}

    /* endpoints */
    .rjapi-tools{display:flex;gap:12px;flex-wrap:wrap;align-items:center}
    .rjapi-search{position:relative;flex:1;min-width:200px}
    .rjapi-search svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;width:16px;height:16px}
    .rjapi-search input{width:100%;padding:9px 12px 9px 36px;border:1px solid #d1d5db;border-radius:10px;font-size:13px;outline:none}
    .rjapi-search input:focus{border-color:var(--p);box-shadow:0 0 0 3px var(--p-ring)}
    .rjapi-chips{display:flex;gap:6px;flex-wrap:wrap}
    .rjapi-chip{border:1px solid var(--line);background:#fff;color:#4b5563;font-size:11px;font-weight:700;padding:6px 11px;border-radius:999px}
    .rjapi-chip.on{background:var(--p);border-color:var(--p);color:#fff}
    .rjapi-list{border:1px solid var(--line);border-radius:14px;overflow:hidden}
    .rjapi-li{display:grid;grid-template-columns:84px minmax(180px,1.3fr) 2fr;gap:14px;align-items:center;padding:13px 18px;border-top:1px solid #f3f4f6;transition:.12s}
    .rjapi-li:first-child{border-top:0}
    .rjapi-li:hover{background:var(--p-faint)}
    .rjapi-li.hd{background:var(--soft);font-size:12px;font-weight:700;color:#4b5563;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--line)}
    .rjapi-li.hd:hover{background:var(--soft)}
    .rjapi-uri{font-family:ui-monospace,Menlo,Consolas,monospace;font-size:12.5px;color:#1f2937;word-break:break-all}
    .rjapi-desc{font-size:13px;color:#4b5563}
    .rjapi-m{display:inline-block;text-align:center;min-width:64px;padding:3px 8px;border-radius:7px;font-size:11px;font-weight:800;letter-spacing:.03em;border:1px solid}
    .m-GET{background:#f0f9ff;color:#0369a1;border-color:#bae6fd}
    .m-POST{background:#ecfdf5;color:#047857;border-color:#a7f3d0}
    .m-PUT{background:#fffbeb;color:#b45309;border-color:#fde68a}
    .m-PATCH{background:#f5f3ff;color:#6d28d9;border-color:#ddd6fe}
    .m-DELETE{background:#fef2f2;color:#b91c1c;border-color:#fecaca}
    .rjapi-none{padding:32px;text-align:center;font-size:13px;color:var(--mut)}

    /* dialog + toast */
    .rjapi-ov{position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;padding:16px}
    .rjapi-bd{position:absolute;inset:0;background:rgba(17,24,39,.5);backdrop-filter:blur(4px)}
    .rjapi-dlg{position:relative;width:100%;max-width:440px;background:#fff;border-radius:20px;padding:24px;box-shadow:0 25px 50px rgba(0,0,0,.25)}
    .rjapi-dlg-top{display:flex;gap:16px}
    .rjapi-dlg .wi{width:46px;height:46px;border-radius:50%;background:#fee2e2;color:#dc2626;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .rjapi-dlg .wi svg{width:24px;height:24px}
    .rjapi-dlg h4{margin:0;font-size:16px;font-weight:700}
    .rjapi-dlg .nm{margin:4px 0 0;font-size:14px;font-weight:600;color:#374151;word-break:break-word}
    .rjapi-dlg .tx{margin:6px 0 0;font-size:13px;color:#4b5563;line-height:1.5}
    .rjapi-dlg-act{display:flex;justify-content:flex-end;gap:8px;margin-top:22px}
    .rjapi-b2{padding:9px 16px;border-radius:10px;border:0;background:#f3f4f6;color:#374151;font-size:13px;font-weight:600}
    .rjapi-b2:hover{background:#e5e7eb}
    .rjapi-b3{display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:10px;border:0;background:#dc2626;color:#fff;font-size:13px;font-weight:600}
    .rjapi-b3:hover{background:#b91c1c}
    .rjapi-b2:disabled,.rjapi-b3:disabled{opacity:.55;cursor:not-allowed}
    .rjapi-toast{position:fixed;bottom:24px;right:24px;z-index:60;display:flex;align-items:center;gap:10px;padding:12px 20px;border-radius:12px;color:#fff;font-size:14px;font-weight:500;max-width:360px;box-shadow:0 20px 40px rgba(0,0,0,.25)}
    .rjapi-toast.success{background:#059669}
    .rjapi-toast.error{background:#dc2626}
    .spin{animation:rjspin 1s linear infinite}
    @@keyframes rjspin{to{transform:rotate(360deg)}}
    @@media (max-width:700px){
        .rjapi-li{grid-template-columns:1fr;gap:6px}
        .rjapi-li.hd{display:none}
        .rjapi-hero{padding:22px}
    }
</style>

<div class="rjapi" x-data="apiSettings()" @keydown.escape.window="revoke.show = false">

    {{-- HERO --}}
    <div class="rjapi-hero">
        <h2>{{ __('API & Integrations') }}</h2>
        <p>{{ __('Manage API keys to allow external applications to access your site data.') }}</p>
        <div class="rjapi-stats">
            <div class="rjapi-stat"><b x-text="keys.length"></b><span>{{ __('Active API Keys') }}</span></div>
            <div class="rjapi-stat"><b>{{ $endpointList->count() }}</b><span>{{ __('Endpoints') }}</span></div>
            <div class="rjapi-stat"><b>v1</b><span>{{ __('Version') }}</span></div>
        </div>
    </div>

    @if (session('success'))
        <div x-data="{ open: true }" x-init="setTimeout(() => open = false, 5000)" x-show="open" x-transition.opacity role="status" class="rjapi-alert ok">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div role="alert" class="rjapi-alert err">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- ============ API KEYS ============ --}}
    <div class="rjapi-card">
        <div class="rjapi-head">
            <div class="rjapi-head-l">
                <div class="rjapi-ico i">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <div>
                    <h3>{{ __('Active API Keys') }}</h3>
                    <p>{{ __('Use these keys to authenticate your external requests.') }}</p>
                </div>
            </div>
            <span class="rjapi-count" x-text="keys.length"></span>
        </div>

        <div class="rjapi-body">
            <div class="rjapi-alert warn">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                <span>{{ __('Never share your private key or expose it in client-side code.') }}</span>
            </div>

            <form @submit.prevent="generateKey()" class="rjapi-gen">
                <label for="key_name">{{ __('Application Name') }}</label>
                <div class="rjapi-row">
                    <div class="rjapi-input">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <input id="key_name" type="text" x-model="newKeyName" required maxlength="60" placeholder="{{ __('e.g. Mobile App, Zapier') }}">
                    </div>
                    <button type="submit" class="rjapi-btn" :disabled="generating || !newKeyName.trim()">
                        <svg x-show="!generating" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <svg x-show="generating" x-cloak class="spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                        <span x-text="generating ? @js(__('Generating...')) : @js(__('Generate Key'))"></span>
                    </button>
                </div>
            </form>

            <div x-show="keys.length === 0" x-cloak class="rjapi-empty">
                <div class="ic">
                    <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <b>{{ __('No API keys generated yet.') }}</b>
                <span>{{ __('Create one above to start using the API.') }}</span>
            </div>

            <div x-show="keys.length > 0" x-cloak style="display:flex;flex-direction:column;gap:14px">
                <template x-for="key in keys" :key="key.id">
                    <div class="rjapi-key">
                        <div class="rjapi-key-top">
                            <div class="rjapi-key-id">
                                <div class="rjapi-av" x-text="(key.name || '?').substring(0, 1).toUpperCase()"></div>
                                <div style="min-width:0">
                                    <h4 x-text="key.name"></h4>
                                    <p>{{ __('Created') }}: <span x-text="key.created_at"></span></p>
                                </div>
                            </div>
                            <button type="button" class="rjapi-revoke" @click="askRevoke(key)">
                                <svg style="width:15px;height:15px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                {{ __('Revoke') }}
                            </button>
                        </div>

                        <div class="rjapi-fields">
                            <div class="rjapi-field">
                                <label>{{ __('Public Key') }}</label>
                                <div class="rjapi-val">
                                    <code x-text="key.public_key"></code>
                                    <button type="button" class="rjapi-ib" :class="copied === key.id + ':public' && 'done'" @click="copyText(key.public_key, key.id + ':public')" aria-label="{{ __('Copy') }}">
                                        <svg x-show="copied !== key.id + ':public'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        <svg x-show="copied === key.id + ':public'" x-cloak fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="rjapi-field">
                                <label>{{ __('Private Key') }}</label>
                                <div class="rjapi-val">
                                    <code x-text="showPrivate[key.id] ? key.private_key : '••••••••••••••••••••••••'"></code>
                                    <button type="button" class="rjapi-ib" @click="showPrivate[key.id] = !showPrivate[key.id]" aria-label="{{ __('Toggle visibility') }}">
                                        <svg x-show="!showPrivate[key.id]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showPrivate[key.id]" x-cloak fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                    </button>
                                    <button type="button" class="rjapi-ib" :class="copied === key.id + ':private' && 'done'" @click="copyText(key.private_key, key.id + ':private')" aria-label="{{ __('Copy') }}">
                                        <svg x-show="copied !== key.id + ':private'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        <svg x-show="copied === key.id + ':private'" x-cloak fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ============ DOCUMENTATION ============ --}}
    <div class="rjapi-card">
        <div class="rjapi-head">
            <div class="rjapi-head-l">
                <div class="rjapi-ico e">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h3>{{ __('API Documentation') }}</h3>
                    <p>{{ __('Available endpoints for external integrations.') }}</p>
                </div>
            </div>
            <span class="rjapi-count">{{ $endpointList->count() }}</span>
        </div>

        <div class="rjapi-body">

            {{-- Code examples --}}
            <div class="rjapi-code">
                <div class="rjapi-code-bar">
                    <div style="display:flex;align-items:center;gap:14px">
                        <div class="rjapi-dots">
                            <i style="background:#f87171"></i><i style="background:#fbbf24"></i><i style="background:#34d399"></i>
                        </div>
                        <span style="font-size:12px;font-weight:600;color:#9ca3af">{{ __('Authentication') }}</span>
                    </div>
                    <div class="rjapi-tabs">
                        <template x-for="t in Object.keys(snippets)" :key="t">
                            <button type="button" class="rjapi-tab" :class="tab === t && 'on'" @click="tab = t" x-text="t"></button>
                        </template>
                    </div>
                    <button type="button" class="rjapi-copy" @click="copyText(snippets[tab], 'code')">
                        <svg x-show="copied !== 'code'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <svg x-show="copied === 'code'" x-cloak style="color:#34d399" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="copied === 'code' ? @js(__('Copied')) : @js(__('Copy'))"></span>
                    </button>
                </div>
                <pre class="rjapi-pre" x-text="snippets[tab]"></pre>
            </div>

            {{-- Endpoints --}}
            <div class="rjapi-tools">
                <div class="rjapi-search">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                    <input type="text" x-model="q" placeholder="{{ __('Search') }}...">
                </div>
                <div class="rjapi-chips">
                    <button type="button" class="rjapi-chip" :class="method === 'ALL' && 'on'" @click="method = 'ALL'">ALL</button>
                    <template x-for="m in methods" :key="m">
                        <button type="button" class="rjapi-chip" :class="method === m && 'on'" @click="method = m" x-text="m"></button>
                    </template>
                </div>
            </div>

            <div class="rjapi-list">
                <div class="rjapi-li hd">
                    <span>{{ __('Method') }}</span><span>{{ __('Endpoint') }}</span><span>{{ __('Description') }}</span>
                </div>
                <template x-for="(e, i) in filtered()" :key="i">
                    <div class="rjapi-li">
                        <span><span class="rjapi-m" :class="'m-' + e.method" x-text="e.method"></span></span>
                        <span class="rjapi-uri" x-text="e.uri"></span>
                        <span class="rjapi-desc" x-text="e.desc"></span>
                    </div>
                </template>
                <div class="rjapi-none" x-show="filtered().length === 0" x-cloak>{{ __('No endpoints available.') }}</div>
            </div>
        </div>
    </div>

    {{-- ============ REVOKE DIALOG ============ --}}
    <div x-show="revoke.show" x-cloak class="rjapi-ov" role="dialog" aria-modal="true">
        <div class="rjapi-bd" x-show="revoke.show" x-transition.opacity @click="!revoke.loading && (revoke.show = false)"></div>
        <div class="rjapi-dlg" x-show="revoke.show"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="rjapi-dlg-top">
                <div class="wi">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div style="min-width:0">
                    <h4>{{ __('Revoke API key?') }}</h4>
                    <p class="nm" x-text="revoke.name"></p>
                    <p class="tx">{{ __('Are you sure you want to revoke this API key? External applications using it will lose access immediately.') }}</p>
                </div>
            </div>
            <div class="rjapi-dlg-act">
                <button type="button" class="rjapi-b2" @click="revoke.show = false" :disabled="revoke.loading">{{ __('Cancel') }}</button>
                <button type="button" class="rjapi-b3" @click="revokeKey()" :disabled="revoke.loading">
                    <svg x-show="revoke.loading" x-cloak class="spin" style="width:15px;height:15px" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                    {{ __('Revoke') }}
                </button>
            </div>
        </div>
    </div>

    {{-- ============ TOAST ============ --}}
    <div x-show="toast.show" x-cloak x-transition class="rjapi-toast" :class="toast.type">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span x-text="toast.message"></span>
    </div>
</div>

<script>
    function apiSettings() {
        const csrf = () => document.querySelector('meta[name="csrf-token"]').content;
        const base = @js(url('/api/v1/posts'));

        return {
            keys: @js($apiKeys),
            endpoints: @js($endpointList),
            newKeyName: '',
            generating: false,
            showPrivate: {},
            copied: null,
            tab: 'cURL',
            q: '',
            method: 'ALL',
            revoke: { show: false, id: null, name: '', loading: false },
            toast: { show: false, message: '', type: 'success' },
            _toastTimer: null,
            _copyTimer: null,

            snippets: {
                'cURL': `curl -X GET "${base}" \\\n  -H "X-Public-Key: YOUR_PUBLIC_KEY" \\\n  -H "X-Private-Key: YOUR_PRIVATE_KEY" \\\n  -H "Accept: application/json"`,
                'JavaScript': `const res = await fetch("${base}", {\n  headers: {\n    "X-Public-Key": "YOUR_PUBLIC_KEY",\n    "X-Private-Key": "YOUR_PRIVATE_KEY",\n    "Accept": "application/json"\n  }\n});\nconst data = await res.json();`,
                'PHP': `$response = Http::withHeaders([\n    'X-Public-Key'  => 'YOUR_PUBLIC_KEY',\n    'X-Private-Key' => 'YOUR_PRIVATE_KEY',\n])->acceptJson()->get('${base}');\n\n$data = $response->json();`,
                'Python': `import requests\n\nres = requests.get(\n    "${base}",\n    headers={\n        "X-Public-Key": "YOUR_PUBLIC_KEY",\n        "X-Private-Key": "YOUR_PRIVATE_KEY",\n        "Accept": "application/json",\n    },\n)\nprint(res.json())`
            },

            get methods() {
                return [...new Set(this.endpoints.map(e => e.method))];
            },

            init() {
                this.keys.forEach(k => { this.showPrivate[k.id] = false; });
            },

            filtered() {
                const q = this.q.trim().toLowerCase();
                return this.endpoints.filter(e =>
                    (this.method === 'ALL' || e.method === this.method) &&
                    (!q || e.uri.toLowerCase().includes(q) || (e.desc || '').toLowerCase().includes(q))
                );
            },

            firstError(data, fallback) {
                if (data && data.errors) {
                    const first = Object.values(data.errors)[0];
                    if (Array.isArray(first) && first.length) return first[0];
                }
                return (data && data.message) || fallback;
            },

            async generateKey() {
                const name = this.newKeyName.trim();
                if (!name || this.generating) return;
                this.generating = true;

                try {
                    const res = await fetch('{{ route('admin.settings.api.generate') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf() },
                        body: JSON.stringify({ name })
                    });
                    const data = await res.json().catch(() => ({}));

                    if (res.ok && data.success) {
                        this.keys.push(data.key);
                        this.showPrivate[data.key.id] = false;
                        this.newKeyName = '';
                        this.showToast(data.message || @js(__('Key generated.')), 'success');
                    } else {
                        this.showToast(this.firstError(data, @js(__('Failed to generate key.'))), 'error');
                    }
                } catch (e) {
                    this.showToast(@js(__('Network error.')), 'error');
                } finally {
                    this.generating = false;
                }
            },

            async copyText(text, id) {
                let ok = false;
                try {
                    await navigator.clipboard.writeText(text);
                    ok = true;
                } catch (e) {
                    try {
                        const ta = document.createElement('textarea');
                        ta.value = text;
                        ta.setAttribute('readonly', '');
                        ta.style.position = 'fixed';
                        ta.style.opacity = '0';
                        document.body.appendChild(ta);
                        ta.select();
                        ok = document.execCommand('copy');
                        document.body.removeChild(ta);
                    } catch (_) { ok = false; }
                }

                if (ok) {
                    this.copied = id;
                    clearTimeout(this._copyTimer);
                    this._copyTimer = setTimeout(() => { this.copied = null; }, 1800);
                    this.showToast(@js(__('Copied to clipboard.')), 'success');
                } else {
                    this.showToast(@js(__('Could not copy.')), 'error');
                }
            },

            askRevoke(key) {
                this.revoke = { show: true, id: key.id, name: key.name, loading: false };
            },

            async revokeKey() {
                if (this.revoke.loading || this.revoke.id === null) return;
                this.revoke.loading = true;
                const id = this.revoke.id;

                try {
                    const res = await fetch(`{{ url('/admin/settings/api') }}/${id}/revoke`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf(),
                            'X-HTTP-Method-Override': 'DELETE'
                        },
                        body: JSON.stringify({ _method: 'DELETE' })
                    });
                    const data = await res.json().catch(() => ({}));

                    if (res.ok && data.success) {
                        this.keys = this.keys.filter(k => k.id !== id);
                        delete this.showPrivate[id];
                        this.revoke.show = false;
                        this.showToast(data.message || @js(__('Key revoked.')), 'success');
                    } else {
                        this.showToast(this.firstError(data, @js(__('Failed to revoke key.'))), 'error');
                    }
                } catch (e) {
                    this.showToast(@js(__('Network error.')), 'error');
                } finally {
                    this.revoke.loading = false;
                }
            },

            showToast(message, type) {
                this.toast = { show: true, message, type: type || 'success' };
                clearTimeout(this._toastTimer);
                this._toastTimer = setTimeout(() => { this.toast.show = false; }, 3500);
            }
        };
    }
</script>
@endsection