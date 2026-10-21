<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function enable(Request $request)
    {
        $request->user()->generateTwoFactorSecret();

        return back()->with('success', __('Two-factor authentication secret generated. Please scan the QR code.'));
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $google2fa = app(Google2FA::class);
        $user = $request->user();

        if ($google2fa->verifyKey($user->two_factor_secret, $request->code)) {
            $user->confirmTwoFactorAuthentication();
            return back()->with('success', __('Two-factor authentication successfully enabled.'));
        }

        return back()->withErrors(['code' => __('The provided two-factor authentication code was invalid.')]);
    }

    public function disable(Request $request)
    {
        $request->user()->disableTwoFactorAuthentication();

        return back()->with('success', __('Two-factor authentication has been disabled.'));
    }
}