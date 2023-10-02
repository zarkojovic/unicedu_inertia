<script setup>

import {onUnmounted, ref} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import toast from '@/Stores/toast';
import Toast from '@/Atoms/Toast.vue';

const page = usePage();

const removeFinishEventListener = router.on('finish', () => {
    if (page.props.toast) {
        toast.add({
            message: page.props.toast.message,
            type: page.props.toast.type
        })
    }
});

onUnmounted(() => removeFinishEventListener());

const remove = (index) => {
    toast.remove(index);
}

</script>

<template>
    <TransitionGroup
        tag="div"
        enter-from-class="translate-x-full opacity-0"
        enter-active-class="duration-500 transition"
        move-class="transition-all duration-200"
        leave-active-class="duration-500 transition"
        leave-to-class="translate-x-full opacity-0"
        class="fixed top-4 right-4 z-50 max-w-xs"
    >
        <Toast v-for="item in toast.items"
               :message="item.message"
               :type="item.type"
               :duration="item.duration"
               :key="item.key"
               @remove="remove(item.key)"
        />
    </TransitionGroup>
</template>

<style scoped>

</style>
