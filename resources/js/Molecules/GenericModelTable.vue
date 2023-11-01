<script setup>

import Button from '@/Atoms/Button.vue';
import {computed, onMounted, ref} from 'vue';
import Pagination from '@/Atoms/Pagination.vue';
import Modal from '@/Molecules/Modal.vue';
import {Link, useForm, usePage} from '@inertiajs/vue3';

import {Icon} from '@iconify/vue';

const props = defineProps({
    data: {
        type: Object,
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
    columnTypes: {
        type: Array,
    },
    excludedColumns: {
        type: Array,
    },
});

const openModal = ref(false);

const deleteItemId = ref(null);

const page = usePage();

const formDelete = useForm({
    id: ref(deleteItemId),
});
const deleteItem = () => {
    openModal.value = false;
    formDelete.post(props.deleteRoute, {
        preserveScroll: true,
    });

};

const columns = computed(() => {
    if (props.data.data[0]) {
        return Object.keys(props.data.data[0]);
    } else {
        return [];
    }
});

const typeOfColumn = (item) => {
    if (props.columnTypes) {
        const result = props.columnTypes.find(obj => obj.name === item);
        return result ? result.type : false;
    }
    return false;
};

const isIncluded = (col) => {
    if (props.excludedColumns) {
        return !props.excludedColumns.includes(col);
    }
    return true;
};

onMounted(() => {
    if (props.columnTypes) {
    }
});

</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 shadow-md ">
        <thead
            class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <template v-for="(column,index) in columns" v-if="columns" :key="index" class="px-6 py-3">
                <th v-if="isIncluded(column)" class="px-6 py-3">
                    {{ column }}
                </th>
            </template>
            <th v-if="props.isEditable && columns"
                class="px-6 py-3" scope="col">
                Edit
            </th>
            <th v-if="props.isDeletable && columns"
                class="px-6 py-3" scope="col">
                Delete
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(item,index) in props.data.data"
            v-if="props.data.data.length > 0"
            :key="index"
            class=" border-b dark:bg-gray-900 dark:border-gray-700"
        >
            <template v-for="(col,idx) in columns" :key="idx">
                <td v-if="isIncluded(col)" class="px-6 py-4">
                    <div v-if="typeOfColumn(col)">
                        <img v-if="typeOfColumn(col) === 'image'" :src="page.props.images_root + item[col]"
                             alt="table image"
                             style="width: 80px"/>
                        <Icon v-if="typeOfColumn(col) === 'icon'" :icon="'tabler:'+item[col]" class="text-2xl me-2"
                              inline/>
                    </div>
                    <div v-else>
                        {{ item[col] }}
                    </div>
                </td>
            </template>
            <td v-if="props.isEditable">
                <Link :href="route(props.editRoute,{id:item['id']})">
                    <Button type="success">Edit</Button>
                </Link>
            </td>
            <td v-if="props.isDeletable">
                <Button type="danger" @click="openModal = true; formDelete.id = item['id']">Delete</Button>
            </td>
        </tr>
        </tbody>
    </table>

    <h1 v-if="props.data.data === 0" class="text-3xl text-center my-5">
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
