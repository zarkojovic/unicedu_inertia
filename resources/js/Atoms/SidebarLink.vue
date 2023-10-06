<script setup>
import {Link, usePage} from "@inertiajs/vue3";
import {computed, onMounted, ref} from "vue";
import {
    IconDashboard,
    IconWallpaper,
    IconBoxMultiple,
    IconRowInsertTop,
    IconUsers,
    IconApiApp,
    IconUser,
    IconSchool,
    IconLogout
} from '@tabler/icons-vue';
import {defineAsyncComponent} from 'vue'


const props = defineProps({
    modelValue: {
        type: Object
    }
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
})

const page = usePage();

const activeLink = computed(() => {
    return props.modelValue.route.includes(page.props.current_route_uri);
});

</script>

<template>

    <Link
        :href="item.route"
        :class="activeLink ? 'flex items-center rounded-lg p-2 bg-orange-500 text-white hover:bg-orange-500 hover:text-white' : '' + 'flex items-center p-2 text-gray-900 hover:bg-orange-100 rounded-lg dark:text-white hover:text-orange-500 dark:hover:bg-gray-700 group transition'"
    >
        <component v-if="iconComponentName === 'IconDashboard'" :is="IconDashboard" class="me-3"/>
        <component v-else-if="iconComponentName === 'IconWallpaper'" :is="IconWallpaper" class="me-3"/>
        <component v-else-if="iconComponentName === 'IconBoxMultiple'" :is="IconBoxMultiple" class="me-3"/>
        <component v-else-if="iconComponentName === 'IconRowInsertTop'" :is="IconRowInsertTop" class="me-3"/>
        <component v-else-if="iconComponentName === 'IconUsers'" :is="IconUsers" class="me-3"/>
        <component v-else-if="iconComponentName === 'IconApiApp'" :is="IconApiApp" class="me-3"/>
        <component v-else-if="iconComponentName === 'IconUser'" :is="IconUser" class="me-3"/>
        <component v-else-if="iconComponentName === 'IconSchool'" :is="IconSchool" class="me-3"/>
        <component v-else-if="iconComponentName === 'IconLogout'" :is="IconLogout" class="me-3"/>

        {{ item.title }}

    </Link>
</template>

<style scoped>

</style>
