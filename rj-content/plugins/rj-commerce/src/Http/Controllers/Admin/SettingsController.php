<?php

namespace RjCommerce\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function index()
    {
        $options = [
            'store_name' => rj_get_option('commerce_store_name', rj_get_option('site_name')),
            'currency' => rj_get_option('commerce_currency', 'USD'),
            'weight_unit' => rj_get_option('commerce_weight_unit', 'kg'),
            'stripe_enabled' => rj_get_option('commerce_stripe_enabled', '0'),
            'paypal_enabled' => rj_get_option('commerce_paypal_enabled', '0'),
            'cod_enabled' => rj_get_option('commerce_cod_enabled', '1'),
            'tax_enabled' => rj_get_option('commerce_tax_enabled', '0'),
            'default_tax_rate' => rj_get_option('commerce_default_tax_rate', '0'),
            'api_enabled' => rj_get_option('commerce_api_enabled', '0'),
            'api_public_key' => rj_get_option('commerce_api_public_key'),
            'api_secret_key' => rj_get_option('commerce_api_secret_key'),
            'notification_emails' => rj_get_option('commerce_notification_emails', ''),
        ];

        return view('plugin-rj-commerce::admin.settings.index', compact('options'));
    }

    public function update(Request $request)
    {
        $tab = $request->input('tab', 'general');

        if ($tab === 'general') {
            $data = $request->validate([
                'store_name' => 'nullable|string|max:255',
                'currency' => 'required|string|max:3',
                'weight_unit' => 'required|string|max:10',
            ]);
            rj_set_option('commerce_store_name', $data['store_name'] ?? '');
            rj_set_option('commerce_currency', $data['currency']);
            rj_set_option('commerce_weight_unit', $data['weight_unit']);
        }
        elseif ($tab === 'payments') {
            rj_set_option('commerce_stripe_enabled', $request->boolean('stripe_enabled') ? '1' : '0');
            rj_set_option('commerce_paypal_enabled', $request->boolean('paypal_enabled') ? '1' : '0');
            rj_set_option('commerce_cod_enabled', $request->boolean('cod_enabled') ? '1' : '0');
        }
        elseif ($tab === 'tax') {
            rj_set_option('commerce_tax_enabled', $request->boolean('tax_enabled') ? '1' : '0');
            rj_set_option('commerce_default_tax_rate', $request->input('default_tax_rate', '0'));
        }
        elseif ($tab === 'api') {
            rj_set_option('commerce_api_enabled', $request->boolean('api_enabled') ? '1' : '0');
            rj_set_option('commerce_notification_emails', $request->input('notification_emails', ''));

            if ($request->boolean('generate_keys')) {
                rj_set_option('commerce_api_public_key', 'pk_' . Str::random(32));
                rj_set_option('commerce_api_secret_key', 'sk_' . Str::random(48));
            }
        }

        return back()->with('success', __('Settings saved successfully.'));
    }

    public function apiDocs()
    {
        return view('plugin-rj-commerce::admin.api-docs');
    }
}
