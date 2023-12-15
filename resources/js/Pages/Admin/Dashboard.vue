<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import ModelDataDisplay from '@/Organisms/ModelDataDisplay.vue';
import Button from '@/Atoms/Button.vue';
import {provide, ref, watch} from 'vue';
import DropdownInput from '@/Atoms/DropdownInput.vue';

const props = defineProps({
    data: {
        type: Object,
    },
    actions: {
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

const formItems = ref({
    formItems: {},
    action: null,
});

const formForFiltering = useForm(formItems.value);

watch(formItems.value.formItems, function(value, oldValue) {
    formForFiltering.action = value.logAction.value;
});

provide('formItems', formItems);

const filterTable = () => {
    // console.log(formForFiltering.action);
    formForFiltering.get('/admin/dashboard', {
        preserveScroll: true,
    });
};

</script>

<template>
    <Head title="Profile"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Admin Panel</h2>
        </template>
        <div class="py-6 mt-6">
            <DropdownInput
                :options="props.actions"
                input-name="logAction"
                label="Log Action"
            />
            <Button class="mt-3" @click="filterTable">Filter Table</Button>
            <ModelDataDisplay :columns="props.columns" :data="props.data" :row-highlight="rowHighlight"
                              section-title="Logs"/>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
