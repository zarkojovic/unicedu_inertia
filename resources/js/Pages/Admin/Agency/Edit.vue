<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import Button from '@/Atoms/Button.vue';
import GenericInput from '@/Atoms/GenericInput.vue';
import FileInput from '@/Atoms/FileInput.vue';
import {provide, ref} from 'vue';
import toast from '@/Stores/toast.js';

const props = defineProps({
    data: {
        type: Array,
    },
    columns: {
        type: Array,
    },
});

const formItems = ref({
    formItems: {},
    companyName: '',
    email: '',
    phone: '',
    password: '',
    confirm_password: '',
    profileImage: null,
});

provide('formItems', formItems);
const insertForm = useForm(formItems.value);

const sendInsert = () => {

    if (insertForm.companyName === '') {
        insertForm.errors.companyName = 'The first name field is required.';
        return;
    } else {
        insertForm.errors.companyName = null;
    }

    if (insertForm.email === '') {
        insertForm.errors.email = 'The email field is required.';
        return;
    } else {
        insertForm.errors.email = null;
    }
    if (insertForm.phone === '') {
        insertForm.errors.phone = 'The phone field is required.';
        return;
    } else {
        insertForm.errors.phone = null;
    }
    if (insertForm.password === '') {
        insertForm.errors.password = 'The password field is required.';
        return;
    } else {
        insertForm.errors.password = null;
    }
    if (insertForm.confirm_password === '') {
        insertForm.errors.confirm_password = 'The confirm password field is required.';
        return;
    } else {
        insertForm.errors.confirm_password = null;
    }
    if (insertForm.password !== insertForm.confirm_password) {
        insertForm.errors.confirm_password = 'The passwords don\'t match.';
        return;
    } else {
        insertForm.errors.confirm_password = null;
    }
    if (formItems.value.formItems.companyLogo === undefined || formItems.value.formItems.companyLogo === null) {
        insertForm.errors.profileImage = 'You must upload image!';
        return;
    } else {
        insertForm.errors.profileImage = null;
    }

    insertForm.profileImage = formItems.value.formItems.companyLogo.value;
    insertForm.submit('post', '/admin/agencies/insertNew', {
        onSuccess: () => {
            toast.add({
                message: 'Agency is inserted!',
                type: 'success',
            });
        },
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

        <div class="mt-20">
            <div class="mx-auto bg-white rounded-xl shadow-md overflow-hidden w-full">
                <div class="bg-white overflow-hidden dark:bg-gray-800  shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex  justify-between items-center mb-5">
                            <h1 class="text-2xl bold antialiased font-bold">Insert Category</h1>
                            <Link :href="route('showCategory')">
                                <Button>Go Back</Button>
                            </Link>
                        </div>
                        <GenericInput v-model="insertForm.companyName" :error="insertForm.errors.companyName"
                                      input-name="companyName" label="Insert company name"
                                      placeholder="Insert new company"/>
                        <GenericInput v-model="insertForm.email" :error="insertForm.errors.email"
                                      input-name="email" label="Insert company email" placeholder="Insert email"
                                      type="email"/>
                        <GenericInput v-model="insertForm.phone" :error="insertForm.errors.phone"
                                      input-name="companyPhone" label="Insert company phone"
                                      placeholder="Insert phone number"/>
                        <GenericInput v-model="insertForm.password" :error="insertForm.errors.password"
                                      input-name="password" label="Insert password"
                                      placeholder="Insert password"
                                      type="password"/>
                        <GenericInput v-model="insertForm.confirm_password" :error="insertForm.errors.confirm_password"
                                      input-name="password" label="Confirm password"
                                      placeholder="Confirm password"
                                      type="password"/>
                        <FileInput v-model="insertForm.profileImage" :error="insertForm.errors.profileImage"
                                   :is-category-field="true" :valid-types="['image/jpeg', 'image/png']"
                                   input-id="companyLogo"
                                   input-name="companyLogo"
                                   label="Insert company logo"
                                   placeholder="Insert logo"/>
                        <Button class="mt-3" @click="sendInsert">Submit</Button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
