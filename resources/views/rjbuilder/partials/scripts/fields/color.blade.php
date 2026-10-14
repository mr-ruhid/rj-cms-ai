<template x-if="field.type === 'color'">
    <div>
        <div style="display:flex; align-items:center; gap:8px;">
            <input type="color"
                   :value="colorHex(fieldValue(field))"
                   @input="updateSetting(field.name, $event.target.value)"
                   style="width:40px; height:36px; border:1px solid var(--line); border-radius:6px; background:#15161a; cursor:pointer; padding:2px;">
            <input type="text"
                   class="rj-in"
                   style="font-family:monospace; font-size:12px;"
                   :value="fieldValue(field)"
                   @input="updateSetting(field.name, $event.target.value)">
        </div>

        <div x-show="globalColorList.length > 0" style="margin-top:8px;">
            <div style="font-size:10px; font-weight:600; color:#777a88; text-transform:uppercase; letter-spacing:.05em; margin-bottom:4px;" x-text="t('Global Colors')"></div>
            <div class="rj-gcp">
                <template x-for="gc in globalColorList" :key="gc.id">
                    <button type="button"
                            class="rj-gcp-sw"
                            :style="'background:' + gc.color"
                            :title="gc.name"
                            @click="updateSetting(field.name, gc.color)"></button>
                </template>
            </div>
        </div>
    </div>
</template>