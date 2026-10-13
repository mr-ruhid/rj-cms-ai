@extends('admin.layouts.app')

@section('title', __('Upload Media'))

@section('content')
<div class="space-y-6" x-data="mediaUpload()">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.media.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Upload Media') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('Drag and drop files, or click to browse.') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="space-y-6" @submit="uploading = true">
        @csrf

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">

            <div @dragover.prevent="dragging = true"
                 @dragleave.prevent="dragging = false"
                 @drop.prevent="handleDrop($event)"
                 @click="$refs.fileInput.click()"
                 :class="dragging ? 'border-rj-purple bg-rj-purple/5' : 'border-gray-300 hover:border-gray-400'"
                 class="border-2 border-dashed rounded-xl p-12 text-center cursor-pointer transition">

                <input type="file" name="files[]" multiple x-ref="fileInput" @change="handleFiles($event.target.files)" class="hidden" accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt">

                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 rounded-full bg-rj-purple/10 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-rj-purple" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-900">{{ __('Drop files here or click to browse') }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ __('Maximum file size: 10 MB per file') }}</p>
                </div>
            </div>

            <div x-show="files.length > 0" x-cloak class="mt-6">
                <div class="flex items-center justify-between mb-3">
                    <div class="text-sm font-medium text-gray-700">
                        <span x-text="files.length"></span> {{ __('file(s) selected') }}
                    </div>
                    <button type="button" @click="clearAll()" class="text-xs text-red-600 hover:underline">
                        {{ __('Clear all') }}
                    </button>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <template x-for="(file, index) in files" :key="index">
                        <div class="relative rounded-lg overflow-hidden border border-gray-200 group">
                            <div class="aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
                                <template x-if="file.preview">
                                    <img :src="file.preview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!file.preview">
                                    <div class="flex flex-col items-center justify-center p-3 text-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        <div class="text-[10px] text-gray-600 mt-2 truncate w-full" x-text="file.name"></div>
                                    </div>
                                </template>
                            </div>

                            <div class="p-2 bg-white">
                                <div class="text-xs text-gray-900 truncate" x-text="file.name"></div>
                                <div class="text-[10px] text-gray-500 mt-0.5" x-text="file.size_human"></div>
                            </div>

                            <button type="button" @click="removeFile(index)"
                                    class="absolute top-2 right-2 w-7 h-7 rounded-full bg-red-600 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <div x-show="files.length > 0" x-cloak class="flex items-center gap-3">
            <button type="submit" :disabled="uploading"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition disabled:opacity-50">
                <svg x-show="!uploading" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <svg x-show="uploading" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                <span x-text="uploading ? '{{ __('Uploading...') }}' : '{{ __('Upload :count file(s)') }}'.replace(':count', files.length)"></span>
            </button>

            <a href="{{ route('admin.media.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Cancel') }}
            </a>
        </div>

    </form>

</div>

<script>
function mediaUpload() {
    return {
        files: [],
        dragging: false,
        uploading: false,
        handleDrop(event) {
            this.dragging = false;
            this.addFiles(event.dataTransfer.files);
        },
        handleFiles(fileList) {
            this.addFiles(fileList);
        },
        addFiles(fileList) {
            Array.from(fileList).forEach(file => {
                const preview = file.type.startsWith('image/') ? URL.createObjectURL(file) : null;
                this.files.push({
                    file: file,
                    name: file.name,
                    size: file.size,
                    size_human: this.formatSize(file.size),
                    preview: preview,
                });
            });
            this.syncInput();
        },
        removeFile(index) {
            if (this.files[index].preview) {
                URL.revokeObjectURL(this.files[index].preview);
            }
            this.files.splice(index, 1);
            this.syncInput();
        },
        clearAll() {
            this.files.forEach(f => f.preview && URL.revokeObjectURL(f.preview));
            this.files = [];
            this.syncInput();
        },
        syncInput() {
            const dt = new DataTransfer();
            this.files.forEach(f => dt.items.add(f.file));
            this.$refs.fileInput.files = dt.files;
        },
        formatSize(bytes) {
            const units = ['B', 'KB', 'MB', 'GB'];
            let i = 0;
            while (bytes >= 1024 && i < units.length - 1) {
                bytes /= 1024;
                i++;
            }
            return bytes.toFixed(2) + ' ' + units[i];
        }
    }
}
</script>
@endsection