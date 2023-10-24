<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import ListInput from "@/Atoms/ListInput.vue";
import {onMounted, ref} from "vue";
const radioItems = ['Item 1', 'Item 2', 'Item 3'];
const selectedRadioItem = ref([]);

</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
<!--        <template v-slot:sidebar>-->
        <!--            <Sidebar-->
        <!--                :pages="pages"-->
        <!--            />-->
        <!--        </template>-->


        <div class="py-12">
            <div class="mx-auto bg-white rounded-xl shadow-md overflow-hidden w-5/6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        You are logged in!
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
