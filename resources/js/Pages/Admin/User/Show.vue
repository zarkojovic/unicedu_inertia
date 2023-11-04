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
const hideColumns = ['id'];

</script>

<template>
    <Head title="Profile"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Admin Panel</h2>
        </template>

        <div class="py-6 mt-6">
            <ModelDataDisplay :column-types="columnTypes" :columns="props.columns" :data="props.data"
                              :excluded-columns="hideColumns" :is-deletable="false" :is-editable="true"
                              edit-route="editUser"
                              section-title="Users"/>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
