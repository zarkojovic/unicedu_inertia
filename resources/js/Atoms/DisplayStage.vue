<script setup>
import {computed} from 'vue';

const props = defineProps({
    stageName: {
        type: String,
    },
});

const stageClass = computed(() => {
    switch (props.stageName) {
        case 'New Application':
            return 'bg-red-400 text-white';
        case 'Applied':
            return 'bg-cyan-400 text-black';
        case 'Accepted, pending payment':
            return 'bg-purple-400 text-white';
        case 'Paid':
            return 'bg-yellow-400 text-black';
        case 'Visa letter':
            return 'bg-lime-400 text-black';
        case 'Visa Received':
            return 'bg-blue-400 text-white';
        case 'Arrived':
            return 'bg-green-400 text-white';
        case 'Visa Refusal':
            return 'bg-green-400 text-white';
        case 'Application rejected':
            return 'bg-green-400 text-white';
        case 'Refund application sent to the university':
            return 'bg-green-400 text-white';
        case 'Resigned':
            return 'bg-green-400 text-white';
        case 'Tuition fee Refunded':
            return 'bg-green-400 text-white';
        case 'Went back home':
            return 'bg-green-400 text-white';
    }
});

</script>
<template>
    <div class="mt-2">
    <span :class="stageClass" class="px-3 py-2 bg-li text-sm rounded-3xl whitespace-nowrap">
        {{ props.stageName }}
    </span></div>
</template>

<style scoped>

</style>
