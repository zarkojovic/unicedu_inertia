<script setup>

import {computed, onMounted} from "vue";
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    fieldInfo: {
        type: Object
    }
});

const page = usePage();


const getDisplayValue = computed(() => {
    if (props.fieldInfo.file_name !== '') {
        return props.fieldInfo.file_name;
    } else if (props.fieldInfo.display_value !== '') {
        return props.fieldInfo.display_value;
    } else if (props.fieldInfo.value !== '') {
        return props.fieldInfo.value;
    } else return null;
});

const filePath = computed(() => {
    return page.props.documents_root + props.fieldInfo.file_path;
});



</script>

<template>
    <div class="mt-4">
        <p class="font-medium text-slate-900 text-md">{{ props.fieldInfo.title }} <span v-if="!!props.fieldInfo.is_required"
                                                                       class="italic text-gray-400 text-sm font-thin">(required)</span>
        </p>
        <span v-if="getDisplayValue === null" class="italic text-gray-400 text-sm mt-3">empty</span>
        <span v-if="props.fieldInfo.file_name !== ''" class="text-orange-400 text-sm mt-3"><a target="_blank"
            :href="filePath">{{ getDisplayValue }}</a></span>
        <span v-else class=" text-slate-600 text-sm mt-3">{{ getDisplayValue }}</span>
    </div>
</template>
