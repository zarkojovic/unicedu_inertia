<script setup>

import FieldsForm from '@/Molecules/FieldsForm.vue';
import Modal from '@/Molecules/Modal.vue';
import Button from '@/Atoms/Button.vue';
import {provide, ref} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import toast from '@/Stores/toast.js';

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

const submit = () => {
    const keys = Object.keys(formItems.value.formItems);
    if (keys.length === 0) {
        display.value = !display.value;
        toast.add({
            message: 'No changes made.',
            type: 'warning',
        });
        return;
    }
    var arrayOfUpdateFields = [];
    keys.forEach(el => {
        formItems.value.formItems[el].field_name = el;
        arrayOfUpdateFields.push(formItems.value.formItems[el]);
    });
    form.items = arrayOfUpdateFields;
    form.post('/applications/addNew', {
        onSuccess: () => {
            changeValue(false);
        },
    });
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
            <div class="grid col-span-2  grid-cols-2 sm:grid-cols-2 gap-4">
                <FieldsForm :items="page.props.deal_fields"/>
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
