<template>
    <div v-if="links.length > 3">
        <div class="flex  my-5 ms-4">
            <template v-for="(link, p) in links" :key="p">
                <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded"
                     v-html="link.label"/>
                <Link v-else
                      :class="{ 'bg-orange-700 text-white': link.active }"
                      :href="link.url"
                      class="mr-1 mb-1 px-4 py-3 text-sm flex leading-4 border rounded  focus:border-orange-500 focus:text-orange-500"
                      v-html="link.label"/>
            </template>
        </div>
    </div>
</template>

<script setup>
import {Link} from '@inertiajs/vue3';

const props = defineProps({
    links: Array,
});

</script>
