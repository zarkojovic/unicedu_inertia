<script setup>

import Button from "@/Atoms/Button.vue";
import {onMounted, ref} from "vue";
import GenericInput from "@/Atoms/GenericInput.vue";
import FileInput from "@/Atoms/FileInput.vue";

const display = ref(true);

const props = defineProps({
    categoryInfo: {
        type: Array
    }
})

onMounted(() => {
    console.log(props.categoryInfo);
})

</script>

<template>
    <div class="bg-white rounded-xl shadow-md mb-3">
        <div class="px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="font-bold text-xl mb-2">{{ props.categoryInfo.category_name }}</div>
                <div class="flex" v-if="display">
                    <Button
                        icon="edit"
                        @click="display = !display"
                    >
                        Edit
                    </Button>
                </div>
                <div v-else>
                    <Button
                        type="success"
                        icon="save"
                    >
                    </Button>
                    <Button
                        type="danger"
                        icon="close"
                        class="ms-2"
                        @click="display = !display"
                    >
                    </Button>
                </div>
            </div>
            <p class="text-gray-700 text-base mt-2" v-if="display" v-for="(field,key) in props.categoryInfo.fields"
               :key="key">
                {{ field.title }}
            </p>
            <p v-else v-for="(field,key) in props.categoryInfo.fields" :key="field.field_name">
                <GenericInput v-if="field.type !== 'file'"
                              :type="field.type"
                              :label="field.title"
                              :is_required="field.is_required"
                />

                <FileInput v-else :key="key"

                />
            </p>
        </div>
    </div>

</template>

<style scoped>

</style>
