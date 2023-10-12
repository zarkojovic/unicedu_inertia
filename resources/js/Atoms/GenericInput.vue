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
        default: ''
    },
    helper: {
        type: String,
    },
    modelValue: {
        type: String,
    },
    is_required: {
        type: Boolean,
        default: false
    },
    inputName: {
        type: String
    },
    inputId: {
        type: String
    },
    isCategoryField: {
        type: Boolean
    }

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
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" v-if="label">
            {{ label }}
            <span class="text-sm text-red-600" v-if="props.is_required">*</span>
        </label>
        <input
            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-300 dark:focus:border-orange-400 focus:ring-orange-300 dark:focus:orange-400 rounded-lg shadow-sm transition ease-in-out delay-100 mt-1 block w-full userFormField"
            v-model="inputValue"
            @input="handleUpdate"
            :class="{ 'border-red-500': error }"
            :type="props.type"
            :placeholder="props.placeholder"
            :disabled="props.disabled"
            :required="props.is_required"
            :name="props.inputName"
            :id="props.inputId"
        />
        <p class="mt-2 text-sm text-gray-500" v-if="props.helper">{{ props.helper }}</p>
        <p class="mt-2 text-sm text-red-500" v-if="error">{{ error }}</p>
    </div>
</template>
