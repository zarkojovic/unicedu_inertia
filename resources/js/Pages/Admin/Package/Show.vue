<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {ref} from 'vue';
import PackagePagesSelection from '@/Organisms/PackagePagesSelection.vue';

const props = defineProps({
    packagePages: {
        type: Object,
    },
});

const val = ref([]);

const packages = ref(props.packagePages);
</script>


<template>
    <Head title="Profile"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Package</h2>
        </template>

        <div class="mt-20">
            <div class="mx-auto bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-white overflow-hidden dark:bg-gray-800 shadow-sm sm:rounded-lg p-10">
                    <h1 class="text-2xl font-bold mb-4">Packages</h1>
                    <!--                    <PackageTabs :data="packagePages"/>-->
                    <div v-for="item in packages">
                        <PackagePagesSelection :data="item"/>
                    </div>


                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
