<template>
    <div class="md:p-6 md:px-8 p-4 mt-20 border rounded-xl bg-white">
        <form @submit.prevent="submitForm">
            <div class="flex justify-between">
                <h3 class="mb-10 text-lg font-bold">{{ category.category_name }}</h3>
                <input class="mb-10 " name="submit-btn" type="submit" value="Submit"/>
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
                        :is_required="element.is_required"
                        :title="element.title ?? element.field_name"
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

const props = defineProps({
    category: Object,
});

const form = useForm({
    fieldsOrders: [],
});

const drag = ref(false);

const onChanged = () => {
    form.fieldsOrders = [];
    props.category.fields.forEach((field, index) => {
        form.fieldsOrders.push({field_id: field.field_id, field_name: field.field_name, order: index + 1});
    });
};

const submitForm = () => {
    form.post("/admin/fields-modify", {preserveScroll: true});
};

const dragOptions = computed(() => {
    return {
        animation: 400,
        disabled: false,
        ghostClass: "ghost",
    };
});
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
