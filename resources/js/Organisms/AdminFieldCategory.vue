<template>
    <div class="md:p-6 md:px-8 p-4 mt-20 border rounded-xl bg-white">
        <form @submit.prevent="submitForm">
            <div class="flex justify-between">
                <h3 class="mb-10 mx-2 text-lg font-bold">{{ category.category_name }}</h3>
                <input class="cursor-pointer mb-10 bg-orange-500 py-1 px-5 text-white rounded-full" name="submit-btn"
                       type="submit"
                       value="Save"/>
            </div>
            <draggable
                :component-data="{
          tag: 'AdminField',
          type: 'transition-group',
          name: !drag ? 'slide' : null,
          pull: 'clone',
        }"
                :group="'fields_' + category.field_category_id"
                :list="category.fields"
                class="fields-container flex flex-wrap"
                item-key="field_id"
                v-bind="dragOptions"
                @change="onChanged"
                @end="drag=false"
                @start="drag=true"
            >
                <template #item="{ element }" class="">
                    <AdminField
                        :field_id="element.field_id"
                        :is_required="element.is_required === 1"
                        :title="element.title ?? element.field_name"
                        @onIsRequiredChange="updateIsRequiredValue"
                    />
                </template>
            </draggable>
            <hr class="mb-3"/>
            <AddNewField :catId="category.field_category_id" :order="category.fields.length + 1"/>
        </form>
    </div>
</template>

<script setup>
import {computed, ref} from 'vue';
import AddNewField from "@/Molecules/AddNewField.vue";
import draggable from "vuedraggable";
import AdminField from "@/Molecules/AdminField.vue";
import {useForm} from "@inertiajs/vue3";
import toast from "@/Stores/toast";

const props = defineProps({
    category: Object,
});

const form = useForm({
    fieldsOrders: props.category.fields
    // fieldsSettings: []
});

const drag = ref(false);

const dragOptions = computed(() => {
    return {
        animation: 400,
        disabled: false,
        ghostClass: "ghost",
    };
});

const onChanged = () => {
    form.fieldsOrders = [];
    props.category.fields.forEach((field, index) => {
        form.fieldsOrders.push({
            field_id: field.field_id,
            field_name: field.field_name,
            is_required: field.is_required,
            order: index + 1
        });
    });
};
const submitForm = () => {
    let isChanged = false;
    props.category.fields.forEach((field, index) => {
        if (form.fieldsOrders[index].order !== field.order || form.fieldsOrders[index].is_required != field.is_required) {
            console.log(form.fieldsOrders[index].is_required)
            console.log(field.is_required)
            isChanged = true;
        }
    });

    if (isChanged) {
        form.post("/admin/fields-modify", {preserveScroll: true});
        return;
    }
    // console.log("return some warning no changes made toast otherwise");
    addToast({
        message: "No changes made",
        type: "warning",
        duration: 4000
    });
};

const addToast = (obj) => {
    toast.add(obj);
};
const updateIsRequiredValue = (event) => {
    const indexToUpdate = form.fieldsOrders.findIndex(field => field.field_id === event.field_id);

    // Update the existing object with the new is_required value
    if (indexToUpdate !== -1) {
        form.fieldsOrders[indexToUpdate].is_required = event.is_required;
    } else console.log("You're trying to set a required field that does not exist.");
    // // Check if the field_id exists in the array
    // const indexToUpdate = form.fieldsSettings.findIndex(field => field.field_id === event.field_id);
    //
    // if (indexToUpdate !== -1) {
    //     // If found, update the existing object with the new data
    //     form.fieldsSettings[indexToUpdate] = event;
    // } else {
    //     // If not found, push the event object into the array
    //     form.fieldsSettings.push(event);
    // }
}
</script>

<style scoped>
.slide-move {
    transition: transform 0.5s;
}

.no-move {
    transition: transform 0s;
}

.ghost {
    opacity: 0.5;
    background: #efefef;
}
</style>
