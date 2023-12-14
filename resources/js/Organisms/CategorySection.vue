<script setup>

import toast from '@/Stores/toast.js';
import Button from '@/Atoms/Button.vue';
import {onMounted, provide, ref} from 'vue';
import DisplayInfo from '@/Atoms/DisplayInfo.vue';
import {useForm} from '@inertiajs/vue3';
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

provide('formItems', formItems);

const submitForm = () => {
    form.dataValues = formItems.value;
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

    form.formItems = arrayOfUpdateFields;

    form.post('/userFieldsUpdate', {
        onSuccess: () => {
            display.value = !display.value;
            toast.add({
                message: props.categoryInfo.category_name + ' category is updated!',
                type: 'success',
            });
            formItems.value = {formItems: {}};
        },
        preserveScroll: true,
    });

};

onMounted(() => {
});
</script>

<template>
    <div class="bg-white rounded-3xl shadow-md mb-6">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div class="font-bold text-neutral-800 text-md sm:text-md md:text-lg mb-2">
                    {{ props.categoryInfo.category_name }}
                </div>
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
