<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        You are logged in!
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
