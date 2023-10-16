<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from "@inertiajs/vue3";
import Modal from "@/Molecules/Modal.vue";
import Button from "@/Atoms/Button.vue";
import {ref} from "vue";

const openModal = ref(false);

</script>

<template>
    <Head title="Profile"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Applications</h2>
        </template>

        <div class="mt-20">
            <div class="mx-auto bg-white rounded-xl shadow-md overflow-hidden w-5/6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <Button @click="openModal = true">
                            Open modal
                        </Button>
                        <Modal v-if="openModal" @close="openModal = false"
                        >
                            <template v-slot:modalTitle>Create new Deal</template>
                        </Modal>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<style scoped>

</style>
