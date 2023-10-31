<script setup>
import {ref} from 'vue';
import {Tab, TabGroup, TabList, TabPanel, TabPanels} from '@headlessui/vue';
import ListInput from '@/Atoms/ListInput.vue';
import {useForm} from '@inertiajs/vue3';

const props = defineProps({
    data: {
        type: Object,
    },
});
const selected = ref(null);

const form = useForm({
    pages: [],
});

const categories = ref({
    Recent: [
        {
            id: 1,
            title: 'Does drinking coffee make you smarter?',
            date: '5h ago',
            commentCount: 5,
            shareCount: 2,
        },
        {
            id: 2,
            title: 'So you\'ve bought coffee... now what?',
            date: '2h ago',
            commentCount: 3,
            shareCount: 2,
        },
    ],
    Popular: [
        {
            id: 1,
            title: 'Is tech making coffee better or worse?',
            date: 'Jan 7',
            commentCount: 29,
            shareCount: 16,
        },
        {
            id: 2,
            title: 'The most innovative things happening in coffee',
            date: 'Mar 19',
            commentCount: 24,
            shareCount: 12,
        },
    ],
    Trending: [
        {
            id: 1,
            title: 'Ask Me Anything: 10 answers to your questions about coffee',
            date: '2d ago',
            commentCount: 9,
            shareCount: 5,
        },
        {
            id: 2,
            title: 'The worst advice we\'ve ever heard about coffee',
            date: '4d ago',
            commentCount: 1,
            shareCount: 2,
        },
    ],
});
</script>
<template>
    <div class="w-full max-w-md px-2 mt-3 mx-auto sm:px-0">
        <TabGroup>
            <TabList class="flex space-x-1 rounded-xl bg-orange-900/20 p-1">
                <Tab
                    v-for="(pack,id) in props.data"
                    :key="id"
                    as="template"
                >
                    <button
                        :class="[
              'w-full rounded-lg py-2.5 text-sm font-medium capitalize leading-5 text-orange-700',
              'ring-white/60 ring-offset-2 ring-offset-orange-400 focus:outline-none focus:ring-2',
              selected
                ? 'bg-white shadow'
                : 'text-blue-100 hover:bg-white/[0.12] hover:text-white',
            ]"
                    >
                        {{ pack.package_name }}
                    </button>
                </Tab>
            </TabList>

            <TabPanels class="mt-2">
                <TabPanel
                    v-for="(pack, idx) in props.data"
                    :key="idx"
                    :class="[
            'rounded-xl bg-white p-3',
            'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
          ]"
                >

                    <ListInput v-model="form.pages" :is_required="true"
                               :items="pack.pages"
                               class="mt-4"
                               label="Select categories you want to display here"
                               type="checkbox"
                    />
                    <!--                    <ul>-->
                    <!--                        <li-->
                    <!--                            v-for="(item,key) in pack.pages"-->
                    <!--                            :key="key"-->
                    <!--                            class="relative rounded-md p-3 hover:bg-gray-100"-->
                    <!--                        >-->
                    <!--                            <h3 class="text-sm font-medium leading-5">-->
                    <!--                                {{ item }}-->
                    <!--                            </h3>-->

                    <!--                            <a-->
                    <!--                                :class="[-->
                    <!--                  'absolute inset-0 rounded-md',-->
                    <!--                  'ring-blue-400 focus:z-10 focus:outline-none focus:ring-2',-->
                    <!--                ]"-->
                    <!--                                href="#"-->
                    <!--                            ></a>-->
                    <!--                        </li>-->
                    <!--                    </ul>-->
                </TabPanel>
            </TabPanels>
        </TabGroup>
    </div>
</template>
