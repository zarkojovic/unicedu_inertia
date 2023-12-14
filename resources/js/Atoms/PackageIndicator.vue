<template>
    <div
        :style="{ color: activePackage.text_color,background:activePackage.primary_color, background: `linear-gradient(to right, ${activePackage.primary_color}, ${activePackage.secondary_color})` }"
        class="package-indicator">
        <span class="package-level font-light">{{ activePackage.package_name }}</span>
    </div>
</template>

<script setup>
import {computed} from 'vue';
import {usePage} from '@inertiajs/vue3';

const page = usePage();

const props = defineProps({
    packageId: {
        type: Number,
        required: true,
    },
});

const activePackage = computed(() => {
    return page.props.packages.filter(el => el.package_id === props.packageId)[0];
});
</script>

<style scoped>
.package-indicator {
    margin-top: 5px;
    padding: 3.5px 10px;
    border-radius: 0px 15px 15px 15px;
    width: fit-content;
    text-transform: uppercase;
    color: #fff;
    letter-spacing: 0.5px;
}
</style>
