<script setup>
import {provide, ref} from 'vue';
import Sidebar from '@/Organisms/Sidebar.vue';
import ToastList from '@/Molecules/ToastList.vue';
import SaleBot from '@/Atoms/SaleBot.vue';

const isSidebarOpen = ref(false);
provide('isSidebarOpen', isSidebarOpen);

</script>

<template>
    <ToastList/>
    <Sidebar :isSidebarOpen="isSidebarOpen"/>
    <SaleBot/>
    <!-- Page Content -->
    <main :class="{ 'blur-x': isSidebarOpen }"
          class="lg:ml-64 p-5 sm:p-10 md:px-15 lg:px-16 xl:px-36 2xl:px-48 3xl:px-124 bg-gray-50 min-h-screen">
        <slot/>
    </main>

</template>

<style>
.grecaptcha-badge { visibility: hidden; }

</style>
