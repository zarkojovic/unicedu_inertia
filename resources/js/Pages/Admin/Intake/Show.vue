<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import ModelDataDisplay from '@/Organisms/ModelDataDisplay.vue';
import DropdownInput from '@/Atoms/DropdownInput.vue';
import {computed, onMounted, provide, ref, watch} from 'vue';
import Button from '@/Atoms/Button.vue';

const props = defineProps({
    data: {
        type: Object,
    },
    intakeSelect: {
        type: Array,
    },
});

const formItems = ref({
    formItems: {},
    intake_id: null,
});

const form = useForm(formItems.value);

provide('formItems', formItems);

const activeIntakeId = computed(() => {
    return props.data.data.filter(obj => obj.active === 'active')[0].id.toString();
});

const hiddenColumns = ['id'];

onMounted(() => {
    console.log(activeIntakeId.value);
});

const isIntakeChanged = ref(null);

watch(formItems.value.formItems, function(value, oldValue) {
    console.log(value.activeIntake.value, activeIntakeId.value);
    if (activeIntakeId.value === value.activeIntake.value) {
        isIntakeChanged.value = false;
    } else {
        form.intake_id = value.activeIntake.value;
        isIntakeChanged.value = true;
    }
});

const changeActiveIntake = () => {
    form.post('/admin/intakes/change-active-intake', {});
};

const rowHighlight = [
    {
        name: 'active',
        value: 'inactive',
        className: 'bg-red-100',
    }, {
        name: 'active',
        value: 'active',
        className: 'bg-blue-100',
    },
];


</script>

<template>
    <Head title="Profile"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Admin Panel</h2>
        </template>
        <div class="py-6 mt-6">
            <ModelDataDisplay :data="props.data" :excluded-columns="hiddenColumns" :row-highlight="rowHighlight"
                              section-title="Intakes"/>
            <div class="my-5"></div>
            <DropdownInput :options="props.intakeSelect" :selected-item="activeIntakeId"
                           input-name="activeIntake" label="Current intake"/>

            <Button v-if="isIntakeChanged" class="mt-3" @click="changeActiveIntake">Change Package</Button>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
