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

        <div class="py-6 mt-6">
            <div class="mx-auto bg-white shadow-lg overflow-hidden rounded-3xl">
                <div class="bg-white overflow-hidden dark:bg-gray-800 shadow-sm sm:rounded-lg p-10">
                    <!--
                                       <PackageTabs :data="packagePages"/>-->
                    <div
                        class="grid grid-cols-1 grid-rows-4 sm:grid-cols-2 sm:grid-rows-2 xl:grid-rows-1 xl:grid-cols-4 gap-2">

                        <div v-for="item in packages">
                            <PackagePagesSelection :data="item"/>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
