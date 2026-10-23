<?php

namespace RjCommerce\Controllers\Admin;

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
            'stripe_pk' => rj_get_option('commerce_stripe_pk', ''),
            'stripe_sk' => rj_get_option('commerce_stripe_sk', ''),

            'paypal_enabled' => rj_get_option('commerce_paypal_enabled', '0'),
            'paypal_client_id' => rj_get_option('commerce_paypal_client_id', ''),
            'paypal_secret' => rj_get_option('commerce_paypal_secret', ''),

            'checkout_enabled' => rj_get_option('commerce_checkout_enabled', '0'),
            'checkout_pk' => rj_get_option('commerce_checkout_pk', ''),
            'checkout_sk' => rj_get_option('commerce_checkout_sk', ''),

            'razorpay_enabled' => rj_get_option('commerce_razorpay_enabled', '0'),
            'razorpay_key' => rj_get_option('commerce_razorpay_key', ''),
            'razorpay_secret' => rj_get_option('commerce_razorpay_secret', ''),

            'crypto_enabled' => rj_get_option('commerce_crypto_enabled', '0'),
            'crypto_provider' => rj_get_option('commerce_crypto_provider', 'binance'),
            'crypto_api_key' => rj_get_option('commerce_crypto_api_key', ''),

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
            rj_set_option('commerce_stripe_pk', $request->input('stripe_pk', ''));
            rj_set_option('commerce_stripe_sk', $request->input('stripe_sk', ''));

            rj_set_option('commerce_paypal_enabled', $request->boolean('paypal_enabled') ? '1' : '0');
            rj_set_option('commerce_paypal_client_id', $request->input('paypal_client_id', ''));
            rj_set_option('commerce_paypal_secret', $request->input('paypal_secret', ''));

            rj_set_option('commerce_checkout_enabled', $request->boolean('checkout_enabled') ? '1' : '0');
            rj_set_option('commerce_checkout_pk', $request->input('checkout_pk', ''));
            rj_set_option('commerce_checkout_sk', $request->input('checkout_sk', ''));

            rj_set_option('commerce_razorpay_enabled', $request->boolean('razorpay_enabled') ? '1' : '0');
            rj_set_option('commerce_razorpay_key', $request->input('razorpay_key', ''));
            rj_set_option('commerce_razorpay_secret', $request->input('razorpay_secret', ''));

            rj_set_option('commerce_crypto_enabled', $request->boolean('crypto_enabled') ? '1' : '0');
            rj_set_option('commerce_crypto_provider', $request->input('crypto_provider', 'binance'));
            rj_set_option('commerce_crypto_api_key', $request->input('crypto_api_key', ''));

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
