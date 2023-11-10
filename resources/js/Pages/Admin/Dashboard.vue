<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import ModelDataDisplay from '@/Organisms/ModelDataDisplay.vue';

const props = defineProps({
    data: {
        type: Object,
    },
});

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

</script>

<template>
    <Head title="Profile"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Admin Panel</h2>
        </template>
        <div class="py-6 mt-6">
            <ModelDataDisplay :columns="props.columns" :data="props.data" :row-highlight="rowHighlight"
                              section-title="Logs"/>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
