<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $form->name }}</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:'Inter', system-ui, -apple-system, sans-serif; color:#111827;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6; padding:40px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.06);">

                <tr>
                    <td style="padding:28px 32px; background:linear-gradient(135deg, #7c3aed, #ec4899); color:#ffffff;">
                        <h1 style="margin:0; font-size:20px; font-weight:600;">{{ $subject }}</h1>
                        <p style="margin:6px 0 0 0; font-size:13px; opacity:.85;">
                            {{ $form->name }}
                            @if($submission->created_at)
                                · {{ $submission->created_at->format('M j, Y H:i') }}
                            @endif
                        </p>
                    </td>
                </tr>

                @if($mode === 'user')
                    <tr>
                        <td style="padding:28px 32px 8px 32px;">
                            <p style="margin:0; font-size:15px; line-height:1.6; color:#374151;">
                                {{ __('Thank you for contacting us. We have received your message and will get back to you soon.') }}
                            </p>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td style="padding:28px 32px 8px 32px;">
                            <p style="margin:0; font-size:14px; color:#6b7280;">
                                {{ __('You received a new submission:') }}
                            </p>
                        </td>
                    </tr>
                @endif

                <tr>
                    <td style="padding:16px 32px 28px 32px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb; border-radius:10px; overflow:hidden;">
                            @foreach($fields as $index => $field)
                                @php
                                    $value = $submission->getField($field->name);
                                    $display = '';
                                    if (is_array($value)) {
                                        if (!empty($field->options())) {
                                            $labels = [];
                                            foreach ($field->options() as $opt) {
                                                if (in_array($opt['value'] ?? '', $value, true)) {
                                                    $labels[] = $opt['label'] ?? $opt['value'];
                                                }
                                            }
                                            $display = implode(', ', $labels);
                                        } else {
                                            $display = implode(', ', $value);
                                        }
                                    } else {
                                        $display = (string) $value;
                                    }
                                @endphp
                                <tr>
                                    <td style="padding:12px 16px; width:35%; background:{{ $index % 2 === 0 ? '#f9fafb' : '#ffffff' }}; border-bottom:1px solid #f3f4f6; font-size:12px; color:#6b7280; font-weight:600; text-transform:uppercase; letter-spacing:.04em;">
                                        {{ $field->label }}
                                    </td>
                                    <td style="padding:12px 16px; background:{{ $index % 2 === 0 ? '#f9fafb' : '#ffffff' }}; border-bottom:1px solid #f3f4f6; font-size:14px; color:#111827;">
                                        {!! nl2br(e($display)) ?: '<em style="color:#9ca3af">—</em>' !!}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>

                @if($mode === 'admin')
                    <tr>
                        <td style="padding:0 32px 28px 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #e5e7eb; padding-top:16px;">
                                <tr>
                                    <td style="font-size:12px; color:#9ca3af; line-height:1.6;">
                                        <strong style="color:#6b7280;">{{ __('Submission ID') }}:</strong> #{{ $submission->id }}<br>
                                        @if($submission->ip_address)
                                            <strong style="color:#6b7280;">{{ __('IP') }}:</strong> {{ $submission->ip_address }}<br>
                                        @endif
                                        @if($submission->user_id)
                                            <strong style="color:#6b7280;">{{ __('User ID') }}:</strong> {{ $submission->user_id }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endif

                <tr>
                    <td style="padding:20px 32px; background:#f9fafb; text-align:center; font-size:12px; color:#9ca3af; border-top:1px solid #e5e7eb;">
                        {{ rj_get_option('site_name', config('app.name')) }}
                        @if(rj_get_option('site_url'))
                            · {{ rj_get_option('site_url') }}
                        @endif
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>