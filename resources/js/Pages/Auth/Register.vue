<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import GenericInput from "@/Atoms/GenericInput.vue";

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const validateFirstName = () => {
    const name = form.first_name;

    if (!name) {
        form.errors.first_name = 'The name field is required.';
    } else {
        form.errors.first_name = '';
    }
};

const validateLastName = () => {
    const surname = form.last_name;

    if (!surname) {
        form.errors.last_name = 'The surname field is required.';
    } else {
        form.errors.last_name = '';
    }
};

const validateEmail = () => {
    const email = form.email;
    const emailRegex = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;

    if (!email) {
        form.errors.email = 'The email field is required.';
    } else if (!emailRegex.test(email)) {
        form.errors.email = 'Invalid email format';
    } else {
        form.errors.email = '';
    }
};

const validatePhone = () => {
    const phone = form.phone;

    if (!phone) {
        form.errors.phone = 'The phone number field is required.';
    }
    else {
        form.errors.phone = '';
    }
};

const validatePassword = () => {
    const password = form.password;

    if (!password) {
        form.errors.password = 'The password field is required.';
    } else if (password.length < 8) {
        form.errors.password = 'Password must be at least 8 characters long';
    } else {
        form.errors.password = '';
    }
};
const validateRepeatPassword = () => {
    const password = form.password;
    const repeatPassword = form.password_confirmation;

    if (!repeatPassword) {
        form.errors.password_confirmation = 'The repeat password field is required.';
    } else if (repeatPassword !== password) {
        form.errors.password_confirmation = 'Passwords do not match';
    } else {
        form.errors.password_confirmation = '';
    }
};

const validateRegistrationForm = () => {
    validateFirstName();
    validateLastName();
    validateEmail();
    validatePhone();
    validatePassword();
    validateRepeatPassword();

    if (
        form.errors.first_name === '' &&
        form.errors.last_name === '' &&
        form.errors.email === '' &&
        form.errors.phone === '' &&
        form.errors.password === '' &&
        form.errors.password_confirmation === ''
    ) {
        submit();
    }
};
</script>

<template>
    <GuestLayout>
        <Head title="Register"/>

        <div class="container mb-3">
            <h1 class="md:text-2xl text-xl text-center">Welcome to Poland Study!</h1>
            <h2 class="text-sm text-gray-400 text-center">Please enter your details</h2>
        </div>

        <form @submit.prevent="validateRegistrationForm" class="grid grid-cols-2 md:gap-x-8 gap-2 gap-y-5">
            <div>
                <InputLabel for="first-name" value="Name"/>

                <TextInput
                    @focusout="validateFirstName"
                    id="first-name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.first_name"
                />

                <InputError class="mt-2" :message="form.errors.first_name"/>
            </div>

            <div>
                <InputLabel for="last-name" value="Last name"/>

                <TextInput
                    @focusout="validateLastName"
                    id="last-name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.last_name"
                />

                <InputError class="mt-2" :message="form.errors.last_name"/>
            </div>

            <div class="col-span-2">
                <InputLabel for="email" value="Email"/>


                <TextInput
                    @focusout="validateEmail"
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                />

                <InputError class="mt-2" :message="form.errors.email"/>
            </div>

            <div class="col-span-2">
                <InputLabel for="phone" value="Phone"/>

                <TextInput
                    @focusout="validatePhone"
                    id="phone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                />

                <InputError class="mt-2" :message="form.errors.phone"/>
            </div>

            <div class="col-span-2">
                <InputLabel for="password" value="Password"/>

                <TextInput
                    @focusout="validatePassword"
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                />

                <InputError class="mt-2" :message="form.errors.password"/>
            </div>

            <div class="col-span-2">
                <InputLabel for="password_confirmation" value="Confirm Password"/>

                <TextInput
                    @focusout="validateRepeatPassword"
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation"/>
            </div>

            <div class="flex items-center justify-end mt-4 col-span-2">
                <PrimaryButton class="w-full flex items-center justify-center text-white"
                               :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>

            <div class="flex items-center justify-center col-span-2">
                <p class="text-sm mr-1">Already have an account? </p>
                <Link
                    :href="route('login')"
                    class="underline text-orange-500 text-sm rounded-md focus:outline-none"
                >
                    Sign in
                </Link>

            </div>
        </form>
    </GuestLayout>
</template>
