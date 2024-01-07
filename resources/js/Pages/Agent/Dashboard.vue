<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import ProfileCard from '@/Organisms/ProfileCard.vue';

defineProps({
    img: String,
});


</script>

<template>
    <Head title="Profile"/>

    <AuthenticatedLayout>


        <ProfileCard :img="img"/>
        <div class="flex flex-col items-center justify-center">
            <h1 class="text-3xl font-bold text-gray-900">Welcome to the Agent Dashboard</h1>
            <p class="text-xl text-gray-600">You are logged in!</p>
        </div>
    </AuthenticatedLayout>
</template>
