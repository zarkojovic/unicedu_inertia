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
                @change="reorderFields"
                @end="drag=false"
                @start="drag=true"
            >
                <template #item="{ element }" class="">
                    <AdminField
                        :catId="element.field_category_id"
                        :field_id="element.field_id"
                        :is_required="element.is_required === 1"
                        :title="element.title ?? element.field_name"
                        @onIsActiveChange="updateFieldCategoryId"
                        @onIsRequiredChange="updateIsRequiredValue"
                    />
                </template>
            </draggable>
            <hr class="mb-3"/>
            <AddNewField :catId="category.field_category_id" :order="highestOrder + 1"/>
        </form>
    </div>
</template>

<script setup>
import {computed, ref, onUpdated, onMounted, watch, provide} from 'vue';
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
});

const drag = ref(false);

const dragOptions = computed(() => {
    return {
        animation: 400,
        disabled: false,
        ghostClass: "ghost",
    };
});

function updateFieldsOrders(field_id) {
    const fieldToAdd = props.category.fields.find(field => field.field_id === field_id);

    if (fieldToAdd) {
        form.fieldsOrders.push(fieldToAdd);
    } else {
        addToast({
            message: "We received a field that doesn't exist in this category.",
            type: "danger",
        });
    }
}

provide('fieldsOrders', updateFieldsOrders);

const reorderFields = () => {
    let reduceByInactive = 1;
    props.category.fields.forEach((field, index) => {
        // Update the order property for each field in form.fieldsOrders that's active (field_category_id !== 0)
        const orderItem = form.fieldsOrders.find(item => item.field_id === field.field_id);
        if (orderItem) {
            if (!orderItem.field_category_id) {
                reduceByInactive--;
                orderItem.order = null;
            } else {
                orderItem.order = index + reduceByInactive;
            }
        } else {
            addToast({
                message: "We received a field that doesn't exist in this category.",
                type: "danger",
            });
        }
    });
};

const updateIsRequiredValue = (event) => {
    const indexToUpdate = form.fieldsOrders.findIndex(field => field.field_id === event.field_id);

    // Update the existing object with the new is_required value
    if (indexToUpdate !== -1) {
        form.fieldsOrders[indexToUpdate].is_required = event.is_required;
    } else {
        addToast({
            message: "You're trying to set a required field that doesn't exist.",
            type: "danger",
        });
    }
}

const updateFieldCategoryId = (event) => {
    const indexToUpdate = form.fieldsOrders.findIndex(field => field.field_id === event.field_id);

    if (indexToUpdate !== -1) {
        if (!event.is_active) {
            form.fieldsOrders[indexToUpdate].field_category_id = null;
            reorderFields();
        } else {
            form.fieldsOrders[indexToUpdate].field_category_id = props.category.field_category_id;
            reorderFields();
        }
    } else {
        addToast({
            message: "You're trying to change the active setting for a field that does not exist.",
            type: "danger",
        });
    }
}

const highestOrder = computed(() => {
    const orders = form.fieldsOrders
        .filter(field => field.field_category_id !== null)
        .map(field => field.order);
    return Math.max(...orders, 0);
});

const submitForm = () => {
    try {
        form.post("/admin/fields-modify", {preserveScroll: true});
        form.fieldsOrders = form.fieldsOrders.filter(orderItem => orderItem.field_category_id !== null);
    } catch {
        addToast({
            message: "Submitting your changes failed.",
            type: "danger"
        });
    }

};

const addToast = (obj) => {
    toast.add(obj);
};
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
