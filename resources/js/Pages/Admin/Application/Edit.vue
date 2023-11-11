<script setup>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import {onMounted, provide, ref, watch} from 'vue';
import DropdownInput from '@/Atoms/DropdownInput.vue';
import DisplayStage from '@/Atoms/DisplayStage.vue';
import Button from '@/Atoms/Button.vue';

const props = defineProps({
    dealInfo: {
        type: Object,
    },
    stages: {
        type: Array,
    },
});

const formItems = ref({
    formItems: {},
    stage_id: null,
    deal_id: props.dealInfo.id,
});

const form = useForm(formItems.value);

provide('formItems', formItems);

onMounted(() => {
    // console.log(props.dealInfo);
});

const isStageChanged = ref(null);

watch(formItems.value.formItems, function(value, oldValue) {

    if (props.dealInfo.stage_id.toString() === value.dealStage.value) {
        isStageChanged.value = false;
    } else {
        form.stage_id = value.dealStage.value;
        isStageChanged.value = true;
    }
});

const changeDealStage = () => {
    form.post('/admin/applications/change-deal-stage', {
        onSuccess: () => {
            isStageChanged.value = false;
        },

    });
};

</script>


<template>
    <Head title="Edit user"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Application</h2>
        </template>

        <div class="mt-20">
            <div class="mx-auto bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-white overflow-hidden dark:bg-gray-800  shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between">
                            <h1 class="text-2xl">Student:
                                <Link :href="route('editUser',{id:props.dealInfo.user_id})"
                                      class="text-orange-500 underline">{{
                                        props.dealInfo.first_name + ' ' + props.dealInfo.last_name
                                    }}
                                </Link>
                            </h1>
                            <Link :href="route('showApplication')">
                                <Button>Go back</Button>
                            </Link>
                        </div>
                        <hr class="my-3">

                        <p class="mt-4">Status</p>
                        <h3 v-if="props.dealInfo.active" class="text-xl text-green-500">Active</h3>
                        <h3 v-else class="text-xl text-red-500">Inactive</h3>
                        <p>Stage</p>
                        <h3 class="text-xl mb-5">
                            <DisplayStage :stage-name="props.dealInfo.stage_name"/>
                        </h3>
                        <p>Program</p>
                        <h3 class="text-xl">{{ props.dealInfo.program }}</h3>
                        <p>Degree</p>
                        <h3 class="text-xl">{{ props.dealInfo.degree }}</h3>
                        <p>Intake</p>
                        <h3 class="text-xl">{{ props.dealInfo.intake }}</h3>
                        <p>University</p>
                        <h3 class="text-xl">{{ props.dealInfo.university }}</h3>
                        <p>Program</p>
                        <h3 class="text-xl">{{ props.dealInfo.program }}</h3>
                        <p>Created at</p>
                        <h3 class="text-xl">{{ props.dealInfo.created_at }}</h3>

                        <div v-if="props.dealInfo.active">
                            <h2 class="text-2xl mt-5">
                                Change Stage for this deal:
                            </h2>

                            <DropdownInput
                                :options="props.stages"
                                :selected-item="props.dealInfo.stage_id"
                                input-name="dealStage"
                                label="Deal Stage"
                            />
                            <Button v-if="isStageChanged" class="mt-3" @click="changeDealStage">Change Stage</Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
