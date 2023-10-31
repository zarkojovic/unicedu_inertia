<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import PackageTabs from '@/Organisms/PackageTabs.vue';

const props = defineProps({
    packagePages: {
        type: Object,
    },
});
</script>


<template>
    <Head title="Profile"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Package</h2>
        </template>

        <div class="mt-20">
            <div class="mx-auto bg-white rounded-xl shadow-md overflow-hidden w-5/6">
                <div class="bg-white overflow-hidden dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <PackageTabs :data="packagePages"/>
                    <!--                    <h1>Pozzz</h1>-->
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
