<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function general()
    {
        $options = $this->getGroup('general');

        $data = [
            'timezones' => $this->timezones(),
            'dateFormats' => $this->dateFormats(),
            'timeFormats' => $this->timeFormats(),
            'currencies' => $this->currencies(),
            'weekdays' => $this->weekdays(),
            'locales' => rj_available_locales(),
        ];

        return view('admin.settings.general', compact('options', 'data'));
    }

    public function updateGeneral(Request $request)
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:500'],
            'site_url' => ['required', 'url'],
            'admin_email' => ['required', 'email'],
            'timezone' => ['required', 'string'],
            'locale' => ['required', 'string'],
            'date_format' => ['required', 'string'],
            'time_format' => ['required', 'string'],
            'week_starts_on' => ['required', 'integer', 'between:0,6'],
            'currency' => ['required', 'string', 'max:8'],
            'currency_position' => ['required', 'in:left,right'],
            'thousands_separator' => ['nullable', 'string', 'max:3'],
            'decimal_separator' => ['nullable', 'string', 'max:3'],
            'decimal_digits' => ['nullable', 'integer', 'between:0,6'],
        ]);

        $typeMap = [
            'admin_email' => 'email',
            'week_starts_on' => 'number',
            'decimal_digits' => 'number',
        ];

        foreach ($data as $name => $value) {
            Option::set($name, $value, 'general', $typeMap[$name] ?? 'text');
        }

        return back()->with('success', __('Settings saved.'));
    }

    public function language()
    {
        $options = $this->getGroup('language');

        $data = [
            'locales' => rj_available_locales(),
        ];

        return view('admin.settings.language', compact('options', 'data'));
    }

    public function updateLanguage(Request $request)
    {
        $data = $request->validate([
            'admin_locale' => ['required', 'string'],
            'frontend_locale' => ['required', 'string'],
            'auto_detect_locale' => ['nullable', 'boolean'],
            'rtl_support' => ['nullable', 'boolean'],
            'ai_translation_enabled' => ['nullable', 'boolean'],
            'ai_translation_provider' => ['nullable', 'string'],
            'ai_translation_auto' => ['nullable', 'boolean'],
        ]);

        $booleanKeys = ['auto_detect_locale', 'rtl_support', 'ai_translation_enabled', 'ai_translation_auto'];

        foreach ($booleanKeys as $key) {
            $data[$key] = $request->boolean($key) ? '1' : '0';
        }

        foreach ($data as $name => $value) {
            Option::set($name, $value, 'language');
        }

        return back()->with('success', __('Language settings saved.'));
    }

    public function email()
    {
        $options = $this->getGroup('email');
        return view('admin.settings.email', compact('options'));
    }

    public function updateEmail(Request $request)
    {
        $data = $request->validate([
            'mail_driver' => ['required', 'in:smtp,sendmail,mailgun,ses,postmark,log,array'],
            'mail_host' => ['nullable', 'string', 'max:255'],
            'mail_port' => ['nullable', 'integer'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['nullable', 'in:tls,ssl,null'],
            'mail_from_address' => ['required', 'email'],
            'mail_from_name' => ['required', 'string', 'max:255'],
        ]);

        if ($data['mail_encryption'] === 'null') {
            $data['mail_encryption'] = null;
        }

        foreach ($data as $name => $value) {
            $type = in_array($name, ['mail_port']) ? 'number' : 'text';
            Option::set($name, $value, 'email', $type);
        }

        return back()->with('success', __('Email settings saved.'));
    }

    public function testEmail(Request $request)
    {
        $data = $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        try {
            Mail::to($data['test_email'])->send(new TestMail());
            return back()->with('success', __('Test email sent to :email', ['email' => $data['test_email']]));
        } catch (\Throwable $e) {
            return back()->with('error', __('Failed to send: :message', ['message' => $e->getMessage()]));
        }
    }

    public function security()
    {
        $options = $this->getGroup('security');
        return view('admin.settings.security', compact('options'));
    }

    public function updateSecurity(Request $request)
    {
        $data = $request->validate([
            'admin_2fa_enabled' => ['nullable', 'boolean'],
            'admin_2fa_method' => ['nullable', 'in:authenticator,email,sms'],
            'frontend_2fa_enabled' => ['nullable', 'boolean'],
            'frontend_2fa_mobile' => ['nullable', 'boolean'],
            'frontend_2fa_email' => ['nullable', 'boolean'],
            'frontend_2fa_authenticator' => ['nullable', 'boolean'],
            'kyc_enabled' => ['nullable', 'boolean'],
            'kyc_level' => ['nullable', 'in:none,basic,standard,advanced'],
            'kyc_required_fields' => ['nullable', 'array'],
            'login_limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'login_lockout_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'force_https' => ['nullable', 'boolean'],
            'captcha_enabled' => ['nullable', 'boolean'],
            'ip_whitelist_enabled' => ['nullable', 'boolean'],
            'ip_whitelist' => ['nullable', 'string'],
        ]);

        $booleanKeys = [
            'admin_2fa_enabled',
            'frontend_2fa_enabled',
            'frontend_2fa_mobile',
            'frontend_2fa_email',
            'frontend_2fa_authenticator',
            'kyc_enabled',
            'force_https',
            'captcha_enabled',
            'ip_whitelist_enabled',
        ];

        foreach ($booleanKeys as $key) {
            $data[$key] = $request->boolean($key) ? '1' : '0';
        }

        if (!isset($data['kyc_required_fields'])) {
            $data['kyc_required_fields'] = [];
        }

        foreach ($data as $name => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            Option::set($name, $value, 'security');
        }

        return back()->with('success', __('Security settings saved.'));
    }

    protected function getGroup(string $group): array
    {
        return Option::where('option_group', $group)
            ->pluck('option_value', 'option_name')
            ->toArray();
    }

    protected function timezones(): array
    {
        return [
            'Pacific/Midway', 'Pacific/Honolulu', 'America/Anchorage', 'America/Los_Angeles',
            'America/Denver', 'America/Chicago', 'America/New_York', 'America/Sao_Paulo',
            'Atlantic/Azores', 'Europe/London', 'Europe/Paris', 'Europe/Berlin',
            'Europe/Moscow', 'Asia/Dubai', 'Asia/Baku', 'Asia/Karachi',
            'Asia/Kolkata', 'Asia/Dhaka', 'Asia/Bangkok', 'Asia/Shanghai',
            'Asia/Tokyo', 'Australia/Sydney', 'Pacific/Auckland', 'UTC',
        ];
    }

    protected function dateFormats(): array
    {
        return [
            'Y-m-d' => '2026-01-15',
            'd/m/Y' => '15/01/2026',
            'm/d/Y' => '01/15/2026',
            'd.m.Y' => '15.01.2026',
            'd M Y' => '15 Jan 2026',
            'M d, Y' => 'Jan 15, 2026',
            'F j, Y' => 'January 15, 2026',
            'D, M j, Y' => 'Thu, Jan 15, 2026',
        ];
    }

    protected function timeFormats(): array
    {
        return [
            'H:i' => '14:30',
            'H:i:s' => '14:30:45',
            'h:i A' => '02:30 PM',
            'h:i:s A' => '02:30:45 PM',
            'g:i a' => '2:30 pm',
        ];
    }

    protected function currencies(): array
    {
        return [
            'USD' => 'US Dollar ($)',
            'EUR' => 'Euro (€)',
            'GBP' => 'British Pound (£)',
            'AZN' => 'Azerbaijani Manat (₼)',
            'TRY' => 'Turkish Lira (₺)',
            'RUB' => 'Russian Ruble (₽)',
            'JPY' => 'Japanese Yen (¥)',
            'CNY' => 'Chinese Yuan (¥)',
            'AED' => 'UAE Dirham (د.إ)',
            'SAR' => 'Saudi Riyal (﷼)',
            'INR' => 'Indian Rupee (₹)',
            'CAD' => 'Canadian Dollar (C$)',
            'AUD' => 'Australian Dollar (A$)',
        ];
    }

    protected function weekdays(): array
    {
        return [
            0 => __('Sunday'),
            1 => __('Monday'),
            2 => __('Tuesday'),
            3 => __('Wednesday'),
            4 => __('Thursday'),
            5 => __('Friday'),
            6 => __('Saturday'),
        ];
    }
}
