<script setup>
import {defineProps, inject, onMounted, provide, ref} from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import {Link, usePage} from '@inertiajs/vue3';
import Button from '@/Atoms/Button.vue';
import Modal from '@/Molecules/Modal.vue';
import FieldsForm from '@/Molecules/FieldsForm.vue';

const page = usePage();

const {toggleSidebar} = defineProps(['toggleSidebar']);

const isUserMenuOpen = ref(false);

const toggleUserMenu = () => {
    isUserMenuOpen.value = !isUserMenuOpen.value;
};

const openModal = ref(false);

const navIcon = ref(null);

const navBtnType = inject('navBtnType', ref(''));

const formItems = ref({
    formItems: {},
});

provide('formItems', formItems);

onMounted(() => {
    document.addEventListener('click', (e) => {
        if (e.target === navIcon.value || e.target.parentNode === navIcon.value) {
            return;
        }
        isUserMenuOpen.value = false;
    });

});

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
                <div class="flex">

                    <Link v-if="navBtnType === 'studentProfile'" :href="route('applications')">
                        <Button :type="'primary'" class="me-3">
                            Apply Now
                        </Button>
                    </Link>

                    <div v-else-if="navBtnType === 'applicationsPage'">
                        <Button class="me-3" @click="openModal = true">
                            Open modal
                        </Button>
                        <Modal v-if="openModal" @close="openModal = false">
                            <template v-slot:modalTitle>
                                <h2 class="text-lg">Add new application</h2>
                            </template>
                            <template v-slot:modalContent>
                                <div class="grid col-span-2  grid-cols-2 sm:grid-cols-2 gap-4">
                                    <FieldsForm :items="page.props.sidebar_fields"/>
                                </div>
                            </template>
                        </Modal>
                    </div>

                    <div class="hidden lg:block">
                        <button ref="navIcon"
                                class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                                type="button"
                                @click="toggleUserMenu">
                            <img :src="page.props.auth.img " alt="user photo" class="w-8 h-8 rounded-full">
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex">
            <div v-if="isUserMenuOpen"
                 class="z-50 right-10 absolute text-base list-none bg-white divide-y divide-gray-100 rounded shadow">
                <div class="px-4 py-3">
                    <p class="text-sm text-gray-900">
                        {{ page.props.auth.user.first_name }} {{ page.props.auth.user.last_name }}
                    </p>
                    <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                        {{ page.props.auth.user.email }}
                    </p>
                </div>
                <ul class="py-1" role="none">
                    <li>
                        <Link :href="route('logout')" as="button"
                              class="flex w-full block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                              method="post"
                              role="menuitem"
                              type="button">
                            Sign out
                        </Link>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
</template>
