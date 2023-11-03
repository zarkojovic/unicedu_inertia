<template>
    <Head title="Manage Fields"/>
    <AuthenticatedLayout>
        <div class="grid grid-cols-1 mt-24">
            <div v-for="(category, key) in categories" :key="key" class="col">
                <div class="p-6 px-8 mb-20 border rounded-3xl bg-white">
                    <form>
                        <div class="flex justify-between">
                            <h3 class="mb-10 text-lg font-bold">{{ category.category_name }}</h3>
                            <input class="mb-10 " name="submit-btn" type="submit"
                                   value="Submit"/>
                        </div>
                        <draggable :component-data="{
                                          tag: 'AdminField',
                                          type: 'transition-group',
                                          name: !drag ? 'slide' : null,
                                          pull: 'clone',
                                        }"
                                   :group="'fields_'+category.field_category_id"
                                   :list="category.fields"
                                   class="fields-container flex flex-wrap"
                                   item-key="field_id"
                                   v-bind="dragOptions"
                                   @end="drag=false"
                                   @start="drag=true">
                            <template #item="{ element }" class="">
                                <AdminField :field_id="element.field_id" :is_required="element.required"
                                            :title="element.title ?? element.field_name"/>
                            </template>
                        </draggable>
                        <hr class="mb-3"/>
                        <AddNewField :catId="category.field_category_id" :order="category.fields.length + 1"/>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import {Head} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminField from '@/Molecules/AdminField.vue';
import {IconPlus} from '@tabler/icons-vue';
import draggable from 'vuedraggable';
import AddNewField from '@/Molecules/AddNewField.vue';
import {provide} from 'vue';
// import { toRef, provide } from 'vue'

export default {
    name: 'Fields',
    components: {
        AdminField,
        AuthenticatedLayout,
        Head,
        IconPlus,
        draggable,
        AddNewField,
    },
    props: {
        categories: Array,
    },
    data() {
        return {
            drag: false,
            showAddNew: false,
        };
    },
    computed: {
        dragOptions() {
            return {
                animation: 400,
                disabled: false,
                ghostClass: 'ghost',
            };
        },
    },
    provide() {
        return {
            navBtnType: 'adminFields',
        };
    },
    // setup(props){
    //     const categoriesNew = toRef(props, "categories");
    //     // console.log(categoriesNew.value[0]);
    //
    //     function addFieldToCategory(catId,field){
    //         categoriesNew.value.forEach((category)=> {
    //             if (category.field_category_id === catId){
    //                 field.order = category.fields.length + 1;
    //                 field.field_category_id = catId;
    //                 field.is_required = false;
    //
    //                 category.fields.push(field);
    //
    //                 console.log(category.fields)
    //             }
    //         });
    //     }
    //
    //     provide("addFieldFunction", addFieldToCategory);
    //     return {
    //         categoriesNew,
    //         addFieldToCategory
    //     }
    // },
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

