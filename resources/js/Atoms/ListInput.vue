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
    error,
} = defineProps(['label', 'items', 'modelValue', 'type', 'name', 'id', 'is_required', 'error']);

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
            selectedItems.value.push(item);
        }
    }
    emits('update:modelValue', selectedItems.value);
};

onMounted(() => {
    // console.log(elementKey.value);
});


</script>


<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ label }} <span
            v-if="is_required" class="text-sm text-red-600">*</span></label>
        <ul>
            <li v-for="(item, index) in items" :key="index"
                :class="isChecked(index) ? 'text-orange-500 bg-orange-50 border-orange-200' : 'border-gray-300'"
                class=" mb-2 p-2 rounded-3xl border flex items-center">
                <input
                    :id="name + '-' + index"
                    :checked="isChecked(index)"
                    :class="type === 'radio' ? 'rounded-full' : 'rounded'"
                    :name="type === 'radio' ? name : null"
                    :type="type === 'radio' ? 'radio' : 'checkbox'"
                    :value="index"
                    class="dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-orange-500 focus:ring-0 focus:ring-offset-0 transition ease-in-out"
                    @change="toggleSelection(index)"
                />
                <label :for="name +'-' + index" class="ms-2">{{ item }}</label>
            </li>
        </ul>
        <!--        <p>Selected Items: {{ selectedItems.length > 0 ? selectedItems : '' }}</p>-->
        <p v-if="error" class=" text-sm text-red-500">{{ error }}</p>
    </div>
</template>

