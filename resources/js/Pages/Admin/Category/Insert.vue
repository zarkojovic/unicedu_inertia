<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import Button from '@/Atoms/Button.vue';
import GenericInput from '@/Atoms/GenericInput.vue';

const props = defineProps({
    data: {
        type: Array,
    },
    columns: {
        type: Array,
    },
});

const insertForm = useForm({
    categoryName: null,
});

const sendInsert = () => {
    var checkErr = false;
    if (insertForm.categoryName === '' || insertForm.categoryName === null) {
        insertForm.errors.categoryName = 'You have to insert category name!';
        checkErr = true;

    } else {
        insertForm.errors.categoryName = null;
        checkErr = false;
    }
    if (!checkErr) {
        insertForm.post('/admin/categories/insertNew');
    }

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
                            <h1 class="text-2xl bold antialiased font-bold">Insert Category</h1>
                            <Link :href="route('showCategory')">
                                <Button>Go Back</Button>
                            </Link>
                        </div>
                        <GenericInput v-model="insertForm.categoryName" :error="insertForm.errors.categoryName"
                                      input-name="text" label="Insert category name"
                                      placeholder="Insert new categories"/>
                        <Button class="mt-3" @click="sendInsert">Submit</Button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
