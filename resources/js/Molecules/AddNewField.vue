<template>
    <div class="w-5/12 add-category flex justify-between col border mb-3 me-4 p-3 rounded-xl position-relative cursor-pointer"
         :id="'category_'+id"
         v-if="!showAddNew"
         @click="showAddNew = true">
        <div class="add-category-text">
            <label class="text-gray-400 cursor-pointer">Add new field</label>
        </div>
        <div class="add-category-icon">
            <IconPlus class="text-gray-400"/>
        </div>
    </div>
    <Autocomplete v-else/>

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
        id: Number
    },
    data() {
        return {
            showAddNew: false
        }
    }
}
</script>

<style scoped>

</style>
