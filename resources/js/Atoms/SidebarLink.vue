<script setup>
import {Link, usePage} from '@inertiajs/vue3';
import {computed, ref} from 'vue';

import {Icon} from '@iconify/vue';
import {Popover, PopoverButton, PopoverPanel} from '@headlessui/vue';

const props = defineProps({
    modelValue: {
        type: Object,
    },
});

const item = ref(props.modelValue);

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
        v-if="item.active"
        :class="activeLink ? 'flex items-center rounded-lg p-2 bg-orange-500 text-white hover:bg-orange-500 hover:text-white' : '' + 'flex items-center p-2 text-gray-900 hover:bg-orange-100 rounded-lg dark:text-white hover:text-orange-500 dark:hover:bg-gray-700 group transition'"
        :href="item.route"
    >
        <Icon :icon="'tabler:'+item.icon" class="text-2xl me-2" inline/>
        {{ item.title }}
    </Link>
    <!--    <p v-else-->
    <!--       class="flex items-center p-2 text-gray-900 hover:bg-gray-100 rounded-lg hover:text-gray-500  group transition">-->
    <!--        <Icon :icon="'tabler:'+item.icon" class="text-2xl me-2" inline/>-->
    <!--        {{ item.title }}-->
    <!--    </p>-->
    <Popover v-else class="relative">
        <PopoverButton
            class="flex w-max items-center outline-0 p-2  hover:bg-gray-100 rounded-lg hover:text-gray-500 text-gray-500 custom-100-w group transition"
            style="width: 100%">
            <Icon :icon="'tabler:'+item.icon" class="text-2xl me-2" inline/>
            {{ item.title }}
        </PopoverButton>
        <PopoverPanel class="absolute z-10 bg-orange-50 shadow p-3 rounded-xl">
            <h1>Upgrade to Platinum Package to get these pages!</h1>
        </PopoverPanel>
    </Popover>
</template>

<style scoped>
.custom-100-w {
    width: 100% !important;
}
</style>
