<template>
    <Head title="Manage Fields"/>
    <AuthenticatedLayout>
        <div class="grid grid-cols-1 mt-24">
            <div v-for="(category, key) in categories" :key="key" class="col">
                <AdminFieldCategory :category="category"/>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import {Head} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {provide} from 'vue';
import AdminFieldCategory from "@/Organisms/AdminFieldCategory.vue";

export default {
    name: 'Fields',
    components: {
        AdminFieldCategory,
        AuthenticatedLayout,
        Head,
    },
    props: {
        categories: Array,
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


