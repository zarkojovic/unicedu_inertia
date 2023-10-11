<script setup>
import {computed, defineProps, inject, onMounted, ref} from 'vue';

const props = defineProps({
    modelValue: {
        type: Object
    },
    options: {
        type: Array
    },
    label: {
        type: String
    },
    is_required: {
        type: Boolean
    },
    inputName: {
        type: String
    },
    inputId: {
        type: String
    },
})

const emits = defineEmits(['update:modelValue']);

const selected = ref(props.modelValue);

const formItems = inject('formItems');

const handleUpdate = (event) => {
    var obj = props.options.filter(el => el.value === event.target.value);
    formItems.value.formItems[props.inputName] = obj[0];
    console.log(formItems.value.formItems)
    emits('update:modelValue', obj[0]);

};

</script>

<template>

    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" v-if="label">
        {{ props.label }}
        <span class="text-sm text-red-600" v-if="props.is_required">*</span>
    </label>
    <select
        @input="handleUpdate"
        v-model="props.modelValue.value"
        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-300 dark:focus:border-orange-400 focus:ring-orange-300 dark:focus:orange-400 rounded-lg shadow-sm transition ease-in-out delay-100 mt-1 block w-full userFormField"
    >
        <option v-for="(option,index) in props.options" :key="index" :value="option.value">{{ option.label }}
        </option>
    </select>

</template>
