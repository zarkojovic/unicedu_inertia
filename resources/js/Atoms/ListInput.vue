<script setup>
import {defineEmits, defineProps, onMounted, ref} from 'vue';

const {
    label,
    items,
    modelValue,
    type,
    name,
    id,
    is_required,
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
            // selectedItems.value = [];
            selectedItems.value.push(item.field_category_id);
        }
    }
    emits('update:modelValue', selectedItems.value);
};

onMounted(() => {
    console.log(items);
});
</script>


<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ label }} <span
            v-if="is_required" class="text-sm text-red-600">*</span></label>
        <ul>
            <li v-for="(item, index) in items" :key="index">
                <input
                    :id="name + '-' + index"
                    :checked="isChecked(item.field_category_id)"
                    :class="type === 'radio' ? 'rounded-full' : 'rounded'"
                    :name="type === 'radio' ? name : null"
                    :type="type === 'radio' ? 'radio' : 'checkbox'"
                    :value="item.field_category_id"
                    class="dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-orange-500 shadow-sm focus:ring-0 focus:ring-offset-0 transition ease-in-out"
                    @change="toggleSelection(item)"

                />
                <label :for="'input-' + index" class="ms-2">{{ item.category_name }}</label>
            </li>
        </ul>
        <p>Selected Items: {{ selectedItems.length > 0 ? selectedItems : '' }}</p>
    </div>
</template>

