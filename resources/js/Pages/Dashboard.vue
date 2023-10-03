<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import ListInput from "@/Atoms/ListInput.vue";
import {onMounted, ref} from "vue";
import Sidebar from "@/Components/Sidebar.vue";

const radioItems = ['Item 1', 'Item 2', 'Item 3'];
const selectedRadioItem = ref([]);

const {pages} = defineProps(['pages']);

</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
<!--        <template v-slot:sidebar>-->
        <!--            <Sidebar-->
        <!--                :pages="pages"-->
        <!--            />-->
        <!--        </template>-->

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        You are logged in!
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
