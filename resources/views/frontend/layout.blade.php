<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? rj_get_option('site_name', config('app.name')) }}</title>

    @if(rj_get_option('site_favicon'))
        <link rel="icon" href="{{ rj_get_option('site_favicon') }}">
    @endif

    {!! rj_global_color_css() !!}

    @include('rjbuilder.partials.styles')

    <style>
        body { margin: 0; font-family: 'Inter', system-ui, -apple-system, sans-serif; background: #fff; color: #111827; }
        .rj-frontend { min-height: 100vh; display: flex; flex-direction: column; }
        .rj-frontend > main { flex: 1; }
        .rj-frontend-wrapper { container-type: inline-size; container-name: rj; }
                /* ───────── Form widget ───────── */
        .rj-form-wrap { width: 100%; }
        .rj-form { display: flex; flex-direction: column; gap: 16px; margin: 0; }
        .rj-form-stack .rj-form-field { width: 100%; }
        .rj-form-inline { flex-direction: row; flex-wrap: wrap; align-items: flex-end; }
        .rj-form-inline .rj-form-field { flex: 1 1 200px; }
        .rj-form-inline .rj-form-actions { flex: 0 0 auto; }
        .rj-form-grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .rj-form-grid-2 .rj-form-field-full,
        .rj-form-grid-2 .rj-form-field-textarea,
        .rj-form-grid-2 .rj-form-actions { grid-column: 1 / -1; }

        .rj-form-field { display: flex; flex-direction: column; gap: 6px; }
        .rj-form-labels-inline .rj-form-field { flex-direction: row; align-items: center; gap: 12px; }
        .rj-form-labels-inline .rj-form-label { flex: 0 0 140px; margin: 0; }
        .rj-form-labels-inline .rj-form-input,
        .rj-form-labels-inline textarea,
        .rj-form-labels-inline select { flex: 1; }

        .rj-form-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            line-height: 1.4;
        }
        .rj-form-req { color: #ef4444; margin-left: 3px; }

        .rj-form-input,
        .rj-form textarea,
        .rj-form select {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            font-family: inherit;
            color: var(--rj-form-field-tc, #111827);
            background: var(--rj-form-field-bg, #ffffff);
            border: 1px solid var(--rj-form-field-bc, #d1d5db);
            border-radius: var(--rj-form-radius, 8px);
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .rj-form-input:focus,
        .rj-form textarea:focus,
        .rj-form select:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124,58,237,.12);
        }
        .rj-form textarea { resize: vertical; min-height: 100px; line-height: 1.5; }
        .rj-form select { cursor: pointer; }

        .rj-form-help {
            font-size: 11px;
            color: #6b7280;
            line-height: 1.4;
        }

        .rj-form-radio-group,
        .rj-form-checkbox-group { display: flex; flex-direction: column; gap: 8px; }
        .rj-form-radio,
        .rj-form-checkbox {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #374151;
            cursor: pointer;
            user-select: none;
        }
        .rj-form-radio input,
        .rj-form-checkbox input { accent-color: #7c3aed; cursor: pointer; }

        .rj-form-actions { display: flex; gap: 8px; }

        .rj-form-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 28px;
            font-size: 14px;
            font-weight: 600;
            color: var(--rj-form-btn-tc, #ffffff);
            background: var(--rj-form-btn-bg, #7c3aed);
            border: 0;
            border-radius: var(--rj-form-radius, 8px);
            cursor: pointer;
            transition: opacity .15s, transform .12s;
            font-family: inherit;
        }
        .rj-form-submit:hover { opacity: .92; }
        .rj-form-submit:active { transform: scale(.98); }
        .rj-form-submit:disabled { opacity: .6; cursor: default; }

        .rj-form-message {
            padding: 12px 16px;
            border-radius: var(--rj-form-radius, 8px);
            font-size: 13px;
            line-height: 1.5;
        }
        .rj-form-message.rj-ok { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .rj-form-message.rj-err { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        .rj-form-input.rj-invalid,
        .rj-form textarea.rj-invalid,
        .rj-form select.rj-invalid { border-color: #ef4444; }

        @media (max-width: 640px) {
            .rj-form-grid-2 { grid-template-columns: 1fr; }
            .rj-form-inline { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>
    <div class="rj-frontend rj-frontend-wrapper">
        @yield('content')
    </div>

    @include('frontend.partials.popup')

    @stack('scripts')
</body>
</html>