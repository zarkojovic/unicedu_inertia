<script setup>
import {defineEmits, defineProps, inject, ref} from 'vue';

const props = defineProps({
    type: {
        type: String,
    },
    placeholder: {
        type: String,
    },
    disabled: {
        type: Boolean,
    },
    label: {
        type: String,
    },
    error: {
        type: String,
        default: '',
    },
    helper: {
        type: String,
    },
    modelValue: {
        type: String,
    },
    is_required: {
        type: Boolean,
        default: false,
    },
    inputName: {
        type: String,
    },
    inputId: {
        type: String,
    },
    isCategoryField: {
        type: Boolean,
    },

});

// Define emits for custom events
const emits = defineEmits(['focus', 'blur', 'update:modelValue']);

const inputValue = ref(props.modelValue);

const formItems = props.isCategoryField ? inject('formItems') : null;
const handleUpdate = (event) => {

    var inputValue = event.target.value;
    if (props.isCategoryField) {
        formItems.value.formItems[props.inputName] = {
            value: inputValue,
        };
    }

    emits('update:modelValue', event.target.value);
};

</script>
<template>
    <div>
        <label v-if="label" class="text-slate-600 block text-sm font-medium dark:text-gray-300">
            {{ label }}
            <span v-if="props.is_required" class="text-sm text-red-600">*</span>
        </label>
        <input
            :id="props.inputId"
            v-model="inputValue"
            :class="{ 'border-red-500': error }"
            :disabled="props.disabled"
            :name="props.inputName"
            :placeholder="props.placeholder"
            :required="props.is_required"
            :type="props.type"
            class="border-gray-300 text-slate-900 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-300 dark:focus:border-orange-400 focus:ring-orange-300 dark:focus:orange-400 rounded-lg shadow-sm transition ease-in-out delay-100 mt-1 mb-1  block w-full userFormField"
            
            @input="handleUpdate"
        />
        <p v-if="props.helper" class="mt-2 text-sm text-gray-500">{{ props.helper }}</p>
        <p v-if="error" class="mt-2 text-sm text-red-500">{{ error }}</p>
    </div>
</template>
