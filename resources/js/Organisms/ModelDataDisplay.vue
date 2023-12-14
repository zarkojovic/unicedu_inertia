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
    columnTypes: {
        type: Array,
    },
    excludedColumns: {
        type: Array,
    },
    rowHighlight: {
        type: Array,
    },
});


</script>

<template>
    <div class="flex justify-between items-center mb-5 px-5">
        <h1 class="text-2xl bold antialiased font-bold">{{ props.sectionTitle }}</h1>
        <Link v-if="props.routeForNew" :href="route(props.routeForNew)">
            <Button>Add New</Button>
        </Link>
    </div>
    <div class="mx-auto bg-white shadow-lg overflow-hidden rounded-3xl">
        <div class="text-gray-900 dark:text-gray-100">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

                <GenericModelTable
                    :column-types="props.columnTypes" :data="props.data"
                    :delete-route="props.deleteRoute" :edit-route="props.editRoute"
                    :excluded-columns="props.excludedColumns" :is-deletable="props.isDeletable"
                    :is-editable="props.isEditable" :row-highlight="props.rowHighlight"/>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
