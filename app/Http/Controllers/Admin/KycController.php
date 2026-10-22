<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycVerification;
use App\Models\User;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function index()
    {
        $verifications = KycVerification::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('admin.kyc.index', compact('verifications'));
    }

    public function show(KycVerification $verification)
    {
        $verification->load('user');
        return view('admin.kyc.show', compact('verification'));
    }

    public function approve(Request $request, KycVerification $verification)
    {
        $verification->update([
            'status' => 'approved',
            'rejection_reason' => null,
        ]);

        $user = $verification->user;
        $user->update([
            'kyc_status' => 'approved',
            'kyc_verified_at' => now(),
        ]);

        return redirect()->route('admin.kyc.index')->with('success', __('KYC verification approved successfully.'));
    }

    public function reject(Request $request, KycVerification $verification)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $verification->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        $verification->user->update([
            'kyc_status' => 'rejected',
            'kyc_verified_at' => null,
        ]);

        return redirect()->route('admin.kyc.index')->with('success', __('KYC verification rejected.'));
    }
}
