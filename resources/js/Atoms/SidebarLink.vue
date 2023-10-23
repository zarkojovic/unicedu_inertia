<script setup>
import {Link, usePage} from '@inertiajs/vue3';
import {computed, defineAsyncComponent, ref} from 'vue';
import {
    IconApiApp,
    IconBoxMultiple,
    IconDashboard,
    IconLogout,
    IconRowInsertTop,
    IconSchool,
    IconUser,
    IconUsers,
    IconWallpaper,
} from '@tabler/icons-vue';

const props = defineProps({
    modelValue: {
        type: Object,
    },
});

// Dynamically import the Tabler Icon component based on the received icon name
const iconComponent = computed(() => {

    return defineAsyncComponent(() => import(`tabler-icons-vue/${iconComponentName.value}.vue`).catch(() => {
    })); // Handle errors gracefully
});

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
        <component :is="IconDashboard" v-if="iconComponentName === 'IconDashboard'" class="me-3"/>
        <component :is="IconWallpaper" v-else-if="iconComponentName === 'IconWallpaper'" class="me-3"/>
        <component :is="IconBoxMultiple" v-else-if="iconComponentName === 'IconBoxMultiple'" class="me-3"/>
        <component :is="IconRowInsertTop" v-else-if="iconComponentName === 'IconRowInsertTop'" class="me-3"/>
        <component :is="IconUsers" v-else-if="iconComponentName === 'IconUsers'" class="me-3"/>
        <component :is="IconApiApp" v-else-if="iconComponentName === 'IconApiApp'" class="me-3"/>
        <component :is="IconUser" v-else-if="iconComponentName === 'IconUser'" class="me-3"/>
        <component :is="IconSchool" v-else-if="iconComponentName === 'IconSchool'" class="me-3"/>
        <component :is="IconLogout" v-else-if="iconComponentName === 'IconLogout'" class="me-3"/>
        <component :is="IconWallpaper" v-else class="me-3"/>

        {{ item.title }}

    </Link>
</template>

<style scoped>

</style>
