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
// const fields = props.category.fields;
const form = useForm({
    fieldsOrders: props.category.fields
});


const drag = ref(false);
// const reduceByInactive = ref(1);

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
        // const reactiveField = ref(fieldToAdd);

        form.fieldsOrders.push(fieldToAdd);
        console.log(form.fieldsOrders)
    } else {
        console.log("The field doesn't exist.");
    }
}

provide('fieldsOrders', updateFieldsOrders);
// watch(fields, (newVal) => {
//     console.log("updated")
//     console.log(newVal)
//     console.log(form.fieldsOrders)
// });
// onMounted(() => {
//     // form.fieldsOrders = props.category.fields;
//     console.log(props.category.fields)
//     console.log(form.fieldsOrders)
// });
// onUpdated(() => {
//     console.log(props.category.fields)
//     // form.fieldsOrders = props.category.fields;
//     console.log(form.fieldsOrders)
// });

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
            console.log("Field doesn't exist in this category.")
        }
    });
    console.log(form.fieldsOrders)
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

const updateFieldCategoryId = (event) => {
    console.log(event.field_id)
    const indexToUpdate = form.fieldsOrders.findIndex(field => field.field_id === event.field_id);

    if (indexToUpdate !== -1) {
        // form.fieldsOrders[indexToUpdate].field_category_id = event.field_category_id;
        if (!event.is_active) {
            form.fieldsOrders[indexToUpdate].field_category_id = null;
            console.log("set to inactive")
            reorderFields();
        } else {
            // const matchingField = props.category.fields.find(field => field.field_id === event.field_id);
            // if (matchingField) {
            //     console.log(matchingField)
            form.fieldsOrders[indexToUpdate].field_category_id = props.category.field_category_id;
            console.log("set to active")
            reorderFields();
            // }
        }
    } else {
        console.log("You're trying to set a field that does not exist.");
    }
}

// const updateFieldCategoryId = (event) => {
//     console.log(event.field_id)
//     const fieldToUpdate = form.fieldsOrders.find(field => field.field_id === event.field_id);
//
//     if (fieldToUpdate) {
//         if (!event.is_active) {
//             // If event.is_active is false, set field_category_id to null & order to null
//             fieldToUpdate.field_category_id = null;
//             console.log("set to inactive")
//             //CALL ONCHANGE TO UPDATE ORDERS
//             reorderFields();
//         } else {
//             // If event.is_active is true, find the corresponding field_category_id from props.category.fields and order
//             const matchingField = props.category.fields.find(field => field.field_id === event.field_id);
//             if (matchingField) {
//                 fieldToUpdate.field_category_id = matchingField.field_category_id;
//                 // reduceByInactive++;
//                 console.log("set to active")
//                 //CALL ONCHANGE TO UPDATE ORDERS
//                 reorderFields();
//             }
//         }
//     } else {
//         console.log("You're trying to set a field that does not exist.");
//     }
// }

const highestOrder = computed(() => {
    const orders = form.fieldsOrders
        .filter(field => field.field_category_id !== null)
        .map(field => field.order);
    return Math.max(...orders, 0);
});

const submitForm = () => {
    form.post("/admin/fields-modify", {preserveScroll: true});
    form.fieldsOrders = form.fieldsOrders.filter(orderItem => orderItem.field_category_id !== null);
    console.log(form.fieldsOrders)

    // let isChanged = false;
    // props.category.fields.forEach((field, index) => {
    //     console.log(field, form.fieldsOrders[index])
    //     if (form.fieldsOrders[index].order !== field.order ||
    //         form.fieldsOrders[index].is_required != field.is_required ||
    //         form.fieldsOrders[index].field_category_id != field.field_category_id) {
    //         isChanged = true;
    //     }
    // });
    //
    // if (isChanged) {
    //     form.post("/admin/fields-modify", {preserveScroll: true});
    //     return;
    // }
    //
    // addToast({
    //     message: "No changes made",
    //     type: "warning",
    //     duration: 4000
    // });
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
