@extends('admin.layouts.app')

@section('title', __('AI Assistant'))

@section('content')
<div class="h-[calc(100vh-8rem)] bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col" x-data="aiChat(@js($stats))">

    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center text-white shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ __('RJ AI Assistant') }}</h2>
                <p class="text-xs text-gray-500 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    {{ __('Online & Ready to help') }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button @click="clearChat()" class="text-sm font-medium text-gray-500 hover:text-red-600 transition flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-red-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                {{ __('Clear Chat') }}
            </button>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50/30" x-ref="chatContainer">
        <div class="max-w-3xl mx-auto space-y-6">
            
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div class="flex gap-3 max-w-[85%]" :class="msg.role === 'user' ? 'flex-row-reverse' : ''">
                        
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-1 shadow-sm border"
                             :class="msg.role === 'user' ? 'bg-gray-100 border-gray-200 text-gray-600' : 'bg-gradient-to-br from-rj-purple to-rj-pink border-transparent text-white'">
                            <template x-if="msg.role === 'user'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </template>
                            <template x-if="msg.role === 'assistant'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </template>
                        </div>

                        <div class="px-5 py-3.5 rounded-2xl text-[15px] leading-relaxed shadow-sm border"
                             :class="msg.role === 'user' 
                                ? 'bg-white border-gray-200 text-gray-800 rounded-tr-sm' 
                                : 'bg-white border-rj-purple/20 text-gray-800 rounded-tl-sm'">
                            <div x-html="formatMessage(msg.text)"></div>
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="messages.length === 1">
                <div class="mt-8 animate-fade-in">
                    <h3 class="text-center text-2xl font-bold text-gray-900 mb-2">{{ __('How can I help you today?') }}</h3>
                    <p class="text-center text-sm text-gray-500 mb-8">{{ __('Ask me anything about managing your site or check your stats.') }}</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-w-2xl mx-auto">
                        <button @click="askQuick('Give me a summary of my site statistics.')" class="flex flex-col items-start p-4 bg-white rounded-xl border border-gray-200 hover:border-rj-purple/50 hover:shadow-md transition text-left group">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <span class="text-sm font-bold text-gray-900 group-hover:text-rj-purple transition-colors">{{ __('Site Statistics') }}</span>
                            <span class="text-xs text-gray-500 mt-1">{{ __('Get a summary of posts, pages, and users.') }}</span>
                        </button>
                        
                        <button @click="askQuick('How do I create a new post and add it to a menu?')" class="flex flex-col items-start p-4 bg-white rounded-xl border border-gray-200 hover:border-rj-purple/50 hover:shadow-md transition text-left group">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <span class="text-sm font-bold text-gray-900 group-hover:text-rj-purple transition-colors">{{ __('Create Content') }}</span>
                            <span class="text-xs text-gray-500 mt-1">{{ __('Learn how to write posts and manage menus.') }}</span>
                        </button>
                    </div>
                </div>
            </template>

            <div x-show="loading" class="flex justify-start">
                <div class="flex gap-3 max-w-[85%]">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-rj-purple to-rj-pink text-white flex items-center justify-center flex-shrink-0 mt-1 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div class="bg-white border border-rj-purple/20 px-5 py-4 rounded-2xl rounded-tl-sm shadow-sm flex items-center gap-1.5 h-[52px]">
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.15s"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border-t border-gray-100 shrink-0">
        <div class="max-w-3xl mx-auto">
            <form @submit.prevent="sendMessage()" class="relative flex items-center gap-2">
                <input type="text" x-model="input" :disabled="loading" 
                       placeholder="{{ __('Message RJ AI Assistant...') }}" 
                       class="w-full pl-5 pr-14 py-4 rounded-2xl border border-gray-300 bg-gray-50 focus:bg-white focus:border-rj-purple focus:ring-4 focus:ring-rj-purple/10 text-[15px] transition-all shadow-sm">
                <button type="submit" :disabled="!input.trim() || loading" 
                        class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-xl bg-gradient-to-r from-rj-purple to-rj-pink text-white flex items-center justify-center hover:opacity-90 transition disabled:opacity-50 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
            <div class="text-center mt-2">
                <span class="text-[11px] text-gray-400">{{ __('RJ AI can make mistakes. Verify important information.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function aiChat(stats) {
    return {
        stats: stats,
        defaultMessage: 'Salam! Mən RJ AI köməkçisiyəm. Saytınızın statistikası, yazılar, səhifələr və ya CMS-in necə işləməsi haqqında mənə istənilən sualı verə bilərsiniz.',
        messages: [],
        input: '',
        loading: false,

        init() {
            this.messages.push({ role: 'assistant', text: this.defaultMessage });
        },

        clearChat() {
            this.messages = [{ role: 'assistant', text: this.defaultMessage }];
        },

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
                    this.messages.push({ role: 'assistant', text: data.answer || data.message || 'Xəta baş verdi.' });
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
            let formatted = text.replace(/\n/g, '<br>');
            
            if (formatted.includes('statistika') || formatted.includes('statistics') || formatted.includes('posts')) {
                formatted += `<br><br><div class="flex flex-wrap gap-2 mt-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-medium border border-indigo-100">📝 ${this.stats.posts} Posts</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-100">📄 ${this.stats.pages} Pages</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-medium border border-blue-100">👥 ${this.stats.users} Users</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 text-xs font-medium border border-amber-100">💬 ${this.stats.comments} Comments</span>
                </div>`;
            }

            return formatted;
        }
    }
}
</script>
@endpush