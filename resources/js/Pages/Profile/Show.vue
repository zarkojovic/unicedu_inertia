<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import UpdatePasswordForm from "@/Pages/Profile/Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "@/Pages/Profile/Partials/UpdateProfileInformationForm.vue";
import DeleteUserForm from "@/Pages/Profile/Partials/DeleteUserForm.vue";
import ProfileCard from "@/Organisms/ProfileCard.vue";
import CategorySection from "@/Organisms/CategorySection.vue";

defineProps({
    firstName: {
        type: String
    },
    lastName: {
        type: String
    },
    email: {
        type: String
    },
    img: {
        type: String
    },
    categoriesWithFields: {
        type: Object
    }
})

</script>

<template>
    <Head title="Profile"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Profile</h2>
        </template>

        <ProfileCard :firstName="firstName" :lastName="lastName" :email="email" :img="img"/>


        <CategorySection
            v-for="(category,key) in categoriesWithFields"
            :key="key"
            :category-info="category"
        />
    </AuthenticatedLayout>
</template>
