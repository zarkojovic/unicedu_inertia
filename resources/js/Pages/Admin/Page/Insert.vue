<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import Button from '@/Atoms/Button.vue';
import GenericInput from '@/Atoms/GenericInput.vue';
import ListInput from '@/Atoms/ListInput.vue';
import {onMounted} from 'vue';

const props = defineProps({
    data: {
        type: Array,
    },
    columns: {
        type: Array,
    },
    roles: {
        type: Array,
    },
    categories: {
        type: Object,
    },
});

onMounted(() => {
    console.log(props.categories);
});

const form = useForm({
    category: [],
    roles: [],
});

</script>

<template>
    <Head title="Profile"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Admin Panel</h2>
        </template>

        <div class="mt-20">
            <div class="mx-auto bg-white rounded-xl shadow-md overflow-hidden w-5/6">
                <div class="bg-white overflow-hidden dark:bg-gray-800  shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex  justify-between items-center mb-5">
                            <h1 class="text-2xl bold antialiased font-bold">Insert Page</h1>
                            <Link :href="route('showPage')">
                                <Button>Go Back</Button>
                            </Link>
                        </div>
                        <GenericInput :is_required='true' label="Insert page name"/>
                        <ListInput v-model="form.category" :items="props.categories"
                                   label="Select categories you want to display here"
                                   type="checkbox"/>
                        <ListInput v-model="form.roles" :items="props.roles"
                                   label="Select roles"
                                   type="radio"/>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
