<script setup>

import Button from '@/Atoms/Button.vue';
import {computed, ref} from 'vue';
import Pagination from '@/Atoms/Pagination.vue';
import Modal from '@/Molecules/Modal.vue';
import {Link, useForm} from '@inertiajs/vue3';

const props = defineProps({
    data: {
        type: Object,
    },
    columns: {
        type: Object,
        default: null,
    },
    isEditable: {
        type: Boolean,
        default: true,
    },
    isDeletable: {
        type: Boolean,
        default: true,
    },
    deleteRoute: {
        type: String,
    },
    editRoute: {
        type: String,
    },
});

const openModal = ref(false);

const formattedData = computed(() => {
    if (props.columns === null) {
        return props.data.data.map((obj) => Object.values(obj));
    }
    return props.data.data.map((obj) => props.columns.map((colName) => obj[colName]));
});

const deleteItemId = ref(null);

const formDelete = useForm({
    id: ref(deleteItemId),
});
const deleteItem = () => {
    openModal.value = false;
    formDelete.post(props.deleteRoute, {
        preserveScroll: true,
    });

};

</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead
            class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th v-for="(column,index) in props.columns" v-if="props.columns" :key="index" class="px-6 py-3"
                scope="col">
                {{ column }}
            </th>
            <th v-if="props.isEditable && props.columns"
                class="px-6 py-3" scope="col">
                Edit
            </th>
            <th v-if="props.isDeletable && props.columns"
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
                <Link :href="route(props.editRoute,{id:item[0]})">
                    <Button type="success">Edit</Button>
                </Link>
            </td>
            <td v-if="props.isDeletable">
                <Button type="danger" @click="openModal = true; formDelete.id = item[0]">Delete</Button>
            </td>
        </tr>
        </tbody>
    </table>

    <h1 v-if="formattedData.length === 0" class="text-3xl text-center my-5">
        No data for now...
    </h1>
    <Modal v-if="openModal && props.isDeletable" @close="openModal = false"
    >
        <template #modalTitle>
            <h1>Confirm your action</h1>
        </template>
        <template #modalContent>
            <h1 class="h2">Are you sure you want to delete this item {{ formDelete.id }}?</h1>
        </template>
        <template #modalFooter>
            <Button class="ms-3" type="danger" @click="deleteItem">
                Delete it
            </Button>
            <Button type="muted" @click="openModal = false">
                Cancel
            </Button>
        </template>
    </Modal>
    <Pagination :links="props.data.links"/>

</template>

<style scoped>

</style>
