<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import ModelDataDisplay from '@/Organisms/ModelDataDisplay.vue';
import DropdownInput from '@/Atoms/DropdownInput.vue';
import {provide, ref, watch} from 'vue';
import Button from '@/Atoms/Button.vue';
import GenericInput from '@/Atoms/GenericInput.vue';

const props = defineProps({
    data: {
        type: Object,
    },
    columns: {
        type: Object,
    },
    dealIntakes: {
        type: Array,
    },
    dealStages: {
        type: Array,
    },
    dealPackages: {
        type: Array,
    },
    dealUniversities: {
        type: Array,
    },
    dealDegrees: {
        type: Array,
    },
    activeFields: {
        type: Array,
    },
    intake: {
        type: String,
        default: null,
    },
    university: {
        type: String,
        default: null,
    },
    degree: {
        type: String,
        default: null,
    },
    stage: {
        type: String,
        default: null,
    },
    package: {
        type: String,
        default: null,
    },
    userInfo: {
        type: String,
        default: null,
    },
    program: {
        type: String,
        default: null,
    },
    begin_date: {
        type: String,
        default: null,
    },
    end_date: {
        type: String,
        default: null,
    },
    active: {
        type: String,
        default: null,
    },
});

const hidden = ['id'];

const rowTypes = [
    {
        name: 'Profile Image',
        type: 'image',
    }, {
        name: 'Package',
        type: 'package',
    }, {
        name: 'Stage',
        type: 'stage',
    },
];

// Array defining row check configurations for inactive rows
const checkRow = [
    {
        name: 'active',
        value: 'inactive',
        className: 'bg-red-100',
    },
];

// Reactive form items using the Composition API's ref function
const formItems = ref({
    formItems: {},
    intake: ref(props.intake),
    university: ref(props.university),
    degree: ref(props.degree),
    stage: ref(props.stage),
    package: ref(props.package),
    userInfo: ref(props.userInfo),
    program: ref(props.program),
    begin_date: ref(props.begin_date),
    end_date: ref(props.end_date),
    active: ref(props.active),
});

// Watch for changes in the form items and update accordingly
watch(formItems.value.formItems, function(value, oldValue) {
    // Extract values from the watched form items
    const dealIntake = value.dealIntake;
    const dealUniversity = value.dealUniversity;
    const dealDegree = value.dealDegree;
    const dealStage = value.dealStage;
    const dealPackage = value.dealPackage;
    const dealActive = value.dealActive;

    // Update form items based on watched values
    if (dealActive) {
        formItems.value.active = dealActive.value;
    }
    if (dealIntake) {
        formItems.value.intake = dealIntake.value;
    }
    if (dealUniversity) {
        formItems.value.university = dealUniversity.value;
    }
    if (dealDegree) {
        formItems.value.degree = dealDegree.value;
    }
    if (dealStage) {
        formItems.value.stage = dealStage.value;
    }
    if (dealPackage) {
        formItems.value.package = dealPackage.value;
    }
});

// Create a form for filtering using the useForm function
const formForFiltering = useForm(formItems.value);

// Function to filter the table based on form values
const filterTable = () => {
    // Update formForFiltering with formItems values
    formForFiltering.intake = formItems.value.intake;
    formForFiltering.university = formItems.value.university;
    formForFiltering.degree = formItems.value.degree;
    formForFiltering.stage = formItems.value.stage;
    formForFiltering.package = formItems.value.package;
    formForFiltering.program = formItems.value.program;
    formForFiltering.userInfo = formItems.value.userInfo;
    formForFiltering.begin_date = formItems.value.begin_date;
    formForFiltering.end_date = formItems.value.end_date;
    formForFiltering.active = formItems.value.active;

    // Make an API request to get filtered data for the admin applications
    formForFiltering.get('/admin/applications', {
        preserveScroll: true,
    });
};

// Provide the formItems to be used globally
provide('formItems', formItems);

// Create a reactive variable for controlling the visibility of filter options
const showFilterOptions = ref(false);


</script>

<template>
    <Head title="Profile"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Admin Panel</h2>
        </template>

        <div class="py-6 mt-6">
            <Button class="my-4" type="success" @click="showFilterOptions = !showFilterOptions">
                {{ showFilterOptions ? 'Hide filter options' : 'Show filter options' }}
            </Button>
            <div v-if="showFilterOptions">
                <GenericInput v-model="formItems.userInfo" class="mt-4"
                              helper="Search for first name, last name, email, phone"
                              input-name="userInfo"
                              label="User Info"
                />
                <GenericInput v-model="formItems.begin_date" class="mt-4"
                              input-name="dealBeginDate" label="Deal Begin Date"
                              type="date"/>
                <GenericInput v-model="formItems.end_date" class="mt-4"
                              input-name="dealEndDate" label="Deal End Date"
                              type="date"/>
                <GenericInput v-model="formItems.program" class="mt-4"
                              input-name="dealProgram"
                              label="Deal Program"
                />
                <DropdownInput :options="props.activeFields"
                               :selected-item="props.active"
                               input-name="dealActive"
                               label="Deal Active"/>
                <DropdownInput
                    :options="props.dealIntakes"
                    :selected-item="props.intake"
                    input-name="dealIntake"
                    label="Deal Intake"
                />
                <DropdownInput
                    :options="props.dealUniversities"
                    :selected-item="props.university"
                    input-name="dealUniversity"
                    label="Deal University"/>
                <DropdownInput
                    :options="props.dealDegrees"
                    :selected-item="props.degree"
                    input-name="dealDegree"
                    label="Deal Degree"/>
                <DropdownInput
                    :options="props.dealStages"
                    :selected-item="props.stage"
                    input-name="dealStage"
                    label="Deal Stage"/>
                <DropdownInput
                    :options="props.dealPackages"
                    :selected-item="props.package"
                    input-name="dealPackage"
                    label="Deal Package"/>

                <div class="flex justify-between">
                    <Button class="my-4" @click="filterTable">
                        Filter
                    </Button>
                    <Link :href="route('showApplication')">
                        <Button class="mt-3" type="danger">Reset</Button>
                    </Link>
                </div>
            </div>
            <ModelDataDisplay :column-types="rowTypes" :columns="props.columns" :data="props.data"
                              :excluded-columns="hidden" :is-editable="true" :row-highlight="checkRow"
                              edit-route="editApplication"
                              section-title="Student Applications"/>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
