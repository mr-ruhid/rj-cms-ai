<template x-if="field.type === 'dimensions'">
    <div>
        <div class="rj-lbl">
            <span x-text="field.label"></span>
            <button type="button" class="rj-reset"
                    x-show="!isDefault(field)"
                    @click="resetField(field)"
                    :title="t('Reset to default')">↺</button>
        </div>

        <div class="rj-dim">
            <div class="rj-dim-grid">
                <div class="rj-dim-cell">
                    <span x-text="t('Top')"></span>
                    <input type="number" :value="dimGet(field).top" @input="dimSet(field, { top: $event.target.value })">
                </div>
                <div class="rj-dim-cell">
                    <span x-text="t('Right')"></span>
                    <input type="number" :value="dimGet(field).right" @input="dimSet(field, { right: $event.target.value })">
                </div>
                <div class="rj-dim-cell">
                    <span x-text="t('Bottom')"></span>
                    <input type="number" :value="dimGet(field).bottom" @input="dimSet(field, { bottom: $event.target.value })">
                </div>
                <div class="rj-dim-cell">
                    <span x-text="t('Left')"></span>
                    <input type="number" :value="dimGet(field).left" @input="dimSet(field, { left: $event.target.value })">
                </div>
            </div>

            <div class="rj-dim-foot">
                <button type="button" class="rj-dim-link"
                        :data-on="dimGet(field).linked"
                        @click="dimToggleLink(field)"
                        :title="t('Link values together')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                </button>
                <select class="rj-dim-unit"
                        :value="dimGet(field).unit"
                        @change="dimSet(field, { unit: $event.target.value })">
                    <option value="px">px</option>
                    <option value="%">%</option>
                    <option value="em">em</option>
                    <option value="rem">rem</option>
                    <option value="vh">vh</option>
                    <option value="vw">vw</option>
                </select>
            </div>
        </div>

        <p class="rj-help" x-show="field.help" x-text="field.help"></p>
    </div>
</template>