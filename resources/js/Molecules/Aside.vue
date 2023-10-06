<script setup>
import {defineProps, ref} from "vue";
import SidebarLink from "@/Atoms/SidebarLink.vue";

import {Link} from "@inertiajs/vue3";

const {isSidebarOpen, closeSidebar, pages} = defineProps(["isSidebarOpen", "closeSidebar", "pages"]);

const logOutBtn = ref({
    route: '/logout',
    title: 'Logout'
});

</script>
<template>
    <aside id="logo-sidebar" :class="{ 'translate-x-0': isSidebarOpen }"
           class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 lg:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
           aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
            <ul class="space-y-2 font-medium">
                <SidebarLink v-for="(item,key) in pages" :key="key" :model-value="item"/>


            </ul>
            <Link :href="route('logout')" method="post" as="button" type="button">Logout</Link>

        </div>
    </aside>
</template>
