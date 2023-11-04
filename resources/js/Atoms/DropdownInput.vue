<script setup>
import {defineProps, inject, ref} from 'vue';

const props = defineProps({
    modelValue: {
        type: Object,
    },
    options: {
        type: Array,
    },
    label: {
        type: String,
    },
    is_required: {
        type: Boolean,
    },
    inputName: {
        type: String,
    },
    inputId: {
        type: String,
    },
    selectedItem: {},
});

const emits = defineEmits(['update:modelValue']);

const selected = ref(props.modelValue);

const formItems = inject('formItems');

const handleUpdate = (event) => {
    var obj = props.options.filter(el => el.value === event.target.value);
    formItems.value.formItems[props.inputName] = obj[0];
    emits('update:modelValue', obj[0]);
};

const val = ref(props.selectedItem);
</script>

<template>

    <label v-if="label" class="text-slate-600 block text-sm font-medium dark:text-gray-300">
        {{ props.label }}
        <span v-if="props.is_required" class="text-sm text-red-600">*</span>
    </label>
    <select
        v-model="val"
        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-300 dark:focus:border-orange-400 focus:ring-orange-300 dark:focus:orange-400 rounded-lg shadow-sm transition ease-in-out delay-100 mt-1 block w-full userFormField"
        @input="handleUpdate"
    >
        <option v-for="(option,index) in props.options" :key="index" :value="option.value">{{ option.label }}
        </option>
    </select>

</template>
