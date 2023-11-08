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

const rowTypes = [
    {
        name: 'Profile Image',
        type: 'image',
    }, {
        name: 'Package',
        type: 'package',
    },
];

const checkRow = [
    {
        name: 'active',
        value: 'inactive',
        className: 'bg-red-100',
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
            <ModelDataDisplay :column-types="rowTypes" :columns="props.columns" :data="props.data"
                              :excluded-columns="hidden" :row-highlight="checkRow"
                              section-title="Student Applications"/>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
