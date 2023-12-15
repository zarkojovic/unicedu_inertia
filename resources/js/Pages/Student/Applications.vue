<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm, usePage} from '@inertiajs/vue3';
import {onMounted, provide, ref} from 'vue';
import Button from '@/Atoms/Button.vue';
import Modal from '@/Molecules/Modal.vue';
import PackageIndicator from '@/Atoms/PackageIndicator.vue';
import ApplicationModal from '@/Organisms/ApplicationModal.vue';
import DisplayStage from '@/Atoms/DisplayStage.vue';

const page = usePage();

const props = defineProps({
    applications: {
        type: Object,
    },
    isModalOpen: {
        type: Boolean,
        default: false,
    },
});

const packageClass = (package_id) => {
    switch (package_id) {
        case 1:
            return 'bronze-top-border';
        case 2:
            return 'silver-top-border';
        case 3:
            return 'gold-top-border';
        case 4:
            return 'platinum-top-border';
        default:
            return ''; // Default border class or no class
    }
};

const openApplyModal = ref(false);

const openDeleteModal = ref(false);

const dealId = ref(null);

provide('navBtnType', 'applicationsPage');

const form = useForm({
    deal_id: '',
});

onMounted(() => {
    if (props.isModalOpen) {
        openApplyModal.value = true;
    }

});

const deleteDeal = () => {
    form.deal_id = dealId.value;

    form.post('/applications/removeDeal', {
        onSuccess: () => {
            openDeleteModal.value = false;
        },
    });
};

const syncForm = useForm({});

const syncData = () => {
    console.log('aloooo');
    syncForm.post('/user/sync-deal-fields');
};

</script>

<template>
    <Head title="Applications"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Applications</h2>
        </template>
        <div class="py-6 mt-10">
            <Button v-if="page.props.auth.user.unsaved_changes" class="text-3xl mb-4" @click="syncData">
                Synchronize data to bitrix
            </Button>
            <div v-for="(deal,index) in applications" v-if="applications.length > 0"
                 id="applicationsContainerHeader" :key="index"
                 :class="packageClass(deal.package_id)"
                 class="mx-auto bg-white rounded-3xl shadow-md overflow-hidden p-5 lg:px-8 mb-5">

                <div
                    class="flex border-b-2">
                    <div class="mr-10">
                        <span class="text-gray-400 font-medium text-md mt-3">Intake</span>
                        <h4 class="mb-5 text-md text-left md:text-xl">{{ deal.intake_name }}</h4>
                    </div>
                    <div>
                        <span class="text-gray-400 font-medium text-md mt-3">Package</span>
                        <PackageIndicator :package-id="deal.package_id"/>
                    </div>
                </div>
                <div class="mt-2">
                    <div v-for="(item,key) in deal.deals" v-if="deal.deals" id="applicationsContainerHeader"
                         :key="key"
                         class="grid sm:grid-cols-4 sm:grid-rows-2 gap-4 pb-5 border-b-2">
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
                            <h5 class="text-md">
                                <DisplayStage
                                    :stage-name="item.stage_name"
                                />
                            </h5>
                        </div>
                        <div class="pt-4">
                            <span class="text-gray-400 font-medium text-md mt-3">Delete </span>
                            <h5 v-if="item.stage_id === 1" class="text-md">
                                <Button type="danger" @click="openDeleteModal = true; dealId= item.deal_id">Delete
                                </Button>
                            </h5>
                            <h5 v-else class="text-md">
                                You can't delete this!
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
                            <h2>Are you sure you want to delete this application?</h2>
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
            <div v-else
                 id="applicationsContainerHeader"
                 :class="packageClass(page.props.auth.user.package_id)"
                 class="mx-auto bg-white rounded-3xl shadow-md overflow-hidden p-5 lg:px-8 mb-5">

                <div
                    class="flex border-b-2">
                    <div class="mr-10">
                        <span class="text-gray-400 font-medium text-md mt-3">Intake</span>
                        <h4 class="text-center mb-5 text-sm md:text-left md:text-xl">
                            {{ page.props.active_intake.intake_name }}</h4>
                    </div>
                    <div>
                        <span class="text-gray-400 font-medium text-md mt-3">Package</span>
                        <PackageIndicator :package-id="page.props.auth.user.package_id"/>
                    </div>
                </div>
                <div class="mt-2">
                        <span class="text-md text-slate-500">Looks like you haven't applied yet.
                                    <button class="me-3 text-orange-500" @click="openApplyModal = true">
                                        Apply here!
                                    </button>
                                    <ApplicationModal v-model="openApplyModal"/>
                        </span>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

.bronze-top-border {
    border-top: 5px solid rgb(240, 175, 110);

}

.silver-top-border {
    border-top: 5px solid #a6b0cd;
}

.gold-top-border {
    border-top: 5px solid rgb(249, 222, 85);
}

.platinum-top-border {
    border-top: 5px solid rgb(192, 205, 230);

}


</style>
