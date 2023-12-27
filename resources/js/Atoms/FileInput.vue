<script setup>
import toast from '@/Stores/toast';
import {defineEmits, defineProps, inject, onMounted, ref} from 'vue';
import Button from '@/Atoms/Button.vue';

// Define props and their default values
const {
    label = '',
    error = '',
    helper = '',
    modelValue,
    is_required = false,
    inputName = 'test',
    inputId = 'test',
    validTypes = null,
    isCategoryField,
    fileExists = false,
} = defineProps([
    'label',
    'error',
    'helper',
    'modelValue',
    'is_required',
    'inputName',
    'inputId',
    'validTypes',
    'isCategoryField',
    'fileExists',
]);

onMounted(() => {
    if (fileExists) {
        isReplace.value = true;
    }
});

// Define emits for custom events
const emits = defineEmits(['update:modelValue']);

// Initialize refs for various values
const input = ref(null); // Input element reference
const upload = ref(false); // Whether a file is being uploaded
const type = ref(null); // MIME type of the uploaded file
const fileValue = ref(undefined); // The uploaded file itself
const isValidType = ref(true); // Whether the file type is valid
const isReplace = ref(false); // Whether to replace an existing document

const formItems = inject('formItems');

// Function to handle file upload
const handleUpload = (event) => {
    console.log(event.target.files[0]);
    if (event.target.files[0]) {
        fileValue.value = event.target.files[0];

        if (fileValue.value) {
            type.value = fileValue.value.type; // Get the MIME type
        }

        if (validTypes) {
            isValidType.value = validTypes.includes(type.value); // Check if the type is valid
        } else {
            isValidType.value = true;
        }

        if (isValidType.value) {
            if (isCategoryField) {
                formItems.value.formItems[inputName] = {
                    value: fileValue.value,
                    is_file: true,
                };
            }
            upload.value = true;

            toast.add({
                message: 'File uploaded but not saved yet!',
                duration: 4000,
                type: 'warning',
            });

            setTimeout(function() {
                upload.value = false;
                isHover.value = false;
                isReplace.value = true;
            }, 4000);
        } else {
            toast.add({
                message: `Invalid document type!`,
                duration: 4000,
                type: 'danger',
            });
            fileValue.value = null;
            isReplace.value = false;
        }
    }
};

const isHover = ref(false); // Whether the mouse is hovering

// Function to handle mouse hover
const handleHover = () => {
    isHover.value = true;
};

// Function to handle mouse move
const handleMove = () => {
    isHover.value = false;
};

const clearFile = function() {
    console.log(formItems.value.formItems);
    fileValue.value = null;
    isReplace.value = false;
    upload.value = false;
    isHover.value = false;
    if (fileExists) {
        formItems.value.formItems[inputName] = {
            value: null,
            file_path: null,
            file_name: null,
            is_file: true,
        };
    } else {
        delete formItems.value.formItems[inputName];
    }
};

</script>
<template>
    <div>
        <label v-if="label" class="text-slate-600 block text-sm font-medium dark:text-gray-300">{{ label }} <span
            v-if="is_required" class="text-sm text-red-600">*</span></label>
        <div class="mt-1 flex">
            <label v-if="!upload" :for="inputName"
                   class="py-3 px-6  rounded-lg  border border-gray-300 hover:bg-orange-500 hover:cursor-pointer hover:text-white hover:border-transparent transition"
                   @mouseenter="handleHover"
                   @mouseleave="handleMove">
                <div class="">
                    <span v-if="isReplace" class="justify-center">Replace Document</span>
                    <span v-else class="">Upload Document</span>
                    <!--                    <v-icon class="ms-2 hidden" name="pr-upload" fill="#FFF" v-show="handleHover"/>-->


                </div>
            </label>
            <label v-else :for="inputName"
                   class="py-3 px-6  rounded-lg  bg-orange-500 text-white hover:cursor-pointer transition">
                <span class="text-white">Document uploaded
                    <!--                    <v-icon name="md-done-round"/>-->
                </span>
            </label>
            <input
                :id="inputId"
                :name="inputName"
                class="form-control userFiles hidden userFormField"
                type="file"
                @change="handleUpload"
            />
            <Transition
                enter-active-class="duration-500 transition"
                enter-from-class="translate-y-full opacity-0"
                leave-active-class="duration-500 transition"
                leave-to-class="translate-y-full opacity-0"
                move-class="transition-all duration-200"
            >
                <Button
                    v-if="fileValue != null || isReplace"
                    :icon="'delete'"
                    :type="'danger'"
                    class="ms-2"
                    @click="clearFile"
                ></Button>
            </Transition>
        </div>
        <p v-if="helper" class="mt-2 text-sm text-gray-500">{{ helper }}</p>
        <p v-if="error" class="mt-2 text-sm text-red-500">{{ error }}</p>
    </div>
</template>

<style scoped>
</style>
