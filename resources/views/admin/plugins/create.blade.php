@extends('admin.layouts.app')

@section('title', __('Add Plugin'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="addPlugin()">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.plugins.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Add New Plugin') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('Install from a ZIP file, GitHub repository, or browse the community registry.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-rj-purple/20 to-rj-pink/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-rj-purple" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.755-7.93 5 5 0 019.58-1.55A5 5 0 0119 16M12 12v9m0 0l-3-3m3 3l3-3"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Upload ZIP') }}</h3>
                    <p class="text-xs text-gray-500">{{ __('Local plugin package') }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.plugins.upload') }}" enctype="multipart/form-data" class="flex-1 flex flex-col" @submit="uploading = true">
                @csrf

                <div class="flex-1 mb-4">
                    <input type="file" name="file" accept=".zip" x-ref="fileInput" @change="handleFile" class="hidden" required>

                    <div @click="$refs.fileInput.click()"
                         @dragover.prevent="dragover = true"
                         @dragleave.prevent="dragover = false"
                         @drop.prevent="handleDrop($event)"
                         :class="dragover ? 'border-rj-purple bg-rj-purple/5' : 'border-gray-300 hover:border-gray-400 bg-gray-50'"
                         class="h-full min-h-[180px] border-2 border-dashed rounded-lg flex flex-col items-center justify-center text-center cursor-pointer transition p-4">

                        <template x-if="!file">
                            <div class="space-y-2">
                                <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.755-7.93 5 5 0 019.58-1.55A5 5 0 0119 16M12 12v9m0 0l-3-3m3 3l3-3"/></svg>
                                <p class="text-xs text-gray-600 font-medium">{{ __('Drop ZIP here') }}</p>
                                <p class="text-xs text-gray-400">{{ __('or click to browse') }}</p>
                            </div>
                        </template>

                        <template x-if="file">
                            <div class="space-y-2 w-full">
                                <svg class="w-8 h-8 mx-auto text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <p class="text-xs text-gray-900 font-medium truncate px-2" x-text="file ? file.name : ''"></p>
                                <p class="text-xs text-gray-500" x-text="file ? formatSize(file.size) : ''"></p>
                                <button type="button" @click.stop="clearFile()" class="text-xs text-red-600 hover:underline">{{ __('Remove') }}</button>
                            </div>
                        </template>
                    </div>
                </div>

                <button type="submit" :disabled="!file || uploading"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition disabled:opacity-50">
                    <svg x-show="!uploading" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <svg x-show="uploading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                    <span x-text="uploading ? '{{ __('Uploading...') }}' : '{{ __('Upload & Install') }}'"></span>
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500/20 to-indigo-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .5C5.4.5 0 5.9 0 12.5c0 5.3 3.4 9.8 8.2 11.4.6.1.8-.3.8-.6v-2c-3.3.7-4-1.6-4-1.6-.5-1.4-1.3-1.8-1.3-1.8-1.1-.7.1-.7.1-.7 1.2.1 1.8 1.2 1.8 1.2 1.1 1.8 2.8 1.3 3.5 1 .1-.8.4-1.3.7-1.6-2.7-.3-5.5-1.3-5.5-5.9 0-1.3.5-2.4 1.2-3.2-.1-.3-.5-1.5.1-3.2 0 0 1-.3 3.3 1.2.9-.3 2-.4 3-.4s2.1.1 3 .4c2.3-1.5 3.3-1.2 3.3-1.2.6 1.7.2 2.9.1 3.2.8.8 1.2 1.9 1.2 3.2 0 4.6-2.8 5.6-5.5 5.9.4.4.8 1.1.8 2.2v3.3c0 .3.2.7.8.6C20.6 22.3 24 17.8 24 12.5 24 5.9 18.6.5 12 .5z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('GitHub URL') }}</h3>
                    <p class="text-xs text-gray-500">{{ __('Install from repository') }}</p>
                </div>
            </div>

            <form @submit.prevent="installFromGitHub()" class="flex-1 flex flex-col">
                <div class="flex-1 space-y-3 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Repository URL') }}</label>
                        <input type="text" x-model="githubUrl" @input="parseGitHubUrl()"
                               placeholder="https://github.com/user/repo"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs font-mono">
                    </div>

                    <div x-show="repo" x-cloak>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Repo (owner/repo)') }}</label>
                        <input type="text" x-model="repo" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs font-mono">
                    </div>

                    <div x-show="zipUrl" x-cloak>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('ZIP URL') }}</label>
                        <input type="text" x-model="zipUrl" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs font-mono">
                    </div>

                    <div x-show="slug" x-cloak>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('Slug') }}</label>
                        <input type="text" x-model="slug" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-xs font-mono">
                    </div>
                </div>

                <button type="submit" :disabled="!repo || !zipUrl || !slug || installing"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium hover:opacity-90 transition disabled:opacity-50">
                    <svg x-show="!installing" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <svg x-show="installing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity=".25"/><path fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
                    <span x-text="installing ? '{{ __('Installing...') }}' : '{{ __('Install from GitHub') }}'"></span>
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500/20 to-teal-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('Browse Registry') }}</h3>
                    <p class="text-xs text-gray-500">{{ __('Community plugins') }}</p>
                </div>
            </div>

            <div class="flex-1 flex flex-col items-center justify-center text-center mb-4 py-6">
                <svg class="w-16 h-16 text-gray-200 mb-3" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <p class="text-xs text-gray-500">{{ __('Explore verified plugins from the official registry.') }}</p>
            </div>

            <a href="{{ route('admin.plugins.browse') }}"
               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-medium hover:opacity-90 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                {{ __('Browse Plugins') }}
            </a>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="text-sm text-blue-800 leading-relaxed">
            <strong>{{ __('Plugin requirements:') }}</strong>
            <ul class="mt-1 ml-4 list-disc space-y-0.5 text-xs">
                <li>{{ __('ZIP must contain plugin.json at root (or one level deep)') }}</li>
                <li>{{ __('plugin.json must declare name, slug, version, namespace, provider') }}</li>
                <li>{{ __('Maximum size: 50 MB') }}</li>
                <li>{{ __('After installation, activate the plugin from the Plugins page.') }}</li>
            </ul>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('admin.plugins.index') }}" class="text-sm text-gray-500 hover:text-gray-900">
            {{ __('← Back to installed plugins') }}
        </a>
    </div>

    <div x-show="toast.show" x-cloak x-transition
         class="fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-2xl text-white text-sm font-medium z-50"
         :style="'background:' + (toast.type === 'error' ? '#dc2626' : '#059669')"
         x-text="toast.message"></div>
