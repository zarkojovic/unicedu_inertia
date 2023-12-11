<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import Button from '@/Atoms/Button.vue';
import GenericInput from '@/Atoms/GenericInput.vue';
import ListInput from '@/Atoms/ListInput.vue';
import {computed} from 'vue';

const props = defineProps({
    data: {
        type: Object,
    },
    columns: {
        type: Object,
    },
    roles: {
        type: Object,
    },
    categories: {
        type: Object,
    },
    editPage: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    title: props.editPage !== null ? props.editPage.title : '',
    route: props.editPage !== null ? props.editPage.route : '',
    categories: props.editPage !== null ? props.editPage.categories.map(obj => String(obj.field_category_id)) : [],
    role_id: props.editPage !== null ? props.editPage.role_id.toString() : [],
    id: props.editPage !== null ? props.editPage.page_id : null,
    icon: props.editPage !== null ? props.editPage.icon : '',
});

const isEdit = computed(() => {
    return props.editPage !== null;
});

const isStudent = computed(() => {
    if (form.role_id == '1') {
        return 1;
    }
    return 0;
});

const createRoute = '/admin/pages/insertNew';
const editRoute = '/admin/pages/update';

const isAdmin = computed(() => {
    if (form.role_id === '3') {
        return 1;
    }
    return 0;
});

const submit = () => {
    let check = true;
    if (isAdmin.value) {
        var regex = new RegExp('/admin/.*');
        if (!regex.test(form.route)) {
            check = false;
            form.errors.route = 'It must start with /admin!';
        } else {
            check = true;
            form.errors.route = null;
        }
    }
    if (check) {
        form.post(isEdit.value ? editRoute : createRoute, {
            onSuccess: () => {
            },
        });
    }
};

</script>


<template>
    <Head title="Insert Page"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Admin Panel</h2>
        </template>

        <div class="mt-20">
            <div class="mx-auto bg-white rounded-xl shadow-md overflow-hidden w-5/6">
                <div class="bg-white overflow-hidden dark:bg-gray-800  shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex  justify-between items-center mb-5">
                            <h1 class="text-2xl bold antialiased font-bold">{{
                                    isEdit
                                        ? 'Update page - ' + form.title
                                        : 'Insert Page'
                                }}</h1>
                            <Link :href="route('showPage')">
                                <Button>Go Back</Button>
                            </Link>
                        </div>
                        <form>
                            <GenericInput v-model="form.title" :error="form.errors.title" :is_required='true'
                                          helper="This a title that will be displayed in the Sidebar link."
                                          label="Insert page name"/>
                            <GenericInput v-model="form.route" :error="form.errors.route"
                                          :is_required='true' class="mt-4"
                                          helper="It should start with /. Example: /test"
                                          label="Insert page route"/>
                            <GenericInput v-model="form.icon" :error="form.errors.icon"
                                          :is_required='true' class="mt-4"
                                          helper="Name should be without 'tabler:' prefiex (tabler:user should be just user)"
                                          label="Insert icon name"/>
                            <a class="text-orange-500 underline text-sm" href="https://icon-sets.iconify.design/tabler/"
                               target="_blank">Browse</a>
                            <ListInput v-model="form.role_id"
                                       :error="form.errors.role_id"
                                       :is_required="true"
                                       :items="props.roles"
                                       class="mt-4"
                                       label="Select roles"
                                       name="role_radio"
                                       type="radio"
                            />
                            <ListInput v-if="isStudent" v-model="form.categories" :is_required="true"
                                       :items="props.categories"
                                       class="mt-4"
                                       label="Select categories you want to display here"
                                       type="checkbox"
                            />
                            <Button :disabled="form.processing" class="mt-5" @click="submit">{{
                                    isEdit
                                        ? 'Update this page'
                                        : 'Add new Page'
                                }}
                            </Button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
