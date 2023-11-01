<script setup>
import {defineProps, inject, ref} from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import {Link, useForm, usePage} from '@inertiajs/vue3';
import Button from '@/Atoms/Button.vue';
import {Menu, MenuButton, MenuItem, MenuItems} from '@headlessui/vue';
import {addIcons} from 'oh-vue-icons';
import {MdLockreset, MdLogoutOutlined} from 'oh-vue-icons/icons';
import ApplicationModal from '@/Organisms/ApplicationModal.vue';

addIcons(MdLogoutOutlined, MdLockreset);

const page = usePage();

const {toggleSidebar} = defineProps(['toggleSidebar']);

const openModal = ref(false);

const navBtnType = inject('navBtnType', ref(''));

const form = useForm({});
const refreshFields = () => {
    form.post('/admin/fields_fields');

};
</script>

<template>
    <nav class="fixed top-0 z-40 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start">
                    <button
                        class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                        type="button"
                        @click="toggleSidebar">
                        <span class="sr-only">Open sidebar</span>
                        <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd"
                                  d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"
                                  fill-rule="evenodd"></path>
                        </svg>
                    </button>
                    <a class="flex ml-2 md:mr-24" href="#">
                        <ApplicationLogo class="h-10 mr-3"/>
                        <span class="self-center text-md font-semibold sm:text-md whitespace-nowrap dark:text-white">Student Platform</span>
                    </a>
                </div>

                <div class="flex justify-center">
                    <Link v-if="navBtnType === 'studentProfile'" :href="route('applications')">
                        <Button :type="'primary'" class="me-3">
                            Apply Now
                        </Button>
                    </Link>

                    <div v-else-if="navBtnType === 'applicationsPage'">
                        <Button class="me-3" @click="openModal = true">
                            Apply Now
                        </Button>
                        <ApplicationModal v-model="openModal"/>
                    </div>

                    <Button v-if="navBtnType === 'adminFields'" class="me-3" type="primary" @click="refreshFields">
                        Refresh Fields
                    </Button>


                    <Menu as="div" class="relative hidden lg:inline-block text-left justify-center">

                        <div class="flex justify-center">
                            <MenuButton
                                class="inline-flex w-full justify-center text-sm font-medium text-white"
                            >
                                <img :src="page.props.auth.img " alt="user photo"
                                     class="w-8 h-8 rounded-full hover:ring-2 ring-orange-500 transition ease-in-out">
                            </MenuButton>
                        </div>

                        <transition
                            enter-active-class="transition duration-100 ease-out"
                            enter-from-class="transform scale-95 opacity-0"
                            enter-to-class="transform scale-100 opacity-100"
                            leave-active-class="transition duration-75 ease-in"
                            leave-from-class="transform scale-100 opacity-100"
                            leave-to-class="transform scale-95 opacity-0"
                        >
                            <MenuItems
                                class="absolute right-0 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                            >
                                <div class="px-1 py-1 px-2 py-2">
                                    <p class="text-sm text-gray-900 mb-1">
                                        {{ page.props.auth.user.first_name }} {{ page.props.auth.user.last_name }}
                                    </p>
                                    <p class="text-sm font-medium text-gray-400 leading-tight" role="none">
                                        {{ page.props.auth.user.email }}
                                    </p></div>
                                <div class="px-1 py-1">
                                    <MenuItem v-slot="{ active }">
                                        <button
                                            :class="[
                                                  active ? 'bg-orange-500 text-white' : 'text-gray-900',
                                                  'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                                                ]">
                                            <Link :href="route('logout')" as="a"
                                                  method="post">
                                                <v-icon class="mr-2 h-5 w-5" name="md-lockreset"/>
                                                Change password
                                            </Link>
                                        </button>
                                    </MenuItem>

                                </div>

                                <div class="px-1 py-1">

                                    <MenuItem v-slot="{ active }">
                                        <Link :href="route('logout')" as="button" class="w-full"
                                              method="post">
                                            <button
                                                :class="[
                  active ? 'bg-orange-500 text-white' : 'text-gray-900',
                  'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                ]"
                                            >

                                                <v-icon class="mr-2 h-5 w-5" name="md-logout-outlined"/>
                                                Sign out
                                            </button>
                                        </Link>

                                    </MenuItem>

                                </div>
                            </MenuItems>
                        </transition>
                    </Menu>
                </div>

            </div>
        </div>
    </nav>
</template>
