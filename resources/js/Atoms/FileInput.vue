<script setup>
import toast from '@/Stores/toast';
import {ref, defineProps, defineEmits} from 'vue';

// Define props
const {
    placeholder = '',
    label = '',
    error = '',
    helper = '',
    modelValue,
    is_required = false,
    inputName,
    inputId
} = defineProps([
    'placeholder',
    'label',
    'error',
    'helper',
    'modelValue',
    'is_required',
    'inputName',
    'inputId'
]);

// Define emits for custom events
const emits = defineEmits(['input', 'focus', 'blur', 'update:modelValue']);

const input = ref(null);

const upload = ref(false);

const handleUpload = (value) => {
    toast.add({
        message: `File uploaded successfully, but it's not saved yet!`,
        duration: 4000,
        type: 'warning'
    });
    upload.value = true;

    emits('update:modelValue', value)

};

</script>
<template>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" v-if="label">{{ label }} <span
            class="text-sm text-red-600" v-if="is_required">*</span></label>
        <div class="my-4">
            <label for="test" v-if="!upload"
                   class="py-3 px-6  rounded-lg  border border-gray-300 hover:bg-orange-500 hover:cursor-pointer hover:text-white hover:border-transparent transition">
                <span>Upload document</span>
            </label>
            <label for="test" v-else
                   class="py-3 px-6  rounded-lg  bg-green-500 text-white hover:cursor-pointer transition">
                <span>Document uploaded</span>
            </label>
            <input
                type="file"
                id="test"
                name="test"
                :value="modelValue"
                class="form-control userFiles hidden"
                @change="handleUpload($event.target.value)"
            />
        </div>

        <!--        ${val != null ? `<a class="btn btn-outline-danger ms-2 removeFileBtn" data-is_required="${el.is_required}"-->
        <!--                            data-field_id="${el.field_id}" data-field_name="${el.field_name}"-->
        <!--                            data-form_label="${el.title}"> <i class="ti ti-trash"></i> </a>` : ''}`;-->


        <p class="mt-2 text-sm text-gray-500" v-if="helper">{{ helper }}</p>
        <p class="mt-2 text-sm text-red-500" v-if="error">{{ error }}</p>
    </div>
</template>

<style scoped>


</style>
