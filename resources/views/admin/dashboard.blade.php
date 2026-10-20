@extends('admin.layouts.app')

@section('title', __('RJ AI Assistant'))

@section('content')
<style>
    [x-cloak] { display: none !important; }

    @keyframes rjFadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes rjMsgIn {
        from { opacity: 0; transform: translateY(8px) scale(.98); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes rjGradientFlow {
        0%   { background-position: 0% 50%; }
        100% { background-position: 200% 50%; }
    }
    @keyframes rjShimmer {
        0%   { background-position: -400px 0; }
        100% { background-position: 400px 0; }
    }
    @keyframes rjGlow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, .35); }
        50%      { box-shadow: 0 0 0 12px rgba(34, 197, 94, 0); }
    }
    @keyframes rjSpin {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }
    @keyframes rjCaret {
        0%, 100% { opacity: 1; }
        50%      { opacity: 0; }
    }

    .rj-fade-up { animation: rjFadeUp .6s cubic-bezier(.22,1,.36,1) both; }
    .rj-msg-in  { animation: rjMsgIn .35s cubic-bezier(.22,1,.36,1) both; }
    .rj-glow    { animation: rjGlow 2.4s ease-in-out infinite; }
    .rj-spin-slow { animation: rjSpin 6s linear infinite; transform-origin: center; }

    /* Tailwind-dən asılı olmayan gradient fon + ağ ikon */
    .rj-grad {
        background: linear-gradient(135deg, #16a34a 0%, #22c55e 50%, #34d399 100%);
        color: #fff;
    }
    .rj-grad svg { color: #fff; stroke: #fff; }

    .rj-gradient-text {
        background: linear-gradient(90deg, #16a34a, #22c55e, #3b82f6, #16a34a);
        background-size: 200% auto;
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: rjFadeUp .6s cubic-bezier(.22,1,.36,1) both, rjGradientFlow 5s linear infinite;
    }

    .rj-shimmer-line {
        height: 10px;
        border-radius: 999px;
        background: linear-gradient(90deg, #f3f4f6 0%, #bbf7d0 20%, #dcfce7 40%, #f3f4f6 60%);
        background-size: 800px 100%;
        animation: rjShimmer 1.4s linear infinite;
    }

    .rj-caret::after {
        content: '';
        display: inline-block;
        width: 2px;
        height: 1em;
        margin-left: 2px;
        vertical-align: -2px;
        background: #22c55e;
        animation: rjCaret .8s step-end infinite;
    }

    .rj-card { transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease; }
    .rj-card:hover { transform: translateY(-3px); border-color: #4ade80; box-shadow: 0 10px 25px -8px rgba(34,197,94,.3); }
    .rj-card:hover .rj-title { color: #16a34a; }

    .rj-ai-bubble { border-color: #bbf7d0; }

    .rj-input:focus {
        outline: none;
        border-color: #22c55e;
        box-shadow: 0 0 0 4px rgba(34,197,94,.12);
        background: #fff;
    }
</style>

<script>
    window.aiChat = function (stats) {
        return {
            stats: stats,
            messages: [],
            input: '',
            loading: false,
            typing: false,

            get busy() {
                return this.loading || this.typing;
            },

            sleep(ms) {
                return new Promise(r => setTimeout(r, ms));
            },

            clearChat() {
                if (this.busy) return;
                this.messages = [];
            },

            async typeAnswer(html) {
                this.typing = true;
                this.messages.push({ role: 'assistant', text: '', typing: true });
                const msg = this.messages[this.messages.length - 1];

                let i = 0;
                while (i < html.length) {
                    if (html[i] === '<') {
                        const end = html.indexOf('>', i);
                        i = end === -1 ? html.length : end + 1;
                    } else {
                        i++;
                    }
                    msg.text = html.slice(0, i);
                    this.scrollToBottom();
                    if (html[i - 1] !== '>') await this.sleep(14);
                }

                msg.text = html;
                msg.typing = false;
                this.typing = false;
                this.scrollToBottom();
            },

            localAnswer(lower) {
                const s = this.stats;

                if (lower.includes('post') || lower.includes('yazı')) {
                    return `Hazırda saytınızda <strong>${s.posts}</strong> ədəd yazı (post) var.`;
                }
                if (lower.includes('page') || lower.includes('səhifə')) {
                    return `Saytınızda <strong>${s.pages}</strong> ədəd səhifə (page) mövcuddur.`;
                }
                if (lower.includes('user') || lower.includes('istifadəçi')) {
                    return `Sistemdə <strong>${s.users}</strong> istifadəçi qeydiyyatdan keçib.`;
                }
                if (lower.includes('comment') || lower.includes('şərh')) {
                    return `Saytınızda ümumilikdə <strong>${s.comments}</strong> ədəd şərh var.`;
                }
                if (lower.includes('media') || lower.includes('fayl')) {
                    return `Media kitabxanasında <strong>${s.media}</strong> ədəd fayl var.`;
                }
                if (lower.includes('overview') || lower.includes('ümumi')) {
                    return `Saytın ümumi vəziyyəti:<br><br>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-medium border border-indigo-100">📝 ${s.posts} Posts</span>
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-100">📄 ${s.pages} Pages</span>
                            <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-medium border border-blue-100">👥 ${s.users} Users</span>
                            <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 text-xs font-medium border border-amber-100">💬 ${s.comments} Comments</span>
                            <span class="px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 text-xs font-medium border border-purple-100">🖼️ ${s.media} Media</span>
                        </div>`;
                }
                return null;
            },

            async sendMessage() {
                const text = this.input.trim();
                if (!text || this.busy) return;

                this.messages.push({ role: 'user', text: this.escapeHtml(text) });
                this.input = '';
                this.loading = true;
                this.scrollToBottom();

                const lower = text.toLowerCase();
                let answer = this.localAnswer(lower);

                if (answer !== null) {
                    await this.sleep(900);
                } else {
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
                        answer = data.success
                            ? data.answer
                            : (data.answer || data.message || 'Xəta baş verdi.');
                    } catch (e) {
                        answer = 'Şəbəkə xətası. İnternet bağlantınızı yoxlayın.';
                    }
                }

                this.loading = false;
                await this.typeAnswer(answer);
            },

            askQuick(text) {
                if (this.busy) return;
                this.input = text;
                this.sendMessage();
            },

            escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const el = this.$refs.chatContainer;
                    if (el) el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' });
                });
            }
        };
    };
</script>

<div class="h-[calc(100vh-8rem)] bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col"
     x-data="aiChat({
         posts: {{ $stats['posts'] ?? 0 }},
         pages: {{ $stats['pages'] ?? 0 }},
         users: {{ $stats['users'] ?? 0 }},
         comments: {{ $stats['comments'] ?? 0 }},
         media: {{ $stats['media'] ?? 0 }}
     })">

    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl rj-grad flex items-center justify-center shadow-sm rj-glow">
                <svg class="w-5 h-5" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ __('RJ AI Assistant') }}</h2>
                <p class="text-xs text-gray-500 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    {{ __('Online & Ready to help') }}
                </p>
            </div>
        </div>
        <button type="button" x-cloak x-show="messages.length > 0" x-transition.opacity @click="clearChat()" class="text-sm font-medium text-gray-500 hover:text-red-600 transition flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-red-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
            {{ __('Clear Chat') }}
        </button>
    </div>

    {{-- Chat area --}}
    <div class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50/30 scroll-smooth" x-ref="chatContainer">
        <div class="max-w-3xl mx-auto space-y-6 pb-4">

            {{-- Welcome screen --}}
            <div x-show="messages.length === 0" class="flex flex-col items-center justify-center min-h-[50vh] text-center">
                <div class="w-16 h-16 rounded-2xl rj-grad flex items-center justify-center shadow-lg mb-6 rj-fade-up rj-glow">
                    <svg class="w-8 h-8" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </div>

                <h3 class="text-3xl font-bold mb-2 rj-gradient-text" style="animation-delay:.1s, 0s">{{ __('How can I help you today?') }}</h3>
                <p class="text-sm text-gray-500 mb-8 max-w-md rj-fade-up" style="animation-delay:.2s">{{ __('Ask me anything about managing your site or click one of the suggestions below to get instant stats.') }}</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 w-full max-w-2xl text-left">
                    <button type="button" @click="askQuick('How many posts do I have?')" style="animation-delay:.30s" class="rj-fade-up rj-card group flex flex-col p-4 bg-white rounded-xl border border-gray-200">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z"/></svg>
                        </div>
                        <span class="rj-title text-sm font-bold text-gray-900">{{ __('How many posts do I have?') }}</span>
                    </button>

                    <button type="button" @click="askQuick('How many pages do I have?')" style="animation-delay:.38s" class="rj-fade-up rj-card group flex flex-col p-4 bg-white rounded-xl border border-gray-200">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <span class="rj-title text-sm font-bold text-gray-900">{{ __('How many pages do I have?') }}</span>
                    </button>

                    <button type="button" @click="askQuick('How many users are there?')" style="animation-delay:.46s" class="rj-fade-up rj-card group flex flex-col p-4 bg-white rounded-xl border border-gray-200">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="rj-title text-sm font-bold text-gray-900">{{ __('How many users are there?') }}</span>
                    </button>

                    <button type="button" @click="askQuick('How many comments do I have?')" style="animation-delay:.54s" class="rj-fade-up rj-card group flex flex-col p-4 bg-white rounded-xl border border-gray-200">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <span class="rj-title text-sm font-bold text-gray-900">{{ __('How many comments?') }}</span>
                    </button>

                    <button type="button" @click="askQuick('How many media files do I have?')" style="animation-delay:.62s" class="rj-fade-up rj-card group flex flex-col p-4 bg-white rounded-xl border border-gray-200">
                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="rj-title text-sm font-bold text-gray-900">{{ __('How many media files?') }}</span>
                    </button>

                    <button type="button" @click="askQuick('Give me an overview of my site.')" style="animation-delay:.70s" class="rj-fade-up rj-card group flex flex-col p-4 bg-white rounded-xl border border-gray-200">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <span class="rj-title text-sm font-bold text-gray-900">{{ __('Site Overview') }}</span>
                    </button>
                </div>
            </div>

            {{-- Messages --}}
            <template x-for="(msg, index) in messages" :key="index">
                <div class="rj-msg-in" :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div class="flex gap-3 max-w-[85%]" :class="msg.role === 'user' ? 'flex-row-reverse' : ''">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-1 shadow-sm border"
                             :class="msg.role === 'user' ? 'bg-gray-100 border-gray-200 text-gray-600' : 'rj-grad border-transparent'">
                            <template x-if="msg.role === 'user'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </template>
                            <template x-if="msg.role === 'assistant'">
                                <svg class="w-4 h-4" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            </template>
                        </div>
                        <div class="px-5 py-3.5 rounded-2xl text-[15px] leading-relaxed shadow-sm border"
                             :class="msg.role === 'user'
                                ? 'bg-white border-gray-200 text-gray-800 rounded-tr-sm'
                                : 'bg-white rj-ai-bubble text-gray-800 rounded-tl-sm'">
                            <div :class="msg.typing ? 'rj-caret' : ''" x-html="msg.text"></div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Thinking (shimmer) --}}
            <div x-cloak x-show="loading" x-transition.opacity class="flex justify-start rj-msg-in">
                <div class="flex gap-3 w-full max-w-[85%]">
                    <div class="w-8 h-8 rounded-full rj-grad flex items-center justify-center flex-shrink-0 mt-1 shadow-sm rj-glow">
                        <svg class="w-4 h-4 rj-spin-slow" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </div>
                    <div class="flex-1 space-y-2.5 pt-2.5">
                        <div class="rj-shimmer-line" style="width:92%"></div>
                        <div class="rj-shimmer-line" style="width:78%; animation-delay:.15s"></div>
                        <div class="rj-shimmer-line" style="width:55%; animation-delay:.3s"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Input --}}
    <div class="p-4 bg-white border-t border-gray-100 shrink-0">
        <div class="max-w-3xl mx-auto">
            <form @submit.prevent="sendMessage()" class="relative flex items-center gap-2">
                <input type="text" x-model="input" :disabled="busy"
                       placeholder="{{ __('Message RJ AI Assistant...') }}"
                       class="rj-input w-full pl-5 pr-14 py-4 rounded-2xl border border-gray-300 bg-gray-50 text-[15px] transition-all shadow-sm">
                <button type="submit" :disabled="!input.trim() || busy"
                        class="rj-grad absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-xl flex items-center justify-center hover:opacity-90 hover:scale-105 active:scale-95 transition disabled:opacity-50 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
            <div class="text-center mt-2">
                <span class="text-[11px] text-gray-400">{{ __('RJ AI can make mistakes. Verify important information.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection