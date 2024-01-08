<script setup>
import {onMounted} from 'vue';

const props = defineProps({
    notification: {
        type: Object,
    },
});

onMounted(() => {
    console.log(props.notification);
});
</script>

<template>

    <div :class="!props.notification.is_read ? 'bg-green-100' : ''" class="border-b pb-3 p-5 lg:px-8 ">
        <p>
            {{ props.notification.message }}
        </p>
        <span class="italic text-gray-500 text-sm">{{
                props.notification.formatted_created_at
            }}</span>
        <a v-if="props.notification.url" :href="props.notification.url" class="text-orange-500 block mt-3">
            View Details
        </a>
    </div>
</template>

<style scoped>

</style>
