<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ rj_dir() }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Identity Verification') }} — {{ rj_get_option('site_name', config('app.name')) }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Figtree', sans-serif; }
        :root { {!! rj_css_variables() !!} }
    </style>
</head>
<body class="h-full text-gray-900 antialiased flex flex-col">

    <header class="w-full bg-white border-b border-gray-200 py-4 px-6 flex items-center justify-between">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
            @php $logoMask = rj_logo_mask_url(); @endphp
            @if ($logoMask)
                <div class="h-8 w-28 bg-rj-dark-1" style="-webkit-mask-image: url('{{ $logoMask }}'); mask-image: url('{{ $logoMask }}'); -webkit-mask-size: contain; mask-size: contain; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-position: center; mask-position: center;"></div>
            @else
                <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-rj-purple to-rj-pink">{{ rj_get_option('site_name', 'RJ CMS') }}</span>
            @endif
        </a>
        <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-gray-900 transition">{{ __('Back to Home') }}</a>
    </header>

    <main class="flex-1 flex items-center justify-center p-4 py-12">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-6 md:p-8 border-b border-gray-100 text-center bg-gradient-to-b from-gray-50 to-white">
                <div class="w-16 h-16 mx-auto bg-rj-purple/10 text-rj-purple rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('Identity Verification') }}</h1>
                <p class="text-sm text-gray-500 mt-2 max-w-md mx-auto">{{ __('Please upload the required documents to verify your identity. This process helps us keep your account secure.') }}</p>
            </div>

            <div class="p-6 md:p-8">
                @php $user = auth()->user(); @endphp

                @if ($user->kyc_status === 'pending')
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-xl flex items-start gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <h3 class="font-medium">{{ __('Documents Under Review') }}</h3>
                            <p class="text-sm mt-1">{{ __('Your documents have been submitted and are currently being reviewed by our team. We will notify you once the process is complete.') }}</p>
                        </div>
                    </div>
                @elseif ($user->kyc_status === 'rejected')
                    <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl flex items-start gap-3 mb-6">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <h3 class="font-medium">{{ __('Verification Rejected') }}</h3>
                            <p class="text-sm mt-1">{{ __('Your previous submission was rejected. Please check the requirements and try again.') }}</p>
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

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Document Type') }}</label>
                            <select name="document_type" class="w-full rounded-xl border-gray-300 focus:border-rj-purple focus:ring-rj-purple" required>
                                <option value="id_card">{{ __('National ID Card') }}</option>
                                <option value="passport">{{ __('Passport') }}</option>
                            </select>
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
                    <div class="bg-green-50 border border-green-200 text-green-800 p-6 rounded-xl text-center flex flex-col items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="text-lg font-bold">{{ __('Identity Verified') }}</h3>
                        <p class="text-sm mt-1">{{ __('Your account has been successfully verified. You now have full access to all features.') }}</p>
                        <a href="{{ url('/') }}" class="mt-4 px-6 py-2 bg-white text-green-700 font-medium rounded-lg shadow-sm border border-green-200 hover:bg-green-50 transition">{{ __('Return to Home') }}</a>
                    </div>
                @endif
            </div>
        </div>
    </main>

</body>
</html>
