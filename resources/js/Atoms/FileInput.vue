<script setup>
import {addIcons} from "oh-vue-icons";
import {MdDoneRound, PrUpload} from "oh-vue-icons/icons";
import toast from '@/Stores/toast';
import {ref, defineProps, defineEmits, inject} from 'vue';
import Button from '@/Atoms/Button.vue';
import {trans} from 'laravel-vue-i18n';

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
    isCategoryField
} = defineProps([
    'label',
    'error',
    'helper',
    'modelValue',
    'is_required',
    'inputName',
    'inputId',
    'validTypes',
    'isCategoryField'
]);

// Add icons for later use
addIcons(MdDoneRound, PrUpload);

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
            // emits('update:modelValue', event.target.files[0].name);

            if (isCategoryField) {
                formItems.value.formItems[inputName] = {
                    value: fileValue.value,
                    is_file: true
                };
            }
            upload.value = true;

            toast.add({
                message: 'File uploaded but not saved yet!',
                duration: 4000,
                type: 'warning'
            });

            setTimeout(function () {
                upload.value = false;
                isHover.value = false;
                isReplace.value = true;
            }, 4000);
        } else {
            toast.add({
                message: `Invalid document type!`,
                duration: 4000,
                type: 'danger'
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

const clearFile = function () {
    fileValue.value = null;
    isReplace.value = false;
    upload.value = false;
    isHover.value = false;
}

</script>
<template>
    <div>
        <label class="text-slate-600 block text-sm font-medium dark:text-gray-300" v-if="label">{{ label }} <span
            class="text-sm text-red-600" v-if="is_required">*</span></label>
        <div class="mt-1 flex">
            <label :for="inputName" v-if="!upload" @mouseenter="handleHover" @mouseleave="handleMove"
                   class="py-3 px-6  rounded-lg  border border-gray-300 hover:bg-orange-500 hover:cursor-pointer hover:text-white hover:border-transparent transition">
                <div>
                    <span v-if="isReplace">Replace Document</span>
                    <span v-else>Upload Document</span>
                    <Transition
                        enter-from-class=" opacity-0"
                        leave-to-class="opacity-0"
                    >
                        <v-icon v-if="isHover" class="ms-2" name="pr-upload" fill="#FFF"/>
                    </Transition>
                </div>
            </label>
            <label :for="inputName" v-else
                   class="py-3 px-6  rounded-lg  bg-orange-500 text-white hover:cursor-pointer transition">
                <span class="text-white">Document uploaded <v-icon name="md-done-round"/></span>
            </label>
            <input
                type="file"
                :id="inputId"
                :name="inputName"
                class="form-control userFiles hidden userFormField"
                @change="handleUpload"
            />
            <Transition
                enter-from-class="translate-y-full opacity-0"
                enter-active-class="duration-500 transition"
                move-class="transition-all duration-200"
                leave-active-class="duration-500 transition"
                leave-to-class="translate-y-full opacity-0"
            >
                <Button
                    v-if="fileValue != null"
                    class="ms-2"
                    :icon="'delete'"
                    :type="'danger'"
                    @click="clearFile()"
                ></Button>
            </Transition>
        </div>
        <p class="mt-2 text-sm text-gray-500" v-if="helper">{{ helper }}</p>
        <p class="mt-2 text-sm text-red-500" v-if="error">{{ error }}</p>
    </div>
</template>

<style scoped>
</style>
