<template x-if="ai.panelOpen">
    <div class="rj-modal rj-ai-modal" @keydown.escape.window="closeAiPanel()">
        <div class="bg" @click="closeAiPanel()"></div>
        <div class="box" style="max-width:760px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
                <div>
                    <h2 style="font-size:18px; font-weight:600; margin:0 0 4px; display:flex; align-items:center; gap:8px;">
                        <span style="background:linear-gradient(135deg, #7c3aed, #ec4899); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; font-weight:800;">AI</span>
                        <span x-text="t('Assistant')"></span>
                    </h2>
                    <p style="font-size:12px; color:#6b7280; margin:0;">
                        <span x-text="ai.model"></span>
                    </p>
                </div>
                <button type="button" @click="closeAiPanel()" style="background:transparent; border:0; color:#6b7280; font-size:20px; cursor:pointer; line-height:1;">✕</button>
            </div>

            <div x-show="!ai.enabled" class="rj-hint" style="text-align:left; border-style:solid; background:rgba(245,158,11,.08); border-color:rgba(245,158,11,.35); color:#fbbf24; margin-bottom:16px;">
                {{ __('AI is not configured. Add your OpenRouter API key in Settings → AI.') }}
            </div>

            <div class="rj-tabs" style="margin:-20px -32px 20px -32px;">
                <button type="button" class="rj-tab" :data-active="ai.tab === 'generate'" @click="ai.tab = 'generate'" x-text="t('Generate')"></button>
                <button type="button" class="rj-tab" :data-active="ai.tab === 'improve'" @click="ai.tab = 'improve'" x-text="t('Improve')"></button>
                <button type="button" class="rj-tab" :data-active="ai.tab === 'translate'" @click="ai.tab = 'translate'" x-text="t('Translate')"></button>
                <button type="button" class="rj-tab" :data-active="ai.tab === 'suggest'" @click="ai.tab = 'suggest'" x-text="t('Suggest Section')"></button>
            </div>

            <div x-show="ai.tab === 'generate'" style="display:flex; flex-direction:column; gap:14px;">
                <div>
                    <label class="rj-lbl">{{ __('What should AI write?') }}</label>
                    <textarea class="rj-ta" rows="3" x-model="ai.generate.prompt" placeholder="{{ __('e.g. A short paragraph about our company values') }}"></textarea>
                </div>

                <div>
                    <label class="rj-lbl">{{ __('Extra context (optional)') }}</label>
                    <input type="text" class="rj-in" x-model="ai.generate.context" placeholder="{{ __('e.g. B2B SaaS company for designers') }}">
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div>
                        <label class="rj-lbl">{{ __('Tone') }}</label>
                        <select class="rj-in" x-model="ai.generate.tone">
                            <option value="professional">{{ __('Professional') }}</option>
                            <option value="friendly">{{ __('Friendly') }}</option>
                            <option value="casual">{{ __('Casual') }}</option>
                            <option value="formal">{{ __('Formal') }}</option>
                            <option value="playful">{{ __('Playful') }}</option>
                            <option value="luxury">{{ __('Luxury') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="rj-lbl">{{ __('Length') }}</label>
                        <select class="rj-in" x-model="ai.generate.length">
                            <option value="short">{{ __('Short') }}</option>
                            <option value="medium">{{ __('Medium') }}</option>
                            <option value="long">{{ __('Long') }}</option>
                        </select>
                    </div>
                </div>

                <button type="button" class="rj-cta" style="width:100%; justify-content:center;" :disabled="ai.busy" @click="aiGenerateText()">
                    <span x-text="ai.busy ? t('Generating...') : t('Generate Text')"></span>
                </button>

                <div x-show="ai.generate.result" class="rj-ai-result">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                        <span style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#8b5cf6;">{{ __('Result') }}</span>
                        <button type="button" class="rj-btn" style="height:26px; font-size:12px; padding:0 10px;" @click="copyToClipboard(ai.generate.result)" x-text="t('Copy')"></button>
                    </div>
                    <div class="rj-ai-result-text" x-text="ai.generate.result"></div>
                </div>
            </div>

            <div x-show="ai.tab === 'improve'" style="display:flex; flex-direction:column; gap:14px;">
                <div class="rj-hint" style="text-align:left;">
                    {{ __('Select a heading or text block first, then choose an action.') }}
                </div>

                <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:8px;">
                    <button type="button" class="rj-ai-action" @click="aiImproveText('improve')" :disabled="ai.busy">
                        <span class="rj-ai-action-icon">✨</span>
                        <span>{{ __('Improve') }}</span>
                    </button>
                    <button type="button" class="rj-ai-action" @click="aiImproveText('shorten')" :disabled="ai.busy">
                        <span class="rj-ai-action-icon">✂️</span>
                        <span>{{ __('Shorten') }}</span>
                    </button>
                    <button type="button" class="rj-ai-action" @click="aiImproveText('expand')" :disabled="ai.busy">
                        <span class="rj-ai-action-icon">↔️</span>
                        <span>{{ __('Expand') }}</span>
                    </button>
                    <button type="button" class="rj-ai-action" @click="aiImproveText('fix')" :disabled="ai.busy">
                        <span class="rj-ai-action-icon">🔧</span>
                        <span>{{ __('Fix Grammar') }}</span>
                    </button>
                    <button type="button" class="rj-ai-action" @click="aiImproveText('formal')" :disabled="ai.busy">
                        <span class="rj-ai-action-icon">👔</span>
                        <span>{{ __('More Formal') }}</span>
                    </button>
                    <button type="button" class="rj-ai-action" @click="aiImproveText('casual')" :disabled="ai.busy">
                        <span class="rj-ai-action-icon">😊</span>
                        <span>{{ __('More Casual') }}</span>
                    </button>
                </div>
            </div>

            <div x-show="ai.tab === 'translate'" style="display:flex; flex-direction:column; gap:14px;">
                <div class="rj-hint" style="text-align:left;">
                    {{ __('Select a heading or text block, then choose the target language.') }}
                </div>

                <div>
                    <label class="rj-lbl">{{ __('Translate to') }}</label>
                    <select class="rj-in" x-model="ai.translate.targetLocale">
                        <option value="">{{ __('— Select language —') }}</option>
                        <template x-for="loc in locales" :key="loc">
                            <option :value="loc" x-text="loc"></option>
                        </template>
                    </select>
                </div>

                <button type="button" class="rj-cta" style="width:100%; justify-content:center;" :disabled="ai.busy || !ai.translate.targetLocale" @click="aiTranslateText()">
                    <span x-text="ai.busy ? t('Translating...') : t('Translate')"></span>
                </button>
            </div>

            <div x-show="ai.tab === 'suggest'" style="display:flex; flex-direction:column; gap:14px;">
                <div>
                    <label class="rj-lbl">{{ __('Describe the section you want') }}</label>
                    <textarea class="rj-ta" rows="3" x-model="ai.suggest.description" placeholder="{{ __('e.g. A hero section with a big headline and a call to action button') }}"></textarea>
                </div>

                <button type="button" class="rj-cta" style="width:100%; justify-content:center;" :disabled="ai.busy" @click="aiSuggestSection()">
                    <span x-text="ai.busy ? t('Generating...') : t('Suggest Section')"></span>
                </button>

                <div x-show="ai.suggest.result.length > 0">
                    <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#8b5cf6; margin-bottom:8px;">{{ __('Suggested blocks') }}</div>
                    <div class="rj-ai-result">
                        <div class="rj-ai-result-text" x-text="JSON.stringify(ai.suggest.result, null, 2).slice(0, 500) + '...'"></div>
                    </div>
                    <button type="button" class="rj-cta" style="width:100%; justify-content:center; margin-top:10px;" @click="aiInsertSection(ai.suggest.result)">
                        <span>{{ __('Insert into page') }}</span>
                    </button>
                </div>
            </div>

            <div style="margin-top:20px; padding-top:16px; border-top:1px solid #e5e7eb; text-align:right;">
                <button type="button" @click="closeAiPanel()" style="padding:8px 16px; background:transparent; border:1px solid #d1d5db; border-radius:8px; color:#374151; font-size:13px; cursor:pointer;" x-text="t('Close')"></button>
            </div>
        </div>
    </div>
</template>