<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import CategorySection from '@/Organisms/CategorySection.vue';
import {provide} from 'vue';

defineProps({
    categoriesWithFields: {
        type: Object,
    },
    title: {
        type: String,
    },
});
provide('navBtnType', 'studentProfile');


</script>

<template>
    <Head :title="title"/>
    <AuthenticatedLayout>
        <div class="mt-16"></div>
        <CategorySection
            v-for="(category,key) in categoriesWithFields"
            :key="key"
            :category-info="category"
        />
    </AuthenticatedLayout>
</template>
