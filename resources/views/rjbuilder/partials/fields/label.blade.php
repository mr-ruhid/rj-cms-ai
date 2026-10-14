<template x-if="field.type !== 'toggle' && field.type !== 'dimensions'">
    <div class="rj-lbl">
        <span style="display:inline-flex; align-items:center; gap:6px; min-width:0;">
            <span x-text="field.label + (templateType === 'page' && locales.length > 1 && isTranslatableField(field.name) ? ' 🌐' : '')"></span>

            <button type="button"
                    x-show="isResponsiveField(field)"
                    @click="cycleFieldDevice(field.name)"
                    class="rj-fdev"
                    :data-on="getFieldDevice(field.name)"
                    :title="t('Responsive') + ' — ' + t(getFieldDevice(field.name) === 'desktop' ? 'Desktop' : getFieldDevice(field.name) === 'tablet' ? 'Tablet' : 'Mobile')">
                <span x-text="getFieldDevice(field.name) === 'desktop' ? 'D' : (getFieldDevice(field.name) === 'tablet' ? 'T' : 'M')"></span>
            </button>
        </span>

        <button type="button" class="rj-reset" x-show="!isDefault(field)" @click="resetField(field)" :title="t('Reset to default')">↺</button>
    </div>
</template>