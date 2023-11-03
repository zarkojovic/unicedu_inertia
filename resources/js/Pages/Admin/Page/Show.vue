<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import ModelDataDisplay from '@/Organisms/ModelDataDisplay.vue';

const props = defineProps({
    data: {
        type: Object,
    },
    columns: {
        type: Object,
    },
});

const colTypes = [
    {
        name: 'icon',
        type: 'icon',
    },
];

const excludedPages = ['id'];

const roleNames = ['student', 'admin', 'employee', 'agent'];

</script>

<template>
    <Head title="Profile"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Admin Panel</h2>
        </template>
        <div class="py-6 mt-6">
            <ModelDataDisplay :column-types="colTypes" :columns="props.columns" :data="props.data"
                              :excluded-columns="excludedPages"
                              :is-deletable="true" :is-editable="true"
                              :role-names="roleNames"
                              delete-route="/admin/pages/deletePage"
                              edit-route="editPage"
                              route-for-new="createNewPage" section-title="Custom Pages"/>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
