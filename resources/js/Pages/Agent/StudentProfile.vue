<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
</script>

<template>
    <Head title="Profile"/>

    <AuthenticatedLayout>

        <div class="py-6 mt-10">
            <div class="mx-auto bg-white rounded-3xl shadow-md overflow-hidden ">
                <h1 class="text-3xl font-bold p-3 mb-2">Student</h1>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
