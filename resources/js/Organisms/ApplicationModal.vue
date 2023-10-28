<script setup>

import FieldsForm from '@/Molecules/FieldsForm.vue';
import Modal from '@/Molecules/Modal.vue';
import Button from '@/Atoms/Button.vue';
import {provide, ref} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';

const props = defineProps({
    modelValue: {
        type: Boolean,
    },
});

const formItems = ref({
    formItems: {},
});

provide('formItems', formItems);

const form = useForm({
    items: '',
});

const page = usePage();

const emits = defineEmits(['update:modelValue']);

const errorsText = ref(null);

const submit = () => {
    const keys = Object.keys(formItems.value.formItems);
    if (keys.length === 0) {

        errorsText.value = 'You must fill the fields!';
        return;
    } else {
        errorsText.value = null;

    }
    var arrayOfUpdateFields = [];
    keys.forEach(el => {
        formItems.value.formItems[el].field_name = el;
        arrayOfUpdateFields.push(formItems.value.formItems[el]);
    });
    form.items = arrayOfUpdateFields;

    function validateArrayOfObjects(arr) {
        // Check if the array has a length of 3
        if (arr.length !== 3) {
            return false;
        }
        // Check if each element has a non-empty .value property
        for (let i = 0; i < arr.length; i++) {
            const element = arr[i];
            if (!element.hasOwnProperty('value') || typeof element.value !== 'string' || element.value.trim() === '') {
                return false;
            }
        }
        // If all checks pass, return true
        return true;
    }

    if (validateArrayOfObjects(arrayOfUpdateFields)) {
        errorsText.value = null;
        form.post('/applications/addNew', {
            onSuccess: () => {
                changeValue(false);
            },
        });
    } else {
        errorsText.value = 'You didn\'t insert all of the fields!';
    }
};

const changeValue = (value) => {
    emits('update:modelValue', value);
};

</script>

<template>

    <Modal v-if="props.modelValue" @close="changeValue(false)">
        <template v-slot:modalTitle>
            <h2 class="text-lg">Add new application</h2>
        </template>
        <template v-slot:modalContent>
            <div class="grid col-span-2  grid-cols-1 sm:grid-cols-1 gap-4">
                <FieldsForm :items="page.props.deal_fields"/>
                <p v-if="errorsText !== null" class="text-red-500">{{ errorsText }}</p>
            </div>
        </template>

        <template v-slot:modalFooter>
            <div class="flex justify-end">

                <Button type="muted" @click="changeValue(false)">
                    Cancel
                </Button>
                <Button class="ms-3" type="primary" @click="submit">
                    Submit
                </Button>
            </div>
        </template>
    </Modal>
</template>

<style scoped>

</style>
