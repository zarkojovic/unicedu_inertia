<template>
    <Head title="Manage Fields"/>
    <AuthenticatedLayout>
        <div class="grid grid-cols-1 mt-24">
            <div class="col" v-for="(category, key) in categories" :key="key">
                <div class="p-6 px-8 mb-20 border rounded-xl bg-white">
                    <form>
                        <div class="flex justify-between">
                            <h3 class="mb-10 text-lg font-bold">{{ category.category_name }}</h3>
                            <input type="submit" value="Submit" name="submit-btn"
                                   class="mb-10 "/>
                        </div>
                        <draggable class="fields-container flex flex-wrap"
                                       v-model="category.fields"
                                       v-bind="dragOptions"
                                       :group="'fields_'+category.field_category_id"
                                       @start="drag=true"
                                       @end="drag=false"
                                       item-key="field_id"
                                       :component-data="{
                                          tag: 'AdminField',
                                          type: 'transition-group',
                                          name: !drag ? 'slide' : null,
                                          pull: 'clone',
                                        }">
                                <template #item="{ element }" class="">
                                    <AdminField :drag="drag" :field_id="element.field_id" :title="element.title ?? element.field_name" :is_required="element.required"/>
                                </template>
                            </draggable>
                        <hr class="mb-3"/>
                        <AddNewField :id="category.field_category_id"/>





                        <!--                                <AdminField :field_id="element.field_id" :title="element.title ?? element.field_name" :is_required="element.required"/>-->
                        <!--v-for="(field, key) in getCategoryFields(category.field_category_id)" :key="key"   v-for-> getCategoryFields(category.field_category_id)-->

                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AdminField from "@/Molecules/AdminField.vue";
import {IconPlus} from '@tabler/icons-vue';
import draggable from "vuedraggable";
import AddNewField from "@/Molecules/AddNewField.vue";


export default {
    name: "Fields",
    components: {
        AdminField,
        AuthenticatedLayout,
        Head,
        IconPlus,
        draggable,
        AddNewField
    },
    props: {
        categories: Array,
    },
    data() {
        return {
            drag: false,
            showAddNew: false
        };
    },
    computed: {
        dragOptions() {
            return {
                animation: 400,
                disabled: false,
                ghostClass: "ghost"
            };
        }
    },
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

