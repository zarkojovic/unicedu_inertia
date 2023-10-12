<script setup>

import toast from '@/Stores/toast.js';
import Button from "@/Atoms/Button.vue";
import {onMounted, provide, ref} from "vue";
import GenericInput from "@/Atoms/GenericInput.vue";
import FileInput from "@/Atoms/FileInput.vue";
import DropdownInput from "@/Atoms/DropdownInput.vue";
import DisplayInfo from "@/Atoms/DisplayInfo.vue";
import {useForm} from "@inertiajs/vue3";

const display = ref(true);

const props = defineProps({
    categoryInfo: {
        type: Object
    }
})

const categoryFieldForm = ref(null);

const formItems = ref({
    formItems: {},
    dropdown: {
        label: 'Platinum',
        value: null
    }
})

const form = useForm(formItems.value);

provide('formItems', formItems);


const submitForm = () => {
    form.dataValues = formItems.value;
    const keys = Object.keys(formItems.value.formItems);
    if (keys.length === 0) {
        display.value = !display.value;
        toast.add({
            message: 'No changes made.',
            type: 'warning'
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
                type: 'success'
            })
        },

    });


    // console.log(arrayOfUpdateFields)


    // const formFields = document.querySelectorAll('.userFormField');
    //
    // formFields.forEach(el => {
    //     var obj = {
    //         type: '',
    //         value: '',
    //         display_value: '',
    //         file_name: '',
    //         file_path: ''
    //     }
    //     if (el.type.includes('select')) {
    //         obj.type = 'select';
    //         obj.value = el.options[el.selectedIndex].value;
    //         obj.display_value = el.options[el.selectedIndex].text;
    //     } else {
    //         obj.type = el.type;
    //         obj.value = el.value;
    //     }
    //
    //     console.log(obj)
    // })


    // display.value = !display.value;
}
</script>

<template>
    <div class="bg-white rounded-xl shadow-md mb-3">
        <div class="p-6 ">
            <div class="flex justify-between items-center">
                <div class="font-bold text-xl mb-2">{{ props.categoryInfo.category_name }}</div>
                <div class="flex" v-if="display">
                    <Button
                        icon="edit"
                        @click="display = !display"
                    >
                        Edit
                    </Button>
                </div>
                <div v-else>
                    <Button
                        type="success"
                        icon="save"
                        :disabled="form.processing"
                        @click="submitForm"
                    >
                    </Button>
                    <Button
                        type="danger"
                        icon="close"
                        class="ms-2"
                        @click="display = !display"
                        :disabled="form.processing"
                    >
                    </Button>
                </div>
            </div>
            <div class="grid grid-cols-3 sm:grid-cols-3 gap-4 mt-3">
                <div class="grid col-span-2  grid-cols-2 sm:grid-cols-2 gap-4">
                    <DisplayInfo
                        v-if="display"
                        v-for="(field,key) in props.categoryInfo.fields"
                        :key="key"
                        :field-info="field"
                    />
                    <div v-else v-for="(field,key) in props.categoryInfo.fields" :key="field.field_name">

                        <form ref="categoryFieldForm">
                            <FileInput v-if="field.type === 'file'"
                                       :key="key"
                                       :label="field.title"
                                       :input-name="field.field_name"
                                       :input-id="field.field_name"
                                       :is-category-field="true"
                                       :valid-types="['application/pdf']"
                            />

                            <DropdownInput
                                v-else-if="field.type === 'enumeration'"
                                :options="field.items"
                                v-model="form.dropdown"
                                :label="field.title"
                                :input-name="field.field_name"
                            />

                            <GenericInput v-else
                                          :type="field.type === 'string' ? 'text' : field.type"
                                          :label="field.title"
                                          :is_required="!!field.is_required"
                                          v-model="field.value"
                                          :is-category-field="true"
                                          :input-name="field.field_name"
                            />
                        </form>
                    </div>
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
