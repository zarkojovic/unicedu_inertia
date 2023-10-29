<template>
    <div class="w-5/12 mb-3" ref="componentRef">
        <Combobox v-model="selected">
            <div class="relative">
                <div
                    class="relative w-full cursor-default overflow-hidden rounded-xl me-4 bg-white border focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75 focus-visible:ring-offset-2 focus-visible:ring-offset-orange-300 sm:text-sm"
                >
                    <ComboboxInput
                        class="w-full border-none text-md text-gray-900 focus:ring-0 p-3"
                        :displayValue="(item) => item.title"
                        @change="query = $event.target.value"
                        v-focus

                    />
                </div>
                <ComboboxOptions
                        class="z-50 absolute max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                        :static="true"
                        >
                        <div
                            v-if="filteredItems.length === 0 && query !== ''"
                            class="relative cursor-default select-none py-2 px-4 text-gray-700"
                        >
                            Nothing found.
                        </div>

                        <ComboboxOption
                            v-for="item in filteredItems"
                            as="template"
                            :key="item.field_id"
                            :value="item"
                            v-slot="{ selected, active }"
                            @click="toggleCombobox"
                        >
                            <Link
                                href="/admin/fields-add"
                                method="post"
                                as="li"
                                :data="{ field_id: item.field_id, field_category_id: props.catId, order: props.order}"
                                class="relative cursor-pointer select-none py-2 pl-10 pr-4"
                                :class="{
                                      'bg-orange-600 text-white': active,
                                      'text-gray-900': !active,
                                    }"
                            >
                                    <span
                                        class="block truncate"
                                        :class="{ 'font-medium': selected, 'font-normal': !selected }"
                                    >
                                      {{ item.title }}
                                    </span>
                                    <span
                                        v-if="selected"
                                        class="absolute inset-y-0 left-0 flex items-center pl-3"
                                        :class="{ 'text-white': active, 'text-orange-600': !active }"
                                    >
                                    </span>
                            </Link>
                        </ComboboxOption>
                    </ComboboxOptions>
            </div>
        </Combobox>
    </div>
</template>

<script setup>
import {ref, computed, onMounted, onBeforeUnmount} from 'vue';
import { useFetch } from '@/Composables/fetch.js';
import { Link } from '@inertiajs/vue3'
import {
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
} from '@headlessui/vue'

const emits = defineEmits(['hide']);

let items = ref([]);
let selected = ""
let query = ref('')
const componentRef = ref(null);


//INJECT
// const addFieldToCategory = inject("addFieldFunction");
// @click="addFieldToCategory(props.catId, item)"


//PROPS
const props = defineProps({
    catId: Number,
    order: Number
})

//CUSTOM DIRECTIVE
const vFocus = {
    mounted: (el) => el.focus()
};

//LIFECYCLE HOOKS
onMounted(async () => {
    try {
        items.value = await useFetch('/admin/fields-fetch');
    } catch (error) {
        console.error(error);
    }

    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

const handleClickOutside = (event) => {
    if (componentRef.value && !componentRef.value.contains(event.target)) {
        toggleCombobox();
    }
};

//COMPUTED
let filteredItems = computed(() => {
    return query.value === ''
        ? items.value
        : items.value.filter((item) =>
                    item.title
                        .toLowerCase()
                        .replace(/\s+/g, '')
                        .includes(query.value.toLowerCase().replace(/\s+/g, ''))
                ).slice(0, 20);
});

//METHODS
//UBACI @blur="toggleCombobox"
const toggleCombobox = (() => {
    query = ''
    emits("hide");
});

// const addFieldToCategory = ((catId, fieldId, order) => {
//
// })

// const delaySearch = (() => {
//     let searchTimeout;
//     let searchedItems = ref([]);
//
//     clearTimeout(searchTimeout);
//     searchTimeout = setTimeout(function() {
//         searchedItems.value = items.value.filter((item) =>
//             item.title
//                 .toLowerCase()
//                 .replace(/\s+/g, '')
//                 .includes(query.value.toLowerCase().replace(/\s+/g, ''))
//         ).slice(0, 20);
//     }, 400);
//     console.log(searchedItems.value)
//     return searchedItems.value;
//     // console.log(filteredItems.value)
// })
</script>
