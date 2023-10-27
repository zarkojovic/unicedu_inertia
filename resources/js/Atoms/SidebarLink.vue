<script setup>
import {Link, usePage} from '@inertiajs/vue3';
import {computed, defineAsyncComponent, ref} from 'vue';

import {Icon} from '@iconify/vue';

const props = defineProps({
    modelValue: {
        type: Object,
    },
});

// Dynamically import the Tabler Icon component based on the received icon name
const AsyncComp = defineAsyncComponent(() =>
    import('@tabler/icons-vue'),
);
const item = ref(props.modelValue);

const iconComponentName = computed(() => {
    if (props.modelValue.icon !== null) {
        var words = props.modelValue.icon.trim().split(' ').slice(1); // Remove the "ti" prefix and trim whitespace

        words = words[0].split('-');
        const camelCasedWords = words.map((word, index) => {
            if (index === 0) {
                // Remove the "ti-" prefix and keep it in lowercase
                return word.slice(3);
            } else {
                // Capitalize the first letter of subsequent words
                return word.charAt(0).toUpperCase() + word.slice(1);
            }
        });

        return 'Icon' + camelCasedWords.join('');
    } else {
        return null;
    }
});

const page = usePage();

const activeLink = computed(() => {
    if (page.props.current_route_uri === '/') {
        return props.modelValue.route.includes('/profile');
    }
    return props.modelValue.route.includes(page.props.current_route_uri);
});

</script>

<template>

    <Link
        :class="activeLink ? 'flex items-center rounded-lg p-2 bg-orange-500 text-white hover:bg-orange-500 hover:text-white' : '' + 'flex items-center p-2 text-gray-900 hover:bg-orange-100 rounded-lg dark:text-white hover:text-orange-500 dark:hover:bg-gray-700 group transition'"
        :href="item.route"
    >
        <Icon :icon="'tabler:'+item.icon" class="text-2xl me-2" inline/>
        {{ item.title }}

    </Link>
</template>

<style scoped>

</style>
