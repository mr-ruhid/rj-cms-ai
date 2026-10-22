@extends('frontend.account.layout')

@section('title', __('Identity Verification'))

@section('account_content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 md:p-8 border-b border-gray-100 bg-gradient-to-b from-gray-50 to-white">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Identity Verification') }}</h1>
        <p class="text-sm text-gray-500 mt-2 max-w-xl">{{ __('Please upload the required documents to verify your identity. This process helps us keep your account secure and unlock all platform features.') }}</p>
    </div>

    <div class="p-6 md:p-8">
        @php $user = auth()->user(); @endphp

        @if ($user->kyc_status === 'pending')
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-5 rounded-xl flex items-start gap-3">
                <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <h3 class="font-bold">{{ __('Documents Under Review') }}</h3>
                    <p class="text-sm mt-1">{{ __('Your documents have been submitted and are currently being reviewed by our team. We will notify you via email once the process is complete.') }}</p>
                </div>
            </div>
        @elseif ($user->kyc_status === 'rejected')
            <div class="bg-red-50 border border-red-200 text-red-800 p-5 rounded-xl flex items-start gap-3 mb-8">
                <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <h3 class="font-bold">{{ __('Verification Rejected') }}</h3>
                    <p class="text-sm mt-1">{{ __('Your previous submission was rejected. Please check the requirements and try again with clear photos.') }}</p>
                </div>
            </div>
        @endif

        @if ($user->kyc_status !== 'pending' && $user->kyc_status !== 'approved')
            <form method="POST" action="{{ route('frontend.kyc.submit') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm border border-red-100">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Document Type') }}</label>
                        <select name="document_type" class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple" required>
                            <option value="id_card">{{ __('National ID Card') }}</option>
                            <option value="passport">{{ __('Passport') }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Document Front') }}</label>
                        <input type="file" name="document_front" accept="image/*" class="w-full rounded-xl border border-gray-300 p-2 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-rj-purple/10 file:text-rj-purple hover:file:bg-rj-purple/20" required>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Clear photo of the front side.') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Document Back') }} <span class="text-gray-400 font-normal">({{ __('Optional') }})</span></label>
                        <input type="file" name="document_back" accept="image/*" class="w-full rounded-xl border border-gray-300 p-2 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                        <p class="text-xs text-gray-500 mt-1">{{ __('Clear photo of the back side.') }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Selfie with Document') }}</label>
                    <input type="file" name="selfie" accept="image/*" class="w-full rounded-xl border border-gray-300 p-2 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-rj-purple/10 file:text-rj-purple hover:file:bg-rj-purple/20" required>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Upload a selfie holding your document next to your face.') }}</p>
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-rj-purple to-rj-pink text-white font-semibold rounded-xl hover:opacity-90 transition shadow-sm hover:shadow">
                        {{ __('Submit Documents') }}
                    </button>
                </div>
            </form>
        @elseif ($user->kyc_status === 'approved')
            <div class="bg-green-50 border border-green-200 text-green-800 p-8 rounded-xl text-center flex flex-col items-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-xl font-bold">{{ __('Identity Verified') }}</h3>
                <p class="text-sm mt-2 max-w-md mx-auto">{{ __('Your account has been successfully verified. You now have full access to all features.') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
