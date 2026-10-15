<template x-if="field.type === 'post_type'">
    <div>
        <template x-if="postTypeList.length === 0">
            <input type="text" class="rj-in" :value="fieldValue(field)" :placeholder="field.placeholder || 'post'" @input="updateSetting(field.name, $event.target.value)">
        </template>

        <template x-if="postTypeList.length > 0">
            <select class="rj-in" @change="updateSetting(field.name, $event.target.value)">
                <template x-for="pt in postTypeList" :key="pt.slug">
                    <option :value="pt.slug" :selected="String(fieldValue(field)) === String(pt.slug)" x-text="pt.label"></option>
                </template>
            </select>
        </template>
    </div>
</template>