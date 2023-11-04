<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
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
    },
});

const formItems = ref({
    formItems: {},
    package_id: null,
    user_id: props.userInfo.id,
});

const form = useForm(formItems.value);

const isPackageChanged = ref(null);

watch(formItems.value.formItems, function(value, oldValue) {

    if (props.userInfo.package_id.toString() === value.userPackage.value) {
        isPackageChanged.value = false;
    } else {
        form.package_id = value.userPackage.value;
        isPackageChanged.value = true;
    }
});

provide('formItems', formItems);

const changeUserPackage = () => {

    form.post('/admin/intakes/change-user-package', {});
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
                        <DropdownInput
                            :options="props.packages"
                            :selected-item="props.userInfo.package_id"
                            input-name="userPackage"
                            label="Users package"
                        />
                        <Button v-if="isPackageChanged" class="mt-3" @click="changeUserPackage">Change Package</Button>
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
