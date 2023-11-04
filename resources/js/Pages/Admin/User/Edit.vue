<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import {onMounted, provide} from 'vue';
import ModelDataDisplay from '@/Organisms/ModelDataDisplay.vue';
import DropdownInput from '@/Atoms/DropdownInput.vue';

const props = defineProps({
    userLogs: {
        type: Object,
    }, userInfo: {
        type: Object,
    }, packages: {
        type: Object,
    },
});

onMounted(() => {
    console.log(props.userLogs);
});

const formItems = useForm({
    formItems: [],
    packages: props.userInfo.package_id,
});

provide('formItems', formItems.value);

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
                            input-name="userPackage"
                            label="Users package"

                        />
                        <ModelDataDisplay :data="props.userLogs"/>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
