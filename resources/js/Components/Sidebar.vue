<template>
    <div class="h-screen w-100 p-5 border-r border-gray-200 bg-white">
        <div class="flex items-center justify-center mb-5">
            <Link :href="route('dashboard')">
                <ApplicationLogo width="80"
                />
            </Link>

        </div>

        <SidebarLink v-for="(item,key) in pages" :key="key" :model-value="item"/>


    </div>
</template>

<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import {Link} from "@inertiajs/vue3";
import {ref} from "vue";
import SidebarLink from "@/Atoms/SidebarLink.vue";

const {pages} = defineProps(['pages']);

</script>
