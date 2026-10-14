<template x-if="field.type === 'font'">
    <div>
        <template x-if="globalFontList.length > 0">
            <div style="margin-bottom:8px;">
                <div style="font-size:10px; font-weight:600; color:#777a88; text-transform:uppercase; letter-spacing:.05em; margin-bottom:4px;" x-text="t('Global Fonts')"></div>
                <div class="rj-gfp">
                    <template x-for="gf in globalFontList" :key="gf.id">
                        <button type="button"
                                class="rj-gfp-btn"
                                :data-on="String(fieldValue(field)) === gf.family"
                                :style="'font-family:' + gf.family"
                                :title="gf.name"
                                @click="updateSetting(field.name, gf.family)">
                            <span x-text="gf.name"></span>
                        </button>
                    </template>
                </div>
            </div>
        </template>

        <input type="text"
               class="rj-in"
               :value="fieldValue(field)"
               :placeholder="field.placeholder || 'Inter, sans-serif'"
               @input="updateSetting(field.name, $event.target.value)">
    </div>
</template>