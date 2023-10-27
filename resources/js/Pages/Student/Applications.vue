<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {provide, ref} from 'vue';

const openModal = ref(false);

provide('navBtnType', 'applicationsPage');

</script>

<template>
    <Head title="Profile"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Applications</h2>
        </template>

        <div class="py-6 mt-6">
        <div class="mx-auto bg-white rounded-3xl shadow-md overflow-hidden p-5 lg:px-8">
                <div id="applicationsContainerHeader" class="flex border-b-2">
                    <div class="mr-10">
                        <span class="text-gray-400 font-medium text-md mt-3">Intake</span>
                        <h4 class="text-center mb-5 text-sm md:text-left md:text-xl">October 2023/2024</h4>
                    </div>
                    <div>
                        <span class="text-gray-400 font-medium text-md mt-3">Package</span>
                        <h4 class="text-center mb-5 text-sm md:text-left md:text-xl">Bronze</h4>
                    </div>
                </div>
                <div id="dealsContainer" class="mt-2">
                    <div class="grid grid-cols-4 grid-rows-2 gap-4 pb-5 border-b-2">
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">University</span>
                            <h5 class="text-md">University of Belgrade </h5>
                        </div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Degree</span>
                            <h5 class="text-md">University of Belgrade </h5>
                        </div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Applied</span>
                            <h5 class="text-md">27.10.2024 13:48</h5>
                        </div>
                        <div></div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Program</span>
                            <h5 class="text-md">University of Belgrade </h5>
                        </div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Status</span>
                            <h5 class="text-md">New Application</h5>
                        </div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Delete</span>
                            <h5 class="text-md">Button</h5>
                        </div>
                        <div></div>
                    </div>
                    <div class="grid grid-cols-4 grid-rows-2 gap-4 pb-5 border-b-2">
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">University</span>
                            <h5 class="text-md">University of Belgrade </h5>
                        </div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Degree</span>
                            <h5 class="text-md">University of Belgrade </h5>
                        </div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Applied</span>
                            <h5 class="text-md">27.10.2024 13:48</h5>
                        </div>
                        <div></div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Program</span>
                            <h5 class="text-md">University of Belgrade </h5>
                        </div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Status</span>
                            <h5 class="text-md">New Application</h5>
                        </div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Delete</span>
                            <h5 class="text-md">Button</h5>
                        </div>
                        <div></div>
                    </div>

                </div>
        </div>
    </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
