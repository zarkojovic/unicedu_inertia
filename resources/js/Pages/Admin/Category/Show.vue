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

</script>

<template>
    <Head title="Profile"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Admin Panel</h2>
        </template>

        <div class="mt-20">
            <div class="mx-auto bg-white rounded-xl shadow-md overflow-hidden ">
                <div class="bg-white overflow-hidden dark:bg-gray-800  shadow-sm sm:rounded-lg">
                    <ModelDataDisplay :columns="props.columns" :data="props.data" :excluded-columns="hidden"
                                      route-for-new="createNewCategory"
                                      section-title="Fields Categories"/>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
