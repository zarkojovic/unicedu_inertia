<template>

    <Nav :toggleSidebar="toggleSidebar" />

    <Aside :isSidebarOpen="isSidebarOpen" @closeSidebar="closeSidebar" :pages="page.props.sidebar_pages"/>

    <Overlay v-if="isSidebarOpen" @click="closeSidebar"/>

</template>
<script setup>
import {inject} from "vue";
import Nav from "@/Molecules/Nav.vue"
import Aside from "@/Molecules/Aside.vue";
import Overlay from "@/Components/Overlay.vue";
import {usePage} from "@inertiajs/vue3";

const page = usePage();

const isSidebarOpen = inject("isSidebarOpen");

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const closeSidebar = () => {
    isSidebarOpen.value = false;
};

const props = defineProps({
    isSidebarOpen: Boolean,
});

</script>
