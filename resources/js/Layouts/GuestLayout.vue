<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col justify-center items-center pt-6 bg-gradient dark:bg-gray-900 px-3">


        <div
            class="w-full max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden rounded-2xl"
        >
            <div class="flex justify-center">
                    <ApplicationLogo class="w-20 h-30 fill-current text-gray-500 my-2" />
            </div>
            <slot />
        </div>
    </div>
</template>
