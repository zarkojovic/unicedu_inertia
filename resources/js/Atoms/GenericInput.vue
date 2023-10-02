<script setup>
import {ref, defineProps, defineEmits, computed, watch, onMounted} from 'vue';
import toast from '@/Stores/toast';


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
    }
});

// Define emits for custom events
const emits = defineEmits(['input', 'focus', 'blur', 'update:modelValue']);

const input = ref(null);

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
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" v-if="label">
            {{ label }}
<!--            <span class="text-sm text-red-600" v-if="is_required">*</span>-->
        </label>
        <input
            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-300 dark:focus:border-orange-400 focus:ring-orange-300 dark:focus:orange-400 rounded-lg shadow-sm transition ease-in-out delay-100 mt-1 block w-full"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            ref="input"
            :class="{ 'border-red-500': error }"
            :type="props.type"
            :placeholder="props.placeholder"
            :disabled="props.disabled"
            @focus="emitFocus"
            @blur="emitBlur"
            :required="props.is_required"
            :name="props.inputName"
            :id="props.inputId"
        />
        <p class="mt-2 text-sm text-gray-500" v-if="props.helper">{{ props.helper }}</p>
        <p class="mt-2 text-sm text-red-500" v-if="error">{{ error }}</p>
    </div>
</template>
