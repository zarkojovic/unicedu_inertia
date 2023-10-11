<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import UpdatePasswordForm from "@/Pages/Profile/Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "@/Pages/Profile/Partials/UpdateProfileInformationForm.vue";
import DeleteUserForm from "@/Pages/Profile/Partials/DeleteUserForm.vue";
import ProfileCard from "@/Organisms/ProfileCard.vue";
import CategorySection from "@/Organisms/CategorySection.vue";

defineProps({
    img: String,
    categoriesWithFields: {
        type: Object
    }
})

</script>

<template>
    <Head title="Profile"/>

    <AuthenticatedLayout>


        <ProfileCard :email="email" :img="img" :csrfToken="csrfToken"/>


        <CategorySection
            v-for="(category,key) in categoriesWithFields"
            :key="key"
            :category-info="category"
        />
    </AuthenticatedLayout>
</template>
