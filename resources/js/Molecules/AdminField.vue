<template>
    <div :class="!is_active ? 'bg-gray-100' : ''"
         :field-id="'field_'+field_id"
         class="border mb-3 md:me-4 p-3 hover:cursor-grab active:cursor-grabbing sortable-item rounded-xl md:w-5/12 w-full">
        <div class="flex justify-between relative">
            <label :class="is_required ? 'text-orange-500' : '', !is_active ? 'line-through' : ''"
                   :for="'field_check_'+field_id" class="hover:cursor-grab active:cursor-grabbing select-none">
                {{ title }}
            </label>
            <div class="mb-field-settings-icon">
                <IconAdjustments :id="'icon_'+field_id" :data-field-name="title"
                                 class="cursor-pointer font-light select-none" @click="openModal = true"/>
            </div>
            <Modal v-if="openModal" @close="openModal=false">
                <template v-slot:modalTitle>Field "{{ title }}" configurations</template>
                <template v-slot:modalContent>
                    <div :id="'field_check_'+field_id" class="checkboxes">
                        <label :for="'active_'+field_id" class="flex items-center mb-1">
                            <input :id="'active_'+field_id"
                                   v-model="is_active"
                                   :value="field_id"
                                   name="fields[]"
                                   type="checkbox"/>
                            <span class="ml-2">Active</span>
                        </label>
                        <label :for="'required_'+field_id"
                               class="flex items-center">
                            <input :id="'required_'+field_id"
                                   v-model="is_required"
                                   :disabled="!is_active"
                                   :value="field_id"
                                   name="requiredFields[]"
                                   type="checkbox"/>
                            <span :class="!is_active ? 'text-gray-200':''" class="ml-2">Required</span>
                        </label>
                    </div>
                </template>
                <template v-slot:modalFooter>
                    <Button :type="'muted'" @click="openModal=false">Close</Button>
                </template>
            </Modal>
        </div>
    </div>
</template>
<script setup>
import {ref, defineProps, defineEmits, watch} from 'vue';
import {IconAdjustments} from '@tabler/icons-vue';
import Modal from '@/Molecules/Modal.vue';
import Button from "@/Atoms/Button.vue";

const props = defineProps({
    field_id: Number,
    title: String,
    is_required: Boolean,
    catId: Number
});

const openModal = ref(false);
const is_required = ref(props.is_required);
const is_active = ref(!!props.catId);
const emit = defineEmits();

watch(is_required, (newValue) => {
    emit("onIsRequiredChange", {field_id: props.field_id, is_required: newValue});
});

watch(is_active, (newValue) => {
    emit("onIsActiveChange", {field_id: props.field_id, is_active: newValue});
});

</script>
<!--<script>-->
<!--import {IconAdjustments} from '@tabler/icons-vue';-->
<!--import Modal from "@/Molecules/Modal.vue";-->

<!--export default {-->
<!--    name: "AdminField",-->
<!--    props: {-->
<!--        field_id: Number,-->
<!--        title: String,-->
<!--        is_required: Number,-->
<!--    },-->
<!--    components: {-->
<!--        Modal,-->
<!--        IconAdjustments-->
<!--    },-->
<!--    data() {-->
<!--        return {-->
<!--            openModal: false,-->
<!--            is_required: this.is_required-->
<!--        }-->
<!--    }-->
<!--}-->
<!--</script>-->
