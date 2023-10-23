<template>
    <Head title="Manage Fields"/>
    <AuthenticatedLayout>
        <div class="grid grid-cols-1 mt-5">
            <div class="col" v-for="(category, key) in categories" :key="key">
                <p>{{ category.category_name }}</p>
                <template v-for="(field, key) in getCategoryFields(category.field_category_id)" :key="key">
                   <li>{{ field.title ?? field.field_name }}</li>
                </template>
                <br/>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    name: "Fields",
    components: {AuthenticatedLayout, Head},
    props: {
        categories: Array,
        fields: Array
    },
    methods: {
        getCategoryFields(categoryId){
            const categoryFields = []
            this.fields.forEach((field) => {
                if (field.field_category_id === categoryId){
                    categoryFields.push(field);
                }
            })

            return categoryFields;
        }
    }
}
</script>

