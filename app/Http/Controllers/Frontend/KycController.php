<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Kyc\KycManager;
use Illuminate\Http\Request;

class KycController extends Controller
{
    protected KycManager $kycManager;

    public function __construct(KycManager $kycManager)
    {
        $this->kycManager = $kycManager;
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->kyc_status === 'approved') {
            return redirect()->route('frontend.profile')->with('success', __('Your identity is already verified.'));
        }

        return view('frontend.kyc', compact('user'));
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'document_type' => 'required|string|in:id_card,passport',
            'document_front' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'document_back' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'selfie' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $result = $this->kycManager->submit($request->user(), $validated);

        if ($result) {
            return redirect()->route('frontend.profile')->with('success', __('Your documents have been submitted for review.'));
        }

        return back()->with('error', __('Failed to submit KYC documents. Please try again.'))->withInput();
    }
}
