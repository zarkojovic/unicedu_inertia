<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import {computed, provide, ref} from 'vue';
import Button from '@/Atoms/Button.vue';
import Modal from '@/Molecules/Modal.vue';
import PackageIndicator from "@/Atoms/PackageIndicator.vue";
import {usePage} from '@inertiajs/vue3';
import ApplicationModal from "@/Organisms/ApplicationModal.vue";

const page = usePage();

const props = defineProps({
    applications: {
        type: Object,
    },
});

// const getPackageBorderClass = (packageId) => {
//     switch (packageId) {
//         case 1:
//             return 'package-border-1';
//         case 2:
//             return 'package-border-2';
//         case 3:
//             return 'package-border-3';
//         case 4:
//             return 'package-border-4';
//         default:
//             return ''; // Default border class or no class
//     }
// };

const packageClass = computed(() => {
    const classes = {
        'bronze-top-border': props.applications[0].package_id === 1,
        'silver-top-border': props.applications[0].package_id === 2,
        'gold-top-border': props.applications[0].package_id === 3,
        'platinum-top-border': props.applications[0].package_id === 4,
    };

    return classes;
});

const openApplyModal = ref(false);

const openDeleteModal = ref(false);

const dealId = ref(null);

provide('navBtnType', 'applicationsPage');

const form = useForm({
    deal_id: '',
});

const deleteDeal = () => {
    form.deal_id = dealId.value;

    form.post('/applications/removeDeal', {
        onSuccess: () => {
            openDeleteModal.value = false;
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
                 :class="packageClass"
                 class="mx-auto bg-white rounded-3xl shadow-md overflow-hidden p-5 lg:px-8">

                <div
                    class="flex border-b-2">
                    <div class="mr-10">
                        <span class="text-gray-400 font-medium text-md mt-3">Intake</span>
                        <h4 class="text-center mb-5 text-sm md:text-left md:text-xl">{{ deal.intake_name }}</h4>
                    </div>
                    <div>
                        <span class="text-gray-400 font-medium text-md mt-3">Package</span>
                        <PackageIndicator :package-id="deal.package_id"/>
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
                                <Button type="danger" @click="openDeleteModal = true; dealId= item.deal_id">Delete</Button>
                            </h5>
                        </div>
                    </div>
                    <div v-else>
                        <span class="text-md text-slate-500">Looks like you haven't applied yet.
                                    <button class="me-3 text-orange-500" @click="openApplyModal = true">
                                        Apply here!
                                    </button>
                                    <ApplicationModal v-model="openApplyModal"/>
                        </span>
                    </div>
                    <Modal v-if="openDeleteModal" @close="openDeleteModal = false">
                        <template #modalTitle>
                            Confirm your action
                        </template>
                        <template #modalContent>
                            <h2>Are you sure you want to delete this application {{ dealId }}?</h2>
                        </template>
                        <template #modalFooter>
                            <div class="flex justify-end">
                                <Button class="me-3" type="muted" @click="openDeleteModal = false">Close</Button>
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

.bronze-top-border {
    border-top: 5px solid rgb(240,175,110);

}

.silver-top-border {
    border-top: 5px solid #a6b0cd;
}
.gold-top-border {
    border-top: 5px solid rgb(249,222,85);
}

.platinum-top-border {
   border-top: 5px solid rgb(192,205,230);

}


</style>
