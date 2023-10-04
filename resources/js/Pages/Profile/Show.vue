<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import UpdatePasswordForm from "@/Pages/Profile/Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "@/Pages/Profile/Partials/UpdateProfileInformationForm.vue";
import DeleteUserForm from "@/Pages/Profile/Partials/DeleteUserForm.vue";

</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>


        <div class="py-12">
            <div class="mx-auto bg-white rounded-xl shadow-md overflow-hidden w-5/6">
                <div class="md:flex">
                    <div class="md:flex-shrink-0">
                        <img class="h-48 w-full object-cover md:w-48" src="public/profile/original/profile.jpg" alt="Student profile image"/>
                    </div>
                    <div class="p-8">
                        <p class="text-lg tracking-wide font-semibold">Student Student</p>
                        <a href="#" class="block mt-1 text-md leading-tight font-medium text-gray-400 hover:underline">email@gmail.com</a>
                        <p class="mt-2 text-gray-500">PACKAGE</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
