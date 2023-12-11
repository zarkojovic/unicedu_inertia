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

const primaryColor = ref(props.data.primary_color);
const secondaryColor = ref(props.data.secondary_color);
const textColor = ref(props.data.text_color);

const form = useForm({
    pages: formattedActiveItems.value,
    package_id: props.data.package_id,
    colors: [primaryColor.value, secondaryColor.value, textColor.value],
});

const updateFields = () => {
    form.colors = [primaryColor.value, secondaryColor.value, textColor.value];
    form.post('/admin/set-package-pages', {
        preserveScroll: true,
    });
};

</script>

<template>

    <PackageIndicator :package-id="props.data.package_id" class="mb-5"/>

    <label for="first_color">First Color</label>
    <input v-model="primaryColor" class="w-full" type="color">

    <label for="second_color">Second Color</label>
    <input v-model="secondaryColor" class="w-full mb-4" type="color">

    <label for="second_color">Text Color</label>
    <input v-model="textColor" class="w-full mb-4" type="color">

    <h2>Pages</h2>
    <ListInput
        v-model="form.pages"
        :items="props.data.pages"

        :name="props.data.package_name"
    />
    <Button class="mt-4" type="success" @click="updateFields">Save</Button>
</template>

<style scoped>

</style>
