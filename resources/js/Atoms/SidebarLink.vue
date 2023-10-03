<script setup>
import {Link} from "@inertiajs/vue3";
import {ref} from "vue";

const {modelValue} = defineProps([
    'modelValue'
]);

const item = ref(modelValue);

</script>

<template>

    <Link
        :href="item.href"
        class="w-full px-3 py-4 flex overflow-y-auto rounded-lg hover:bg-orange-500 hover:text-white mb-2 transition-all ease-in">
        {{ item.title }}
    </Link>

</template>

<style scoped>

</style>
