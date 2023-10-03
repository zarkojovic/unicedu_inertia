<script setup>
import {ref, defineProps, defineEmits} from 'vue';

const {
    label,
    items,
    modelValue,
    type,
    name,
    id,
    is_required
} = defineProps(['label', 'items', 'modelValue', 'type', 'name', 'id', 'is_required']);

const emits = defineEmits(['update:modelValue']);
const selectedItems = ref([...modelValue]);

const isChecked = (item) => selectedItems.value.includes(item);

const toggleSelection = (item) => {
    if (type === 'radio') {
        selectedItems.value = item; // For radio buttons, only one item can be selected at a time
    } else {
        if (isChecked(item)) {
            selectedItems.value = selectedItems.value.filter((selectedItem) => selectedItem !== item);
        } else {
            selectedItems.value.push(item);
        }
    }
    emits('update:modelValue', selectedItems.value);
};
</script>


<template>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" v-if="label">{{ label }} <span
            class="text-sm text-red-600" v-if="is_required">*</span></label>
        <ul>
            <li v-for="(item, index) in items" :key="index">
                <input
                    :type="type === 'radio' ? 'radio' : 'checkbox'"
                    :id="name + '-' + index"
                    :value="item"
                    class="dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-orange-500 shadow-sm focus:ring-0 focus:ring-offset-0 transition ease-in-out"
                    :class="type === 'radio' ? 'rounded-full' : 'rounded'"
                    :name="type === 'radio' ? name : null"
                    :checked="isChecked(item)"
                    @change="toggleSelection(item)"

                />
                <label :for="'input-' + index" class="ms-2">{{ item }}</label>
            </li>
        </ul>
        <p>Selected Items: {{ selectedItems.length > 0 ? selectedItems : '' }}</p>
    </div>
</template>

