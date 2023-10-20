<script setup>

const props = defineProps({
    data: {
        type: Object,
    },
    columns: {
        type: Array,
    },
    sectionTitle: {
        type: String,
    },
    routeForNew: {
        type: String,
    },
    isDeletable: {
        type: Boolean,
        default: true,
    },
});

import {Link} from '@inertiajs/vue3';
import GenericModelTable from '@/Molecules/GenericModelTable.vue';
import Button from '@/Atoms/Button.vue';
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
            <GenericModelTable :columns="props.columns" :data="props.data" :is-deletable="props.isDeletable"
                               :is-editable="props.isEditable"/>
        </div>
    </div>
</template>

<style scoped>

</style>
