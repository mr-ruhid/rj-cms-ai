<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Test Email') }}</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f9fafb; padding:40px 0; margin:0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <tr>
            <td style="background:linear-gradient(90deg,#7c3aed,#ec4899); padding:24px; text-align:center;">
                <h1 style="color:#ffffff; margin:0; font-size:20px;">{{ rj_get_option('site_name', 'RJ CMS') }}</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:32px 24px; color:#111827;">
                <h2 style="margin:0 0 12px; font-size:18px;">{{ __('Test Email') }}</h2>
                <p style="margin:0 0 16px; color:#4b5563; line-height:1.6;">
                    {{ __('This is a test email from your site. If you received this message, your SMTP settings are working correctly.') }}
                </p>
                <p style="margin:0; color:#6b7280; font-size:13px;">
                    {{ __('Sent at') }}: {{ now()->format('Y-m-d H:i:s') }}
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding:16px 24px; background:#f9fafb; color:#9ca3af; font-size:12px; text-align:center;">
                &copy; {{ date('Y') }} {{ rj_get_option('site_name', 'RJ CMS') }}
            </td>
        </tr>
    </table>
</body>
</html>
