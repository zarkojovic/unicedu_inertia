<script setup>

import GenericModelTable from '@/Molecules/GenericModelTable.vue';
import {Link} from '@inertiajs/vue3';
import Button from '@/Atoms/Button.vue';

const props = defineProps({
    data: {
        type: Object,
    },
    columns: {
        type: Object,
    },
    sectionTitle: {
        type: String,
    },
    routeForNew: {
        type: String,
    },
    isDeletable: {
        type: Boolean,

    },
    isEditable: {
        type: Boolean,
    },
    deleteRoute: {
        type: String,
    },
    editRoute: {
        type: String,
    },
});
</script>

<template>
    <div class="p-6 text-gray-900 dark:text-gray-100">
        <div class="flex  justify-between items-center mb-5">
            <h1 class="text-2xl bold antialiased font-bold">{{ props.sectionTitle }}</h1>
            <Link v-if="props.routeForNew" :href="route(props.routeForNew)">
                <Button>Add New</Button>
            </Link>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <GenericModelTable :columns="props.columns" :data="props.data" :delete-route="props.deleteRoute"
                               :edit-route="props.editRoute"
                               :is-deletable="props.isDeletable" :is-editable="props.isEditable"/>
        </div>
    </div>
</template>

<style scoped>

</style>
