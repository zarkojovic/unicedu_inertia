<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {onMounted, provide, ref} from 'vue';
import Button from '@/Atoms/Button.vue';

const props = defineProps({
    applications: {
        type: Object,
    },
});

const openModal = ref(false);

provide('navBtnType', 'applicationsPage');

onMounted(() => {
    // console.log(props.applications[0].deals);
});

</script>

<template>
    <Head title="Profile"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Applications</h2>
        </template>

        <div class="py-6 mt-6">
            <div v-for="(deal,index) in applications"
                 id="applicationsContainerHeader" :key="index"
                 class="mx-auto bg-white rounded-3xl shadow-md overflow-hidden p-5 lg:px-8">
                <div
                    class="flex border-b-2">
                    <div class="mr-10">
                        <span class="text-gray-400 font-medium text-md mt-3">Intake</span>
                        <h4 class="text-center mb-5 text-sm md:text-left md:text-xl">{{ deal.intake_name }}</h4>
                    </div>
                    <div>
                        <span class="text-gray-400 font-medium text-md mt-3">Package</span>
                        <h4 class="text-center mb-5 text-sm md:text-left md:text-xl">{{ deal.package_name }}</h4>
                    </div>
                </div>
                <div id="dealsContainer" class="mt-2">

                    <div v-for="(item,key) in deal.deals" id="applicationsContainerHeader" :key="key"
                         class="grid grid-cols-4 grid-rows-2 gap-4 pb-5 border-b-2">
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">University</span>
                            <h5 class="text-md">{{ item.university }}</h5>
                        </div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Degree</span>
                            <h5 class="text-md">{{ item.degree }}</h5>
                        </div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Applied</span>
                            <h5 class="text-md">{{ item.created_at }}</h5>
                        </div>
                        <div></div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Program</span>
                            <h5 class="text-md">{{ item.program }}</h5>
                        </div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Status</span>
                            <h5 class="text-md">New Application</h5>
                        </div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Delete</span>
                            <h5 class="text-md">
                                <Button type="danger">Delete</Button>
                            </h5>
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
