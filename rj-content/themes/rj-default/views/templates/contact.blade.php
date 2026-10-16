@extends('theme::layout')

@section('title', ($page->title ?? __('Contact')) . ' — ' . rj_get_option('site_name', config('app.name')))

@push('styles')
    <style>
        .rj-contact-hero {
            position: relative;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #4c1d95 100%);
            color: #fff;
            padding: 72px 24px 64px;
            overflow: hidden;
            text-align: center;
        }
        .rj-contact-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(139,92,246,.35), transparent 45%),
                radial-gradient(circle at 80% 70%, rgba(236,72,153,.25), transparent 45%);
            pointer-events: none;
        }
        .rj-contact-hero-inner {
            max-width: 720px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        .rj-contact-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            background: rgba(139,92,246,.2);
            border: 1px solid rgba(139,92,246,.35);
            color: #c4b5fd;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 18px;
            backdrop-filter: blur(8px);
        }
        .rj-contact-hero h1 {
            font-size: 44px;
            line-height: 1.15;
            font-weight: 800;
            margin: 0 0 16px;
            letter-spacing: -.025em;
            background: linear-gradient(135deg, #ffffff 0%, #e9d5ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .rj-contact-hero p {
            font-size: 17px;
            color: rgba(255,255,255,.7);
            line-height: 1.6;
            margin: 0 auto;
            max-width: 520px;
        }

        .rj-contact-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 72px 24px;
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 48px;
            align-items: start;
        }

        .rj-contact-form-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 24px -8px rgba(0,0,0,.06);
        }
        .rj-contact-form-card h2 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px;
            color: #0f172a;
            letter-spacing: -.02em;
        }
        .rj-contact-form-card > p {
            font-size: 14px;
            color: #6b7280;
            margin: 0 0 28px;
            line-height: 1.6;
        }

        .rj-contact-info {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .rj-contact-info-card {
            background: #fafafa;
            border: 1px solid #f3f4f6;
            border-radius: 16px;
            padding: 24px;
            display: flex;
            gap: 16px;
            align-items: flex-start;
            transition: all .2s;
        }
        .rj-contact-info-card:hover {
            border-color: #e9d5ff;
            background: #faf5ff;
        }
        .rj-contact-info-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 6px 16px -4px rgba(139,92,246,.4);
        }
        .rj-contact-info-icon svg {
            width: 20px;
            height: 20px;
        }
        .rj-contact-info-body h3 {
            font-size: 15px;
            font-weight: 700;
            margin: 0 0 4px;
            color: #0f172a;
        }
        .rj-contact-info-body p {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.5;
            margin: 0;
            word-break: break-word;
        }
        .rj-contact-info-body a {
            color: #7c3aed;
            text-decoration: none;
            font-weight: 500;
        }
        .rj-contact-info-body a:hover {
            text-decoration: underline;
        }

        .rj-contact-content {
            max-width: 760px;
            margin: 0 auto;
            padding: 0 24px 32px;
            font-size: 16px;
            line-height: 1.75;
            color: #1f2937;
        }

        @media (max-width: 900px) {
            .rj-contact-wrap {
                grid-template-columns: 1fr;
                gap: 32px;
                padding: 48px 20px;
            }
            .rj-contact-form-card { padding: 28px; }
        }
        @media (max-width: 640px) {
            .rj-contact-hero { padding: 56px 20px 48px; }
            .rj-contact-hero h1 { font-size: 30px; }
            .rj-contact-hero p { font-size: 15px; }
        }
    </style>
@endpush

@section('content')

<header class="rj-frontend-header">
    @if($header)
        {!! rj_render_template($header) !!}
    @endif
</header>

<main class="rj-frontend-main">

    <section class="rj-contact-hero">
        <div class="rj-contact-hero-inner">
            <span class="rj-contact-label">{{ __('Get in touch') }}</span>
            <h1>{{ $page->title ?? __('Contact Us') }}</h1>
            <p>{{ $page->excerpt ?: __('We would love to hear from you. Send us a message and we will get back as soon as possible.') }}</p>
        </div>
    </section>

    <div class="rj-contact-wrap">

        <div class="rj-contact-form-card">
            <h2>{{ __('Send a message') }}</h2>
            <p>{{ __('Fill out the form below and we will respond within 24 hours.') }}</p>

            <form method="POST" action="{{ route('frontend.form.submit', 'contact') }}" class="rj-form rj-form-stack">
                @csrf

                <div class="rj-form-field">
                    <label class="rj-form-label">{{ __('Name') }} <span class="rj-form-req">*</span></label>
                    <input type="text" name="name" class="rj-form-input" required>
                </div>

                <div class="rj-form-field">
                    <label class="rj-form-label">{{ __('Email') }} <span class="rj-form-req">*</span></label>
                    <input type="email" name="email" class="rj-form-input" required>
                </div>

                <div class="rj-form-field">
                    <label class="rj-form-label">{{ __('Subject') }}</label>
                    <input type="text" name="subject" class="rj-form-input">
                </div>

                <div class="rj-form-field">
                    <label class="rj-form-label">{{ __('Message') }} <span class="rj-form-req">*</span></label>
                    <textarea name="message" rows="5" class="rj-form-input" required></textarea>
                </div>

                <div class="rj-form-actions">
                    <button type="submit" class="rj-form-submit">
                        {{ __('Send Message') }}
                    </button>
                </div>

                <div class="rj-form-message" style="display:none;"></div>
            </form>
        </div>

        <div class="rj-contact-info">

            @if(rj_get_option('contact_email'))
                <div class="rj-contact-info-card">
                    <div class="rj-contact-info-icon">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="rj-contact-info-body">
                        <h3>{{ __('Email') }}</h3>
                        <p><a href="mailto:{{ rj_get_option('contact_email') }}">{{ rj_get_option('contact_email') }}</a></p>
                    </div>
                </div>
            @endif

            @if(rj_get_option('contact_phone'))
                <div class="rj-contact-info-card">
                    <div class="rj-contact-info-icon">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div class="rj-contact-info-body">
                        <h3>{{ __('Phone') }}</h3>
                        <p><a href="tel:{{ rj_get_option('contact_phone') }}">{{ rj_get_option('contact_phone') }}</a></p>
                    </div>
                </div>
            @endif

            @if(rj_get_option('contact_address'))
                <div class="rj-contact-info-card">
                    <div class="rj-contact-info-icon">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div class="rj-contact-info-body">
                        <h3>{{ __('Address') }}</h3>
                        <p>{{ rj_get_option('contact_address') }}</p>
                    </div>
                </div>
            @endif

            @if(!rj_get_option('contact_email') && !rj_get_option('contact_phone') && !rj_get_option('contact_address'))
                <div class="rj-contact-info-card">
                    <div class="rj-contact-info-icon">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="rj-contact-info-body">
                        <h3>{{ __('Contact info') }}</h3>
                        <p>{{ __('Add contact details in Settings → General to display them here.') }}</p>
                    </div>
                </div>
            @endif

        </div>

    </div>

    @if($page->content)
        <div class="rj-contact-content">
            {!! $page->content !!}
        </div>
    @endif

</main>

<footer class="rj-frontend-footer">
    @if($footer)
        {!! rj_render_template($footer) !!}
    @endif
</footer>

@endsection