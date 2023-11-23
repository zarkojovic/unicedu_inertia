<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import {provide, ref, watch} from 'vue';
import ModelDataDisplay from '@/Organisms/ModelDataDisplay.vue';
import DropdownInput from '@/Atoms/DropdownInput.vue';
import Button from '@/Atoms/Button.vue';

const props = defineProps({
    userLogs: {
        type: Object,
    }, userInfo: {
        type: Object,
    }, packages: {
        type: Object,
    }, roles: {
        type: Object,
    },
});

const formItems = ref({
    formItems: {},
    package_id: null,
    role_id: null,
    user_id: props.userInfo.id,
});

const form = useForm(formItems.value);

const isPackageChanged = ref(null);

const isRoleChanged = ref(null);

watch(formItems.value.formItems, function(value, oldValue) {
    const userRole = value.userRole;
    const userPackage = value.userPackage;
    
    if (userRole) {
        isRoleChanged.value = props.userInfo.role_id.toString() !== userRole.value;
        form.role_id = isRoleChanged.value ? userRole.value : form.role_id;
    }
    if (userPackage) {
        isPackageChanged.value = props.userInfo.package_id.toString() !== userPackage.value;
        form.package_id = isPackageChanged.value ? userPackage.value : form.package_id;
    }

});

provide('formItems', formItems);

const changeUserPackage = () => {
    form.post('/admin/users/change-user-package', {
        onSuccess: () => {
            isPackageChanged.value = false;
        },

    });
};
const changeUserRole = () => {
    form.post('/admin/users/change-user-role', {
        onSuccess: () => {
            isRoleChanged.value = false;
        },

    });
};

</script>


<template>
    <Head title="Edit user"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">User Profile</h2>
        </template>

        <div class="mt-20">
            <div class="mx-auto bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-white overflow-hidden dark:bg-gray-800  shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between flex-wrap">
                            <h1 class="text-2xl font-bold mb-4">Student - {{
                                    userInfo.first_name + ' ' + userInfo.last_name
                                }}</h1>
                            <Link :href="route('showUser')">
                                <Button>Go Back</Button>
                            </Link>
                        </div>
                        <DropdownInput
                            :options="props.packages"
                            :selected-item="props.userInfo.package_id"
                            input-name="userPackage"
                            label="Users package"
                        />
                        <Button v-if="isPackageChanged" class="mt-3" @click="changeUserPackage">Change Package</Button>
                        <div class="mt-5"></div>
                        <DropdownInput
                            :options="props.roles"
                            :selected-item="props.userInfo.role_id"
                            input-name="userRole"
                            label="Users roles"
                        />
                        <Button v-if="isRoleChanged" class="mt-3" @click="changeUserRole">Change Role</Button>
                        <h3 class="h3 font-bold mt-4 mb-0">User History</h3>
                        <ModelDataDisplay :data="props.userLogs"/>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
