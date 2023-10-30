<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import {provide, ref} from 'vue';
import Button from '@/Atoms/Button.vue';
import Modal from '@/Molecules/Modal.vue';
import PackageIndicator from "@/Atoms/PackageIndicator.vue";
import {usePage} from '@inertiajs/vue3';

const page = usePage();

const props = defineProps({
    applications: {
        type: Object,
    },
});

const openModal = ref(false);

const dealId = ref(null);

provide('navBtnType', 'applicationsPage');

const form = useForm({
    deal_id: '',
});

const deleteDeal = () => {
    form.deal_id = dealId.value;

    form.post('/applications/removeDeal', {
        onSuccess: () => {
            openModal.value = false;
        },
    });
};

</script>

<template>
    <Head title="Profile"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Applications</h2>
        </template>

        <div class="py-6 mt-6">
            <div v-for="(deal,index) in applications" v-if="applications.length > 0"
                 id="applicationsContainerHeader" :key="index"
                 class="mx-auto bg-white rounded-3xl shadow-md overflow-hidden p-5 lg:px-8">

                <div
                    class="flex border-b-2">
                    <div class="mr-10">
                        <span class="text-gray-400 font-medium text-md mt-3">Intake</span>
                        <h4 class="text-center mb-5 text-sm md:text-left md:text-xl">{{ deal.intake_name }}</h4>
                    </div>
                    <div>
                        <span class="text-gray-400 font-medium text-md mt-3">Package</span>
                        <PackageIndicator :package-id="page.props.auth.user.package_id"/>
                    </div>
                </div>
                <div class="mt-2">
                    <div v-for="(item,key) in deal.deals" v-if="deal.deals" id="applicationsContainerHeader"
                         :key="key"
                         class="grid grid-cols-4 grid-rows-2 gap-4 pb-5 border-b-2">
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">University</span>
                            <h5 class="text-md">{{ item.university }}</h5>
                        </div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Degree</span>
                            <h5 class="text-md">{{ item.degree }}</h5>
                        </div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Applied</span>
                            <h5 class="text-md">{{ item.created_at }}</h5>
                        </div>
                        <div></div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Program</span>
                            <h5 class="text-md">{{ item.program }}</h5>
                        </div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Status</span>
                            <h5 class="text-md">New Application</h5>
                        </div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Delete</span>
                            <h5 class="text-md">
                                <Button type="danger" @click="openModal = true; dealId= item.deal_id">Delete</Button>
                            </h5>
                        </div>
                    </div>
                    <div v-else>
                        <h1>No applications!</h1>
                    </div>
                    <Modal v-if="openModal" @close="openModal = false">
                        <template #modalTitle>
                            Confirm your action
                        </template>
                        <template #modalContent>
                            <h2>Are you sure you want to delete this application {{ dealId }}?</h2>
                        </template>
                        <template #modalFooter>
                            <div class="flex justify-end">
                                <Button class="me-3" type="muted" @click="openModal = false">Close</Button>
                                <Button type="danger" @click="deleteDeal">Delete</Button>
                            </div>
                        </template>
                    </Modal>

                </div>

            </div>
            <div v-else>
                <h1>No applications yet</h1>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
