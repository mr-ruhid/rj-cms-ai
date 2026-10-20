@extends('admin.layouts.app')

@section('title', __('Dashboard'))

@section('content')
<div class="space-y-6" x-data="aiChat()">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('Welcome back') }}, {{ auth()->user()->name }}! 👋</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Here is an overview of your site today.') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                {{ __('New Post') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Posts') }}</span>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['posts']) }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Pages') }}</span>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['pages']) }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Comments') }}</span>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['comments']) }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Users') }}</span>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['users']) }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Media') }}</span>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['media']) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900">{{ __('Recent Posts') }}</h3>
                    <a href="{{ route('admin.posts.index') }}" class="text-sm font-medium text-rj-purple hover:underline">{{ __('View All') }}</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentPosts as $post)
                        <div class="p-4 flex items-center justify-between gap-4 hover:bg-gray-50/50 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                @if($post->featured_image)
                                    <img src="{{ Str::startsWith($post->featured_image, 'http') ? $post->featured_image : asset($post->featured_image) }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0 border border-gray-200">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z"/></svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="text-sm font-medium text-gray-900 hover:text-rj-purple truncate block">{{ $post->title }}</a>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $post->created_at->diffForHumans() }} · {{ $post->author->name ?? '—' }}</div>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $post->status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ ucfirst($post->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="p-8 text-center text-sm text-gray-500">{{ __('No posts yet.') }}</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900">{{ __('Recent Comments') }}</h3>
                    <a href="{{ route('admin.comments.index') }}" class="text-sm font-medium text-rj-purple hover:underline">{{ __('View All') }}</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentComments as $comment)
                        <div class="p-4 hover:bg-gray-50/50 transition">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center flex-shrink-0 text-gray-600 font-bold text-xs border border-gray-300">
                                    {{ strtoupper(substr($comment->author_name ?? 'G', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-gray-900">{{ $comment->author_name ?? __('Guest') }}</div>
                                    <p class="text-xs text-gray-600 line-clamp-1 mt-0.5">{{ $comment->content }}</p>
                                    <div class="text-[10px] text-gray-400 mt-1">
                                        {{ __('On') }}: <a href="{{ route('admin.comments.show', $comment) }}" class="hover:underline text-gray-500">{{ $comment->post->title ?? '—' }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-sm text-gray-500">{{ __('No comments yet.') }}</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col h-[600px] sticky top-6">
                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-rj-purple to-rj-pink text-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold">{{ __('AI Assistant') }}</h3>
                        <p class="text-[10px] text-white/80">{{ __('Ask about site features') }}</p>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-4" x-ref="chatContainer">
                    <template x-for="(msg, index) in messages" :key="index">
                        <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                            <div class="max-w-[85%] rounded-2xl px-4 py-2.5 text-sm"
                                 :class="msg.role === 'user' 
                                    ? 'bg-rj-purple text-white rounded-br-sm' 
                                    : 'bg-gray-100 text-gray-800 rounded-bl-sm'">
                                <div x-html="formatMessage(msg.text)"></div>
                            </div>
                        </div>
                    </template>

                    <div x-show="loading" class="flex justify-start">
                        <div class="bg-gray-100 rounded-2xl rounded-bl-sm px-4 py-3 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce"></span>
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.15s"></span>
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                        </div>
                    </div>
                </div>

                <div class="p-3 border-t border-gray-100 bg-gray-50/50">
                    <div class="flex flex-wrap gap-1.5 mb-2" x-show="messages.length === 1">
                        <button @click="askQuick('How do I create a new post?')" class="text-[10px] bg-white border border-gray-200 text-gray-600 px-2.5 py-1 rounded-full hover:border-rj-purple hover:text-rj-purple transition">{{ __('How to create a post?') }}</button>
                        <button @click="askQuick('How do I change the theme?')" class="text-[10px] bg-white border border-gray-200 text-gray-600 px-2.5 py-1 rounded-full hover:border-rj-purple hover:text-rj-purple transition">{{ __('How to change theme?') }}</button>
                        <button @click="askQuick('How do I add a menu?')" class="text-[10px] bg-white border border-gray-200 text-gray-600 px-2.5 py-1 rounded-full hover:border-rj-purple hover:text-rj-purple transition">{{ __('How to add a menu?') }}</button>
                    </div>

                    <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                        <input type="text" x-model="input" :disabled="loading" placeholder="{{ __('Type your question...') }}" class="flex-1 px-3 py-2 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm bg-white">
                        <button type="submit" :disabled="!input.trim() || loading" class="w-9 h-9 rounded-xl bg-rj-purple text-white flex items-center justify-center hover:opacity-90 transition disabled:opacity-50 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function aiChat() {
    return {
        messages: [
            { role: 'assistant', text: 'Salam! Mən RJ CMS-in AI köməkçisiyəm. Saytın funksiyaları, yazı yaratmaq, menyu əlavə etmək və ya digər mövzular haqqında mənə sual verə bilərsiniz.' }
        ],
        input: '',
        loading: false,

        async sendMessage() {
            const text = this.input.trim();
            if (!text || this.loading) return;

            this.messages.push({ role: 'user', text });
            this.input = '';
            this.loading = true;
            this.scrollToBottom();

            try {
                const res = await fetch('{{ route('admin.ask-ai') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ question: text })
                });

                const data = await res.json();

                if (data.success) {
                    this.messages.push({ role: 'assistant', text: data.answer });
                } else {
                    this.messages.push({ role: 'assistant', text: data.message || 'Xəta baş verdi. Zəhmət olmasa yenidən cəhd edin.' });
                }
            } catch (e) {
                this.messages.push({ role: 'assistant', text: 'Şəbəkə xətası. İnternet bağlantınızı yoxlayın.' });
            } finally {
                this.loading = false;
                this.scrollToBottom();
            }
        },

        askQuick(text) {
            this.input = text;
            this.sendMessage();
        },

        scrollToBottom() {
            this.$nextTick(() => {
                this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
            });
        },

        formatMessage(text) {
            return text.replace(/\n/g, '<br>');
        }
    }
}
</script>
@endpush