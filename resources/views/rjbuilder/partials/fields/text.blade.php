<template x-if="field.type === 'text' || field.type === 'url' || field.type === 'email'">
    <div>
        <template x-if="isDynamicValue(fieldValue(field))">
            <div class="rj-dyn-chip">
                <span class="rj-dyn-chip-icon">⚡</span>
                <span class="rj-dyn-chip-label" x-text="getDynamicTagLabel(fieldValue(field))"></span>
                <button type="button" class="rj-dyn-chip-btn" @click="openTagPicker(field)" :title="t('Change tag')">✎</button>
                <button type="button" class="rj-dyn-chip-btn rj-dyn-chip-x" @click="clearTag(field.name)" :title="t('Remove tag')">✕</button>
            </div>
        </template>

        <template x-if="!isDynamicValue(fieldValue(field))">
            <div style="display:flex; align-items:center; gap:6px;">
                <input type="text" class="rj-in" :value="fieldValue(field)" :placeholder="field.placeholder || ''" @input="updateSetting(field.name, $event.target.value)">
                <button type="button"
                        x-show="dynamicTagList.length > 0"
                        @click="openTagPicker(field)"
                        class="rj-dyn-btn"
                        :title="t('Insert Dynamic Tag')">⚡</button>
            </div>
        </template>
    </div>
</template>