<template>
    <div class="w-5/12 mb-3">
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
                        @blur="toggleCombobox"
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
                            :key="item.id"
                            :value="item"
                            v-slot="{ selected, active }"
                        >
                            <li
                                class="relative cursor-default select-none py-2 pl-10 pr-4"
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
                            </li>
                        </ComboboxOption>
                    </ComboboxOptions>
            </div>
        </Combobox>
    </div>
</template>

<script setup>
import {ref, computed, onMounted, watch, nextTick} from 'vue';
import { useFetch } from '@/Composables/fetch.js';

import {
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
    TransitionRoot,
} from '@headlessui/vue'

const emits = defineEmits(['hide', 'blur']);

// let items = [];
//[{id: 0, title: "naslov"}]
let items = ref([]);
let selected = ""
let query = ref('')
// let filteredItems = ref([]);

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
});


//COMPUTED
// return items ? items : [{id: 0, title: "naslov"}]
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
const toggleCombobox = (() => {
    query = ''
    emits("hide");
});
</script>
