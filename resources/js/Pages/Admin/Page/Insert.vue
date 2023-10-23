<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import Button from '@/Atoms/Button.vue';
import GenericInput from '@/Atoms/GenericInput.vue';
import ListInput from '@/Atoms/ListInput.vue';
import toast from '@/Stores/toast.js';

const props = defineProps({
    data: {
        type: Array,
    },
    columns: {
        type: Array,
    },
    roles: {
        type: Object,
    },
    categories: {
        type: Object,
    },
});

const form = useForm({
    title: '',
    route: '',
    categories: [],
    roles: [],
});

const submit = () => {
    console.log('pozz');
    form.post('/admin/pages/insertNew', {
        onSuccess: () => {
            toast.add({
                message: 'Hello!',
                type: 'success',
            });
        },
    });
};

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
                        <form @submit="submit">
                            <GenericInput v-model="form.title" :error="form.errors.roles" :is_required='true'
                                          label="Insert page name"/>
                            <GenericInput v-model="form.route" :error="form.errors.route" :is_required='true'
                                          class="mt-4"
                                          label="Insert page route"/>
                            <ListInput v-model="form.categories" :is_required="true"
                                       :items="props.categories"
                                       class="mt-4"
                                       label="Select categories you want to display here"
                                       type="checkbox"
                            />
                            <ListInput v-model="form.roles"
                                       :error="form.errors.roles"
                                       :is_required="true"
                                       :items="props.roles"
                                       class="mt-4"
                                       label="Select roles"
                                       name="role_radio"
                                       type="radio"
                            />
                            <Button :disabled="form.processing" class="mt-5" @click="submit">Add new Page</Button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
