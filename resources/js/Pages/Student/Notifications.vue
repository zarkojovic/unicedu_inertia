<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import NotificationTab from '@/Atoms/NotificationTab.vue';
import {onMounted, provide} from 'vue';
import Pagination from '@/Atoms/Pagination.vue';

provide('navBtnType', 'studentProfile');

const props = defineProps({
    notifications: {
        type: Array,
    },
});

onMounted(() => {
    console.log(props.notifications);
});
</script>

<template>
    <Head title="Deal Detail"/>

    <AuthenticatedLayout>


        <div class="py-6 mt-10">
            <h1 class="text-3xl font-bold mb-2">Notifications</h1>
            <div class="mx-auto bg-white rounded-3xl shadow-md overflow-hidden ">
                <div v-if="props.notifications.data.length  ">
                    <NotificationTab v-for="notification in props.notifications.data"
                                     :notification="notification"/>
                    <Pagination :links="props.notifications.links"/>
                </div>
                <div v-else class="p-5">
                    <p class="text-center">No notifications yet.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