</div>

<script>
function addPlugin() {
    return {
        file: null,
        dragover: false,
        uploading: false,
        githubUrl: '',
        repo: '',
        zipUrl: '',
        slug: '',
        installing: false,
        toast: { show: false, message: '', type: 'success' },

        handleFile(e) {
            const f = e.target.files?.[0];
            if (f) this.file = f;
        },

        handleDrop(e) {
            this.dragover = false;
            const f = e.dataTransfer.files?.[0];
            if (!f) return;
            if (!f.name.endsWith('.zip')) {
                this.showToast('{{ __('Only .zip files are allowed.') }}', 'error');
                return;
            }
            this.file = f;
            const dt = new DataTransfer();
            dt.items.add(f);
            this.$refs.fileInput.files = dt.files;
        },

        clearFile() {
            this.file = null;
            this.$refs.fileInput.value = '';
        },

        formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1024 / 1024).toFixed(2) + ' MB';
        },

        parseGitHubUrl() {
            const url = this.githubUrl.trim();
            if (!url) return;
            const match = url.match(/github\.com[\/:]([^\/]+\/[^\/\?#]+?)(?:\.git)?(?:[\/?#]|$)/i);
            if (!match) return;
            this.repo = match[1];
            this.slug = match[1].split('/')[1].toLowerCase().replace(/[^a-z0-9-]/g, '-');
            this.zipUrl = 'https://github.com/' + this.repo + '/archive/refs/heads/main.zip';
        },

        async installFromGitHub() {
            this.installing = true;
            try {
                const res = await fetch('{{ route('admin.plugins.install-github') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        repo: this.repo,
                        zip_url: this.zipUrl,
                        slug: this.slug,
                    }),
                });
                const data = await res.json().catch(() => ({}));
                if (res.ok && data.success) {
                    this.showToast(data.message || '{{ __('Installed.') }}', 'success');
                    setTimeout(() => window.location.href = '{{ route('admin.plugins.index') }}', 1000);
                } else {
                    this.showToast(data.message || '{{ __('Installation failed.') }}', 'error');
                }
            } catch (e) {
                this.showToast('{{ __('Network error.') }}', 'error');
            } finally {
                this.installing = false;
            }
        },

        showToast(message, type) {
            this.toast = { show: true, message, type: type || 'success' };
            setTimeout(() => { this.toast.show = false; }, 3500);
        },
    };
}
</script>
@endsection