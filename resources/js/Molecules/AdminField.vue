<template>
    <div :field-id="'field_'+field_id"
         class="border mb-3 md:me-4 p-3 hover:cursor-grab active:cursor-grabbing sortable-item rounded-xl md:w-5/12 w-full">
        <div class="flex justify-between relative">
            <label :class="is_required ? 'text-primary' : ''"
                   :for="'field_check_'+field_id" class="hover:cursor-grab active:cursor-grabbing select-none">
                {{ title }}
            </label>
            <IconAdjustments :id="'icon_'+field_id" :data-field-name="title"
                             class="cursor-pointer font-light select-none"/>

            <div :id="'field_check_'+field_id" class="hidden checkboxes">
                <label :for="'active_'+field_id" class="flex items-center mb-1">
                    <input :id="'active_'+field_id" :value="field_id" checked="checked" name="fields[]"
                           type="checkbox"/>
                    <span class="ml-2">Active</span>
                </label>
                <label :for="'required_'+field_id" class="flex items-center">
                    <input :id="'required_'+field_id" :checked="is_required ? 'checked' : '' " :value="field_id"
                           name="requiredFields[]"
                           type="checkbox"/>
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
    },
    components: {
        IconAdjustments
    },
}
</script>

<style scoped>

</style>
