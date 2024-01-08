<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, useForm, usePage} from '@inertiajs/vue3';
import Button from '@/Atoms/Button.vue';
import {ref} from 'vue';
import Modal from '@/Molecules/Modal.vue';
import GenericInput from '@/Atoms/GenericInput.vue';
import toast from '@/Stores/toast.js';

const props = defineProps({
    agentStudents: {
        type: Array,
    },
});

const page = usePage();

const openModal = ref(false);

const form = useForm({
    email: '',
    first_name: '',
    last_name: '',
    phone: '',
});

const checkAndSubmit = () => {
    if (form.email === '') {
        form.errors.email = 'The email field is required.';
        return;
    } else {
        form.errors.email = null;
    }
    if (form.first_name === '') {
        form.errors.first_name = 'The first name field is required.';
        return;
    } else {
        form.errors.first_name = null;
    }
    if (form.last_name === '') {
        form.errors.last_name = 'The last name field is required.';
        return;
    } else {
        form.errors.last_name = null;
    }
    if (form.phone === '') {
        form.errors.phone = 'The phone field is required.';
        return;
    } else {
        form.errors.phone = null;
    }
    form.post('/agent/addNewStudentToAgent', {
        onSuccess: () => {
            toast.add({
                message: 'Student added!',
                type: 'success',
            });
            openModal.value = false;
        },
        preserveScroll: true,
    });
};


</script>

<template>
    <Head title="Profile"/>

    <AuthenticatedLayout>

        <div class="py-6 mt-10">
            <div class="mx-auto bg-white rounded-3xl shadow-md overflow-hidden ">
                <div class="flex items-center">
                    <h1 class="text-3xl font-bold p-3 mb-2">My Students</h1>

                    <Button class="ml-auto mr-3" @click="openModal = true">
                        Add Student
                    </Button>
                    <Modal v-if="openModal" @close="openModal=false">
                        <template v-slot:modalTitle>Add Student</template>
                        <template v-slot:modalContent>
                            <div class="w-64">
                                <GenericInput v-model="form.email"
                                              :error="form.errors.email"
                                              class="mb-5"
                                              input-name="email"
                                              label="Email"
                                              placeholder="Enter Student Email"
                                              type="email"
                                />
                                <GenericInput v-model="form.first_name"
                                              :error="form.errors.first_name"
                                              class="mb-5"
                                              input-name="first_name"
                                              label="First Name"
                                              placeholder="Enter Student First Name"
                                              type="text"
                                />
                                <GenericInput v-model="form.last_name"
                                              :error="form.errors.last_name"
                                              class="mb-5"
                                              input-name="last_name"
                                              label="Last Name"
                                              placeholder="Enter Student Last Name"
                                              type="text"
                                />
                                <GenericInput v-model="form.phone"
                                              :error="form.errors.phone"
                                              class="mb-5"
                                              input-name="phone"
                                              label="Phone"
                                              placeholder="Enter Student Phone"
                                              type="text"
                                />
                            </div>
                            <Button class="mt-5" type="success" @click="checkAndSubmit()">Add Student</Button>
                        </template>
                        <template v-slot:modalFooter>
                            <Button :type="'muted'" @click="openModal=false">Close</Button>
                        </template>
                    </Modal>
                </div>
                <hr>
                <div v-for="agentStudent in props.agentStudents" class="p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="mx-auto md:mx-0 w-28">
                                <img :src="page.props.images_root + agentStudent.profile_image"
                                     alt="Student profile image"
                                     class="h-auto max-w-full rounded-full"/>
                            </div>
                            <div class="ms-3">
                                <h2 class="text-xl font-bold">{{
                                        agentStudent.first_name + ' ' + agentStudent.last_name
                                    }}</h2>
                                <p class="text-gray-600">{{ agentStudent.email }}</p>
                                <p class="text-gray-600">{{ agentStudent.phone ?? '' }}</p>
                            </div>
                        </div>
                        <div>
                            <Link :href="route('agentStudentProfile', agentStudent.user_id)"
                            >
                                <Button>
                                    View Profile
                                </Button>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
