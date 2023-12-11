<template>
    <div
        :style="{ color: activePackage.text_color,background:activePackage.primary_color, background: `linear-gradient(to right, ${activePackage.primary_color}, ${activePackage.secondary_color})` }"
        class="package-indicator">
        <span class="package-level font-light">{{ activePackage.package_name }}</span>
    </div>
</template>

<script setup>
import {computed, onMounted, ref} from 'vue';
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

onMounted(() => {
    console.log(activePackage.value);
});

const packageTitle = ref(null);

const packageClass = computed(() => {
    const classes = {
        'package-indicator': true,
        'bronze-package': props.packageId === 1,
        'silver-package': props.packageId === 2,
        'gold-package': props.packageId === 3,
        'platinum-package': props.packageId === 4,
    };

    switch (props.packageId) {
        case 1:
            packageTitle.value = 'Bronze';
            break;
        case 2:
            packageTitle.value = 'Silver';
            break;
        case 3:
            packageTitle.value = 'Gold';
            break;
        case 4:
            packageTitle.value = 'Platinum';
            break;
        default:
            packageTitle.value = 'Bronze';
    }

    return classes;
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

.gold-package {
    background: #f5df6c;
    background: linear-gradient(to right, #f5df6c, #e0c340);
}

.silver-package {
    background: #757f9a; /* fallback for old browsers */
    background: -webkit-linear-gradient(to right, #d7dde8, #757f9a); /* Chrome 10-25, Safari 5.1-6 */
    background: linear-gradient(to right, #d7dde8, #757f9a); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
}

.platinum-package {
    background: #c2cde4;
    background: linear-gradient(to right, #c2cde4, #d3b1df);
}

.bronze-package {
    background: #e6b278;
    background: linear-gradient(to right, #e6b278, #c28342);
}
</style>
