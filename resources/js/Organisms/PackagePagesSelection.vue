<script setup>
import Button from '@/Atoms/Button.vue';
import ListInput from '@/Atoms/ListInput.vue';
import PackageIndicator from '@/Atoms/PackageIndicator.vue';
import {computed, ref} from 'vue';
import {useForm} from '@inertiajs/vue3';

const props = defineProps({
    data: {
        type: Object,
    },
});

const selectedItems = ref(props.data.active);

const formattedActiveItems = computed(() => {
    return selectedItems.value.map(String);
});

const realItems = ref(formattedActiveItems.value);

const form = useForm({
    pages: formattedActiveItems.value,
    package_id: props.data.package_id,
});

const updateFields = () => {
    form.post('/admin/set-package-pages', {
        preserveScroll: true,
    });
    console.log(form.pages, form.package_id);
};

</script>

<template>

    <PackageIndicator :package-id="props.data.package_id" class="mb-5"/>

    <ListInput
        v-model="form.pages"
        :items="props.data.pages"

        :name="props.data.package_name"
    />
    <Button class="mt-4" type="success" @click="updateFields">Save</Button>
    <hr class="my-5"/>
</template>

<style scoped>

</style>
