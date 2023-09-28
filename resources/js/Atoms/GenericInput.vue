<script setup>
import {ref, defineProps, defineEmits} from 'vue';
import TextInput from "@/Components/TextInput.vue";

// Define props
const {
    type = 'text',
    placeholder = '',
    disabled = false,
    label = '',
    error = '',
    helper = '',
    modelValue,
    is_required = false
} = defineProps([
    'type',
    'placeholder',
    'disabled',
    'label',
    'error',
    'helper',
    'modelValue',
    'is_required'
]);

// Define emits for custom events
const emits = defineEmits(['input', 'focus', 'blur', 'update:modelValue']);

const input = ref(null);


// Check if there's an error message
const isReq = ref(is_required);


// Emit input event when the input value changes
const emitInput = (event) => {
    emits('input', event.target.value);
};

// Emit focus event when the input is focused
const emitFocus = () => {
    emits('focus');
};


// Emit blur event when the input loses focus
const emitBlur = () => {
    emits('blur');
};
</script>
<template>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" v-if="label">{{ label }} <span
            class="text-sm text-red-600" v-if="is_required">*</span></label>
        <input
            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-300 dark:focus:border-orange-400 focus:ring-orange-300 dark:focus:orange-400 rounded-lg shadow-sm transition ease-in-out delay-100 mt-1 block w-full"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            ref="input"
            :class="{ 'border-red-500': error }"
            :type="type"
            :placeholder="placeholder"
            :disabled="disabled"
            @focus="emitFocus"
            @blur="emitBlur"
            :required="required"
        />
        <p class="mt-2 text-sm text-gray-500" v-if="helper">{{ helper }}</p>
        <p class="mt-2 text-sm text-red-500" v-if="error">{{ error }}</p>
    </div>
</template>
