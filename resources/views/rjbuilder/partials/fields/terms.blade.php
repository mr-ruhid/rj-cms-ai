<template x-if="field.type === 'terms'">
    <div>
        <template x-if="taxonomyList.length === 0">
            <p class="rj-help" style="text-align:center; padding:12px; background:rgba(245,158,11,.08); border:1px dashed rgba(245,158,11,.3); border-radius:6px; color:#fbbf24;">
                <span x-text="t('No taxonomies yet.')"></span>
                <a href="{{ route('admin.taxonomies.index') }}" target="_blank" style="color:#a78bfa; text-decoration:underline; display:block; margin-top:4px;" x-text="t('Create one →')"></a>
            </p>
        </template>

        <template x-if="taxonomyList.length > 0">
            <div>
                <div class="rj-terms-pick">
                    <div class="rj-terms-head">
                        <select class="rj-in" style="margin-bottom:8px;" @change="setTermsTaxonomy(field.name, $event.target.value)">
                            <template x-for="tax in taxonomyList" :key="tax.slug">
                                <option :value="tax.slug" :selected="getTermsTaxonomy(field.name) === tax.slug" x-text="tax.label"></option>
                            </template>
                        </select>

                        <input type="text"
                               class="rj-in"
                               :placeholder="t('Search terms...')"
                               x-model="termSearch[field.name]">
                    </div>

                    <div class="rj-terms-list">
                        <template x-for="term in filteredTermsFor(field.name)" :key="term.id">
                            <label class="rj-term-row">
                                <input type="checkbox"
                                       :checked="isTermSelected(field.name, term.id)"
                                       @change="toggleTerm(field.name, term.id)">
                                <span x-text="term.name"></span>
                                <span class="rj-term-count" x-show="term.post_count !== undefined" x-text="term.post_count"></span>
                            </label>
                        </template>

                        <div x-show="filteredTermsFor(field.name).length === 0" class="rj-help" style="text-align:center; padding:14px; color:#777a88; font-size:12px;">
                            <span x-text="t('No terms found.')"></span>
                        </div>
                    </div>

                    <div class="rj-terms-foot">
                        <span x-text="t('Selected: :n').replace(':n', selectedTermCount(field.name))"></span>
                        <button type="button" class="rj-terms-clear" @click="clearTerms(field.name)" x-show="selectedTermCount(field.name) > 0">
                            <span x-text="t('Clear selection')"></span>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>