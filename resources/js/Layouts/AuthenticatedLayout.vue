<script setup>
import {ref, provide} from 'vue';
import Sidebar from '@/Organisms/Sidebar.vue';


const isSidebarOpen = ref(false);
provide("isSidebarOpen", isSidebarOpen);


</script>

<template>

    <Sidebar :isSidebarOpen="isSidebarOpen" />

    <!-- Page Content -->
    <main :class="{ 'blur-sm': isSidebarOpen }"
          class="lg:ml-64 p-5 sm:p-10 md:px-15 lg:px-16 xl:px-36 2xl:px-48 3xl:px-124 ">
        <slot/>
    </main>

</template>
