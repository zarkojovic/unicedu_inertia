<script setup>
import {useDark, useToggle} from "@vueuse/core";

const isDark = useDark();
const toggleDark = useToggle(isDark);
</script>

<template>
    <button @click="toggleDark()"
            class="text-orange-700 dark:text-orange-400 font-semibold py-2 px-4  rounded-md">
        Switch theme
    </button>
</template>
