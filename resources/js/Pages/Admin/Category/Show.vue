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
            <ModelDataDisplay :columns="props.columns" :data="props.data" :excluded-columns="hidden"
                              :is-deletable="true" :row-highlight="checkRow"
                              delete-route="/admin/categories/deleteCategory"
                              route-for-new="createNewCategory"
                              section-title="Fields Categories"/>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
