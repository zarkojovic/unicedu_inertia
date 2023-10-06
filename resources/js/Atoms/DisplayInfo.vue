<script setup>

import {computed, onMounted} from "vue";

const props = defineProps({
    fieldInfo: {
        type: Object
    }
});


const getDisplayValue = computed(() => {
    if (props.fieldInfo.file_name !== '') {
        return props.fieldInfo.file_name;
    } else if (props.fieldInfo.display_value !== '') {
        return props.fieldInfo.display_value;
    } else if (props.fieldInfo.value !== '') {
        return props.fieldInfo.value;
    } else return null;
});


</script>

<template>
    <div>
        <p class="font-bold text-sm">{{ props.fieldInfo.title }} <span v-if="!!props.fieldInfo.is_required"
                                                                class="italic text-gray-400 text-sm">(required)</span>
        </p>
        <span v-if="getDisplayValue === null" class="italic text-gray-400 text-sm">empty</span>
        <span v-else class="text-gray-400 text-sm">{{ props.fieldInfo.value }}</span>
    </div>
</template>
