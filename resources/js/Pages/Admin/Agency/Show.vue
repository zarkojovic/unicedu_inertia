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

const hidden = ['id'];

const types = [
    {
        name: 'profile_image',
        type: 'image',
    },
];

const checkRow = [
    {
        name: 'is_visible',
        value: false,
        className: 'bg-red-100',
    }, {
        name: 'is_visible',
        value: true,
        className: 'bg-green-100',
    },
];
</script>

<template>
    <Head title="Profile"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Admin Panel</h2>
        </template>

        <div class="py-6 mt-6">
            <ModelDataDisplay :column-types="types" :columns="props.columns" :data="props.data"
                              :excluded-columns="hidden" :is-deletable="true"
                              :row-highlight="checkRow"
                              delete-route="/admin/agencies/deleteAgency"
                              route-for-new="createNewAgency"
                              section-title="Agencies"/>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
