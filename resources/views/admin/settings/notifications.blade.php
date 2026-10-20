@extends('admin.settings.layout')

@section('title', __('Notifications'))

@section('settings')
<div class="space-y-6" x-data="notificationSettings()">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Notifications') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Configure email alerts for various events on your site.') }}</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r-lg">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form @submit.prevent="save()" class="space-y-6">
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-sm font-bold text-gray-900">{{ __('Recipient Address') }}</h3>
            </div>
            
            <div class="p-6">
                <div class="max-w-md">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Admin Email Address') }}</label>
                    <input type="email" x-model="form.admin_email" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                    <p class="text-xs text-gray-500 mt-2">{{ __('Where should system notifications be sent?') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">{{ __('Email Notifications') }}</h3>
                <span class="text-xs font-medium text-gray-500">{{ __('Toggle the events you want to be notified about.') }}</span>
            </div>

            <div class="divide-y divide-gray-100">

                <div class="p-6 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ __('New Comment') }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ __('Get an email when someone leaves a comment on your site.') }}</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                        <input type="checkbox" x-model="form.events.new_comment" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rj-purple/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rj-purple"></div>
                    </label>
                </div>

                <div class="p-6 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ __('New User Registration') }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ __('Get an email when a new user registers on your site.') }}</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                        <input type="checkbox" x-model="form.events.new_user" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rj-purple/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rj-purple"></div>
                    </label>
                </div>

                <div class="p-6 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ __('Post Published') }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ __('Get an email when a new post or page is published.') }}</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                        <input type="checkbox" x-model="form.events.post_published" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rj-purple/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rj-purple"></div>
                    </label>
                </div>

                <div class="p-6 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ __('Update Available') }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ __('Get an email when RJ CMS, themes, or plugins have updates available.') }}</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                        <input type="checkbox" x-model="form.events.update_available" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rj-purple/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rj-purple"></div>
                    </label>
                </div>

                <div class="p-6 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ __('Failed Login Attempt') }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ __('Get an email when someone fails to log in to the admin panel.') }}</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                        <input type="checkbox" x-model="form.events.failed_login" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rj-purple/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rj-purple"></div>
                    </label>
                </div>

                <div class="p-6 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ __('Form Submission') }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ __('Get an email when a user submits a form on your site.') }}</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                        <input type="checkbox" x-model="form.events.form_submission" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-rj-purple/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rj-purple"></div>
                    </label>
                </div>

            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <button type="submit" :disabled="saving" 
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition shadow-sm disabled:opacity-60">
                <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                <span x-text="saving ? '{{ __('Saving...') }}' : '{{ __('Save Changes') }}'"></span>
            </button>
        </div>

    </form>

    <div x-show="toast.show" x-cloak x-transition
         class="fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-2xl text-white text-sm font-bold z-[60]"
         :class="toast.type === 'error' ? 'bg-red-600' : 'bg-emerald-600'" x-text="toast.message"></div>

</div>
@endsection

@push('scripts')
<script>
function notificationSettings() {
    return {
        saving: false,
        form: {
            admin_email: @js($settings['admin_email']),
            events: @js($settings['events'])
        },
        toast: { show: false, message: '', type: 'success' },
        _toastTimer: null,

        async save() {
            this.saving = true;
            try {
                const res = await fetch('{{ route('admin.settings.notifications.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        _method: 'PUT',
                        admin_email: this.form.admin_email,
                        events: this.form.events
                    })
                });
                
                const data = await res.json();
                
                if (res.ok && data.success) {
                    this.showToast(data.message || '{{ __('Settings saved.') }}', 'success');
                } else {
                    this.showToast(data.message || '{{ __('Save failed.') }}', 'error');
                }
            } catch (e) {
                this.showToast('{{ __('Network error.') }}', 'error');
            } finally {
                this.saving = false;
            }
        },

        showToast(message, type) {
            this.toast = { show: true, message, type: type || 'success' };
            clearTimeout(this._toastTimer);
            this._toastTimer = setTimeout(() => { this.toast.show = false; }, 3500);
        }
    };
}
</script>
@endpush