<template>
    <div :class="packageClass">
        <span class="package-level font-light">{{ packageTitle }}</span>
    </div>
</template>

<script setup>
import {computed, ref} from 'vue';

const props = defineProps({
    packageId: {
        type: Number,
        required: true,
    },
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
    background: rgb(249, 222, 85);
    background: linear-gradient(90deg, rgba(249, 222, 85, 1) 2%, rgba(230, 194, 0, 1) 99%);
}

.silver-package {
    background: #757F9A; /* fallback for old browsers */
    background: -webkit-linear-gradient(to right, #D7DDE8, #757F9A); /* Chrome 10-25, Safari 5.1-6 */
    background: linear-gradient(to right, #D7DDE8, #757F9A); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
}

.platinum-package {
    background: rgb(192, 205, 230);
    background: linear-gradient(93deg, rgba(192, 205, 230, 1) 0%, rgba(218, 175, 227, 1) 100%);
}

.bronze-package {
    background: rgb(240, 175, 110);
    background: linear-gradient(90deg, rgba(240, 175, 110, 1) 2%, rgba(205, 127, 50, 1) 99%);
}
</style>
