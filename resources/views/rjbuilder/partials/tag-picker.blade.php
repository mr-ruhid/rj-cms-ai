<template x-if="dynamicTagPicker.open">
    <div class="rj-modal" @keydown.escape.window="closeTagPicker()">
        <div class="bg" @click="closeTagPicker()"></div>
        <div class="box" style="max-width:640px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
                <div>
                    <h2 style="font-size:18px; font-weight:600; margin:0 0 4px;" x-text="t('Dynamic Tags')"></h2>
                    <p style="font-size:13px; color:#6b7280; margin:0;" x-text="t('Choose a dynamic value to bind to this field.')"></p>
                </div>
                <button type="button" @click="closeTagPicker()" style="background:transparent; border:0; color:#6b7280; font-size:20px; cursor:pointer; line-height:1;">✕</button>
            </div>

            <div style="max-height:420px; overflow-y:auto;">
                <template x-for="group in dynamicTagGrouped" :key="group.key">
                    <div style="margin-bottom:18px;">
                        <div style="font-size:11px; font-weight:600; color:#8b5cf6; text-transform:uppercase; letter-spacing:.05em; margin-bottom:8px;">
                            <span x-text="group.icon"></span>
                            <span x-text="group.label"></span>
                        </div>
                        <div style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:8px;">
                            <template x-for="tag in group.items" :key="tag.id">
                                <button type="button"
                                        @click="applyTag(tag)"
                                        style="text-align:left; padding:12px 14px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px; cursor:pointer; transition:all .12s;"
                                        onmouseover="this.style.borderColor='#8b5cf6'; this.style.background='#faf5ff';"
                                        onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb';">
                                    <div style="font-size:13px; font-weight:600; color:#111827; margin-bottom:2px;" x-text="tag.name"></div>
                                    <div style="font-size:11px; color:#6b7280; line-height:1.4;" x-text="tag.description"></div>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                <div x-show="dynamicTagGrouped.length === 0" style="text-align:center; padding:32px 16px; color:#9ca3af; font-size:13px;">
                    <span x-text="t('No dynamic tags available.')"></span>
                </div>
            </div>

            <div style="margin-top:20px; padding-top:16px; border-top:1px solid #e5e7eb; text-align:right;">
                <button type="button" @click="closeTagPicker()" style="padding:8px 16px; background:transparent; border:1px solid #d1d5db; border-radius:8px; color:#374151; font-size:13px; cursor:pointer;" x-text="t('Cancel')"></button>
            </div>
        </div>
    </div>
</template>