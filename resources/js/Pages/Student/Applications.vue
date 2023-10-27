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
    // console.log(props.applications);
});

</script>

<template>
    <Head title="Profile"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Applications</h2>
        </template>

        <div class="mt-20">
            <div class="mx-auto bg-white rounded-xl shadow-md overflow-hidden w-5/6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div v-for="(item,index) in props.applications" :key="index"
                         class="grid grid-cols-3 gap-4 bg-blue-100 mb-4">
                        <div class=" p-4"><h2>Program</h2>
                            <h1
                                class="text-xl bold antialiased font-bold">
                                {{ item.program }}
                            </h1>
                        </div>
                        <div class=" p-4">
                            <h2>Intake</h2>
                            <h1
                                class="text-xl bold antialiased font-bold">
                                {{ item.intake }}
                            </h1>
                        </div>
                        <div class=" p-4">
                            <h2>Intake</h2>
                            <h1
                                class="text-xl bold antialiased font-bold">
                                {{ item.university }}
                            </h1>
                        </div>
                        <div class=" p-4">
                            <h2>Intake</h2>
                            <h1
                                class="text-xl bold antialiased font-bold">
                                {{ item.degree }}
                            </h1>
                        </div>
                        <div class=" p-4">
                            <h2>Delete this application</h2>
                            <Button type="danger">Delete</Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
