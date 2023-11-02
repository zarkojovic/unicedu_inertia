<template>
    <div
        v-if="!showAddNew"
        :id="'category_'+catId"
        class="md:w-5/12 w-full add-category flex justify-between col border mb-3 me-4 p-3 rounded-xl position-relative cursor-pointer"
        @click="showAddNew = true">
        <div class="add-category-text">
            <label class="text-gray-400 cursor-pointer">Add new field</label>
        </div>
        <div class="add-category-icon">
            <IconPlus class="text-gray-400"/>
        </div>
    </div>
    <Autocomplete v-else :catId="catId" :order="order" @hide="toggleCombobox"/>

</template>

<script>
import {IconPlus} from "@tabler/icons-vue";
import Autocomplete from "@/Molecules/Autocomplete.vue";

export default {
    name: "AddNewField",
    components: {
        IconPlus,
        Autocomplete
    },
    props: {
        catId: Number,
        order: Number
    },
    data() {
        return {
            showAddNew: false
        }
    },
    methods: {
        toggleCombobox() {
            this.showAddNew = false
        }
    }
}
</script>

<style scoped>

</style>
