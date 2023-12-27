<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import ModelDataDisplay from '@/Organisms/ModelDataDisplay.vue';
import Button from '@/Atoms/Button.vue';
import {provide, ref, watch} from 'vue';
import DropdownInput from '@/Atoms/DropdownInput.vue';
import GenericInput from '@/Atoms/GenericInput.vue';

// Define props for the component
const props = defineProps({
    data: {
        type: Object,
    },
    actions: {
        type: Object,
    },
    action: {
        type: String,
        default: null,
    },
    user_email: {
        type: String,
        default: '',
    },
    begin_date: {
        type: String,
        default: '',
    },
    end_date: {
        type: String,
        default: '',
    },
});

// Define rules for highlighting rows based on action types
const rowHighlight = [
    {
        name: 'action_name',
        value: 'information',
        className: 'bg-blue-100',
    },
    {
        name: 'action_name',
        value: 'errors',
        className: 'bg-red-100',
    },
    {
        name: 'action_name',
        value: 'api',
        className: 'bg-green-100',
    },
];

// Initialize reactive form items with default values
const formItems = ref({
    formItems: {},
    action: ref(props.action),
    user_email: ref(props.user_email),
    begin_date: ref(props.begin_date),
    end_date: ref(props.end_date),
});

// Create a form instance for filtering using the reactive form items
const formForFiltering = useForm(formItems.value);

// Watch for changes in form items and update the form for filtering
watch(formItems.value.formItems, function(value, oldValue) {
    formForFiltering.action = value.logAction.value;
});

// Provide formItems globally using the 'provide' function
provide('formItems', formItems);

// Function to filter the table based on the form values
const filterTable = () => {
    formForFiltering.user_email = formItems.value.user_email;
    formForFiltering.begin_date = formItems.value.begin_date;
    formForFiltering.end_date = formItems.value.end_date;
    formForFiltering.get('/admin/dashboard', {
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
                    :options="props.actions"
                    :selected-item="props.action"
                    input-name="logAction"
                    label="Log Action"
                />
                <GenericInput
                    v-model="formItems.user_email"
                    class="mt-4"
                    input-name="user_email"
                    label="User Email"
                    placeholder="Enter User Email"
                    type="text"/>
                <GenericInput v-model="formItems.begin_date"
                              class="mt-4"
                              input-name="begin_date"
                              label="Begin Date"
                              placeholder="Enter Begin Date"
                              type="date"/>
                <GenericInput v-model="formItems.end_date"
                              class="mt-4"
                              input-name="end_date"
                              label="End Date"
                              placeholder="Enter End Date"
                              type="date"/>
                <div class="flex justify-between">
                    <Button class="mt-3" @click="filterTable">Filter Table</Button>
                    <Link :href="route('adminDashboard')">
                        <Button class="mt-3" type="danger">Reset</Button>
                    </Link>
                </div>
            </div>
            <ModelDataDisplay :columns="props.columns" :data="props.data" :row-highlight="rowHighlight"
                              section-title="Logs"/>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
