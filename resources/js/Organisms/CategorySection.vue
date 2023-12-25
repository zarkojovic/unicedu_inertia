<script setup>

import toast from '@/Stores/toast.js';
import Button from '@/Atoms/Button.vue';
import {provide, ref} from 'vue';
import DisplayInfo from '@/Atoms/DisplayInfo.vue';
import {useForm, usePage} from '@inertiajs/vue3';
import FieldsForm from '@/Molecules/FieldsForm.vue';

const display = ref(true);

const props = defineProps({
    categoryInfo: {
        type: Object,
    },
});

const categoryFieldForm = ref(null);

const formItems = ref({
    formItems: {},
});

const form = useForm(formItems.value);

const page = usePage();

provide('formItems', formItems);

// Variable to store the timer ID
const timer = ref(null);

// Form for updating the category fields to Bitrix CRM
const bitrixForm = useForm({});

// Function with a delayed execution
const delayedFunction = () => {
    // Clear the previous timer if it exists
    if (timer.value) {
        clearTimeout(timer.value);
    }
    // Set the timer
    var seconds = 10;
    timer.value = setTimeout(() => {
        // console.log('Delayed function executed.');
        bitrixForm.post('/user/sync-deal-fields', {
            onSuccess: () => {
                toast.add({
                    message: 'Bitrix CRM fields are updated!',
                    type: 'success',
                });
            },
            preserveScroll: true,
        });
        // Clear the timer after execution if needed
        clearTimeout(timer.value);
    }, seconds * 1000);
};
const submitForm = () => {

    // Copy formItems value to form.dataValues
    form.dataValues = formItems.value;

    // Get the keys of formItems
    const keys = Object.keys(formItems.value.formItems);

    // Check if there are no changes
    if (keys.length === 0) {
        display.value = !display.value;
        // Display a warning toast if no changes are made
        toast.add({
            message: 'No changes made.',
            type: 'warning',
        });
        return;
    }

    // Create an array to store updated fields
    var arrayOfUpdateFields = [];

    // Iterate through the keys
    keys.forEach(el => {
        // Set the field_name property in each formItem
        formItems.value.formItems[el].field_name = el;
        // Push the formItem to the array
        arrayOfUpdateFields.push(formItems.value.formItems[el]);
    });

    // Update the form with the array of updated fields
    form.formItems = arrayOfUpdateFields;

    // Send a POST request to update user fields
    form.post('/userFieldsUpdate', {
        onSuccess: () => {
            // Update the display value
            display.value = !display.value;

            // Display a success toast
            toast.add({
                message: props.categoryInfo.category_name + ' category is updated!',
                type: 'success',
            });

            // Reset formItems after successful update
            formItems.value = {formItems: {}};

            // Check if there are unsaved changes on the page and trigger the delayed function
            if (page.props.auth.user.unsaved_changes) {
                delayedFunction();
            }
        },
        preserveScroll: true,
    });
};

</script>

<template>
    <div class="bg-white rounded-3xl shadow-md mb-6">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div class="font-bold text-neutral-800 text-md sm:text-md md:text-lg mb-2">
                    {{ props.categoryInfo.category_name }}
                </div>
                <div v-if="!props.categoryInfo.read_only">
                    <div v-if="display" class="flex">
                        <Button
                            icon="edit"
                            @click="display = !display"
                        >
                            <span class="pr-1">Edit</span>
                        </Button>
                    </div>

                    <div v-else>
                        <Button
                            :disabled="form.processing"
                            icon="save"
                            type="success"
                            @click="submitForm"
                        >
                        </Button>
                        <Button
                            :disabled="form.processing"
                            class="ms-2"
                            icon="close"
                            type="danger"
                            @click="display = !display"
                        >
                        </Button>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-3">
                <div class="grid col-span-3  grid-cols-1 lg:col-span-2 sm:grid-cols-2 gap-4">
                    <DisplayInfo
                        v-for="(field,key) in props.categoryInfo.fields"
                        v-if="display"
                        :key="key"
                        :field-info="field"
                    />
                    <FieldsForm v-else :items="props.categoryInfo.fields"/>
                </div>
                <!-- Third Column (Empty on smaller screens) -->
                <div class="col-span-1 md:col-span-0">
                    <!-- Empty column content -->
                </div>
            </div>

        </div>
    </div>

</template>

<style scoped>

</style>
