<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import {useReCaptcha} from 'vue-recaptcha-v3';

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    recaptcha: '',
});

const {executeRecaptcha, recaptchaLoaded} = useReCaptcha();
const submit = async () => {

    await recaptchaLoaded();
    form.recaptcha = await executeRecaptcha('register');
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
        form.errors.email = 'Please enter a valid email address (e.g., example@example.com)';
    } else {
        form.errors.email = '';
    }
};

const validatePhone = () => {
    const phone = form.phone;

    if (!phone) {
        form.errors.phone = 'The phone number field is required.';
    } else {
        form.errors.phone = '';
    }
};

const validatePassword = () => {
    const password = form.password;
    const passwordRegex = /^(?=[A-Za-z0-9@#$%^&+!=]+$)^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@#$%^&+!=])(?=.{8,}).*$/;

    if (!password) {
        form.errors.password = 'The password field is required.';
    } else if (!passwordRegex.test(password)) {
        form.errors.password = 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character from @#$%^&+!=';
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

        <form class="grid grid-cols-2 md:gap-x-8 gap-2 gap-y-5" @submit.prevent="validateRegistrationForm">
            <div>
                <InputLabel for="first-name" value="First Name"/>

                <TextInput
                    id="first-name"
                    v-model="form.first_name"
                    class="mt-1 block w-full"
                    type="text"
                    @focusout="validateFirstName"
                />

                <InputError :message="form.errors.first_name" class="mt-2"/>
            </div>

            <div>
                <InputLabel for="last-name" value="Last Name"/>

                <TextInput
                    id="last-name"
                    v-model="form.last_name"
                    class="mt-1 block w-full"
                    type="text"
                    @focusout="validateLastName"
                />

                <InputError :message="form.errors.last_name" class="mt-2"/>
            </div>

            <div class="col-span-2">
                <InputLabel for="email" value="Email"/>


                <TextInput
                    id="email"
                    v-model="form.email"
                    class="mt-1 block w-full"
                    type="email"
                    @focusout="validateEmail"
                />

                <InputError :message="form.errors.email" class="mt-2"/>
            </div>

            <div class="col-span-2">
                <InputLabel for="phone" value="Phone"/>

                <TextInput
                    id="phone"
                    v-model="form.phone"
                    class="mt-1 block w-full"
                    type="text"
                    @focusout="validatePhone"
                />

                <InputError :message="form.errors.phone" class="mt-2"/>
            </div>

            <div class="col-span-2">
                <InputLabel for="password" value="Password"/>

                <TextInput
                    id="password"
                    v-model="form.password"
                    class="mt-1 block w-full"
                    type="password"
                    @focusout="validatePassword"
                />

                <InputError :message="form.errors.password" class="mt-2"/>
            </div>

            <div class="col-span-2">
                <InputLabel for="password_confirmation" value="Confirm Password"/>

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    class="mt-1 block w-full"
                    type="password"
                    @focusout="validateRepeatPassword"
                />

                <InputError :message="form.errors.password_confirmation" class="mt-2"/>
            </div>

            <div class="flex items-center justify-end mt-4 col-span-2">
                <PrimaryButton :class="{ 'opacity-25': form.processing }"
                               :disabled="form.processing" class="w-full flex items-center justify-center text-white">
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
