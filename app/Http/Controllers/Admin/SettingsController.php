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
        return view('admin.settings.general', compact('options'));
    }

    public function updateGeneral(Request $request)
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_description' => ['nullable', 'string', 'max:500'],
            'site_url' => ['required', 'url'],
            'admin_email' => ['required', 'email'],
            'timezone' => ['required', 'string'],
            'date_format' => ['required', 'string'],
            'time_format' => ['required', 'string'],
            'language' => ['required', 'string'],
            'currency' => ['required', 'string'],
        ]);

        foreach ($data as $name => $value) {
            $type = in_array($name, ['admin_email']) ? 'email' : 'text';
            Option::set($name, $value, 'general', $type);
        }

        return back()->with('success', __('Settings saved.'));
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
}
