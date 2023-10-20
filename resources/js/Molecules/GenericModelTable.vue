<script setup>

import Button from '@/Atoms/Button.vue';
import {computed} from 'vue';
import Pagination from '@/Atoms/Pagination.vue';

const props = defineProps({
    data: {
        type: Object,
    },
    columns: {
        type: Array,
    },
    isEditable: {
        type: Boolean,
        default: true,
    },
    isDeletable: {
        type: Boolean,
        default: true,
    },
});

const formattedData = computed(() => {

    return props.data.data.map((obj) => props.columns.map((colName) => obj[colName]));
});


</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead
            class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th v-for="(column,index) in props.columns" :key="index" class="px-6 py-3"
                scope="col">
                {{ column }}
            </th>
            <th v-if="props.isEditable"
                class="px-6 py-3" scope="col">
                Edit
            </th>
            <th v-if="props.isDeletable"
                class="px-6 py-3" scope="col">
                Delete
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(item,index) in formattedData"
            v-if="formattedData.length > 0"
            :key="index"
            :class="index % 2 ? 'bg-gray-50' : 'bg-white'"
            class=" border-b dark:bg-gray-900 dark:border-gray-700"
        >
            <td v-for="(element,index) in item" :key="index" class="px-6 py-4">
                {{ element }}
            </td>
            <td v-if="props.isEditable">
                <Button :type="'success'">Edit</Button>
            </td>
            <td v-if="props.isDeletable">
                <Button :type="'danger'">Delete</Button>
            </td>
        </tr>
        </tbody>
    </table>

    <h1 v-if="formattedData.length === 0" class="text-3xl text-center my-5">
        No data for now...
    </h1>
    <Pagination :links="props.data.links"/>

</template>

<style scoped>

</style>
