<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import ModelDataDisplay from '@/Organisms/ModelDataDisplay.vue';
import DropdownInput from '@/Atoms/DropdownInput.vue';
import {provide, ref, watch} from 'vue';
import Button from '@/Atoms/Button.vue';

// Define props for the component
const props = defineProps({
    data: {
        type: Object,
    },
    columns: {
        type: Object,
    },
    roles: {
        type: Array,
    },
    role: {
        type: String,
        default: null,
    },
});

// Define column types
const colTypes = [
    {
        name: 'icon',
        type: 'icon',
    },
];

// Define row highlighting rules based on roles
const rowHighlight = [
    {
        name: 'role name',
        value: 'student',
        className: 'bg-blue-100',
    }, {
        name: 'role name',
        value: 'admin',
        className: 'bg-green-100',
    }, {
        name: 'role name',
        value: 'agent',
        className: 'bg-yellow-100',
    },
];

// Define excluded pages
const excludedPages = ['id'];

// Initialize reactive form items
const formItems = ref({
    formItems: {},
    role: ref(props.role),
});

// Create a form instance for filtering using the reactive form items
const formForFiltering = useForm(formItems.value);

// Watch for changes in form items and update the page role
watch(formItems.value.formItems, function(value, oldValue) {
    formItems.value.pageRole = value.pageRole.value;
});

// Provide formItems globally using the 'provide' function
provide('formItems', formItems);

// Function to filter the table based on the form values
const filterTable = () => {
    formForFiltering.role = formItems.value.pageRole;
    formForFiltering.get('/admin/pages', {
        preserveScroll: true,
    });
};

// Create a reactive variable for controlling the visibility of filter options
const showFilterOptions = ref(false);

</script>

<template>
    <Head title="Profile"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Admin Panel</h2>
        </template>
        <div class="py-6 mt-6">

            <Button class="my-4" type="success" @click="showFilterOptions = !showFilterOptions">
                {{ showFilterOptions ? 'Hide filter options' : 'Show filter options' }}
            </Button>
            <div v-if="showFilterOptions">
                <DropdownInput
                    :options="props.roles"
                    :selected-item="props.role"
                    input-name="pageRole"
                    label="Page roles"
                />
                <div class="flex justify-between">
                    <Button class="mt-3" @click="filterTable">Filter Table</Button>
                    <Link :href="route('showPage')">
                        <Button class="mt-3" type="danger">Reset</Button>
                    </Link>
                </div>
            </div>

            <ModelDataDisplay :column-types="colTypes" :columns="props.columns" :data="props.data"
                              :excluded-columns="excludedPages"
                              :is-deletable="true"
                              :is-editable="true" :row-highlight="rowHighlight"
                              delete-route="/admin/pages/deletePage"
                              edit-route="editPage"
                              route-for-new="createNewPage" section-title="Custom Pages"/>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
