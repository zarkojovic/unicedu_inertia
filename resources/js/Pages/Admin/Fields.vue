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
                        <template class="w-5/12 add-category flex justify-between col border mb-3 me-4 p-3 rounded-xl position-relative cursor-pointer"
                                  :id="'category_'+category.field_category_id">
                            <div class="add-category-text">
                                <label class="text-gray-400 cursor-pointer">Add new field</label>
                            </div>
                            <div class="add-category-icon">
                                <IconPlus class="text-gray-400"/>
                            </div>
                        </template>




                        <br/>
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


export default {
    name: "Fields",
    components: {
        AdminField,
        AuthenticatedLayout,
        Head,
        IconPlus,
        draggable
    },
    props: {
        categories: Array,
        // fields: Array
    },
    data() {
        return {
            drag: false,
            draggableFields: []
            // localFields: this.fields
            // localFields: (categoryId) => {return this.fields.filter((field) => field.field_category_id === categoryId)}
        };
    },
    computed: {
        dragOptions() {
            return {
                animation: 200,
                // group: "description",
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

