<script setup>

import FileInput from '@/Atoms/FileInput.vue';
import GenericInput from '@/Atoms/GenericInput.vue';
import DropdownInput from '@/Atoms/DropdownInput.vue';

const props = defineProps({
    items: {
        type: Array,
    },
});

// onMounted(() => {
//     console.log(props.items);
// });

</script>

<template>
    <div v-for="(field,key) in props.items" :key="field.field_name">
        <FileInput v-if="field.type === 'file'"
                   :key="key"
                   :file-exists="field.file_name !== ''"
                   :input-id="field.field_name"
                   :input-name="field.field_name"
                   :is-category-field="true"
                   :label="field.custom_title ?? field.title"
                   :valid-types="['application/pdf']"
        />

        <DropdownInput
            v-else-if="field.type === 'enumeration'"
            :input-name="field.field_name"
            :is_required="!!field.is_required"
            :label="field.custom_title ?? field.title"
            :options="field.items"
            :selected-item="field.value"
        />

        <GenericInput v-else
                      v-model="field.value"
                      :input-name="field.field_name"
                      :is-category-field="true"
                      :is_required="!!field.is_required"
                      :label="field.custom_title ?? field.title"
                      :type="field.type === 'string' ? 'text' : field.type"
        />
    </div>
</template>

<style scoped>

</style>
