<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import ProfileCard from '@/Organisms/ProfileCard.vue';
import CategorySection from '@/Organisms/CategorySection.vue';
import {provide} from 'vue';

defineProps({
    img: String,
    categoriesWithFields: {
        type: Object,
    },
});

provide('navBtnType', 'studentProfile');

</script>

<template>
    <Head title="Profile"/>

    <AuthenticatedLayout>

        <ProfileCard :img="img"/>
        <CategorySection
            v-for="(category,key) in categoriesWithFields"
            :key="key"
            :category-info="category"
        />


    </AuthenticatedLayout>
</template>
