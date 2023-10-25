<template>
    <div class="border mb-3 me-4 p-3 hover:cursor-grab active:cursor-grabbing sortable-item rounded-xl w-5/12" :field-id="'field_'+field_id">
        <div class="flex justify-between relative">
            <label :class="is_required ? 'text-primary' : ''" class="hover:cursor-grab active:cursor-grabbing select-none" :for="'field_check_'+field_id">
                {{ title }}
            </label>
                <IconAdjustments :id="'icon_'+field_id" :data-field-name="title" class="cursor-pointer font-light select-none"/>

            <div class="hidden checkboxes" :id="'field_check_'+field_id">
                <label class="flex items-center mb-1" :for="'active_'+field_id">
                    <input type="checkbox" :id="'active_'+field_id" :value="field_id" name="fields[]" checked="checked"/>
                    <span class="ml-2">Active</span>
                </label>
                <label class="flex items-center" :for="'required_'+field_id">
                    <input type="checkbox" :id="'required_'+field_id" :value="field_id" name="requiredFields[]" :checked="is_required ? 'checked' : '' "/>
                    <span class="ml-2">Required</span>
                </label>
            </div>
        </div>
    </div>
</template>

<script>
import {IconAdjustments} from '@tabler/icons-vue';
export default {
    name: "AdminField",
    props: {
        field_id: Number,
        title: String,
        is_required: Boolean,
        drag: Boolean
    },
    components: {
        IconAdjustments
    },
    // data() {
    //     return {
    //         drag: this.drag
    //     }
    // }
}
</script>

<style scoped>

</style>
