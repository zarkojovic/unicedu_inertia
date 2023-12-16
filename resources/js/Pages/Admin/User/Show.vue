<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import ModelDataDisplay from '@/Organisms/ModelDataDisplay.vue';
import {provide, ref, watch} from 'vue';
import DropdownInput from '@/Atoms/DropdownInput.vue';
import Button from '@/Atoms/Button.vue';
import GenericInput from '@/Atoms/GenericInput.vue';

const props = defineProps({
    data: {
        type: Object,
    },
    columns: {
        type: Object,
    },
    userPackages: {
        type: Array,
    },
    userRoles: {
        type: Array,
    },
    package_id: {
        type: String,
        default: null,
    },
    role_id: {
        type: String,
        default: null,
    },
    userInfo: {
        type: String,
        default: null,
    },
});

const columnTypes = [
    {
        name: 'profile_image',
        type: 'image',
    },
    {
        name: 'package',
        type: 'package',
    },
];

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
const hideColumns = ['id'];
const formItems = ref({
    formItems: {},
    package_id: ref(props.package_id),
    role_id: ref(props.role_id),
    userInfo: ref(props.userInfo),
});

const formForFiltering = useForm(formItems.value);

watch(formItems.value.formItems, function(value, oldValue) {
    const userRole = value.userRole;
    const userPackage = value.userPackage;

    if (userRole) {
        formItems.value.userRole = userRole.value;
    }
    if (userPackage) {
        formItems.value.userPackage = userPackage.value;
    }
});

provide('formItems', formItems);
// Function to filter the table based on the form values
const filterTable = () => {
    formForFiltering.role_id = formItems.value.userRole;
    formForFiltering.package_id = formItems.value.userPackage;
    formForFiltering.userInfo = formItems.value.userInfo;
    formForFiltering.get('/admin/users', {
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
                <GenericInput v-model="formItems.userInfo" class="mt-4"
                              helper="Search for first name, last name, email, phone"
                              input-name="userInfo"
                              label="User Info"
                />
                <DropdownInput
                    :options="props.userPackages"
                    :selected-item="props.package_id"
                    input-name="userPackage"
                    label="Users package"
                />
                <DropdownInput
                    :options="props.userRoles"
                    :selected-item="props.role_id"
                    input-name="userRole"
                    label="Users roles"
                />
                <div class="flex justify-between">
                    <Button class="my-4" @click="filterTable">
                        Filter
                    </Button>
                    <Link :href="route('showUser')">
                        <Button class="mt-3" type="danger">Reset</Button>
                    </Link>
                </div>
            </div>
            <ModelDataDisplay :column-types="columnTypes" :columns="props.columns" :data="props.data"
                              :excluded-columns="hideColumns"
                              :is-deletable="false" :is-editable="true" :row-highlight="rowHighlight"
                              edit-route="editUser"
                              section-title="Users"/>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
