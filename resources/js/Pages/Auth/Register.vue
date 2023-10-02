<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import GenericInput from "@/Atoms/GenericInput.vue";

const form = useForm({
    name: '',
    lastName: '',
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
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="container mb-3">
            <h1 class="md:text-2xl text-xl text-center">Welcome to Poland Study!</h1>
            <h2 class="text-sm text-gray-400 text-center">Please enter your details</h2>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-2 md:gap-x-8 gap-2 gap-y-5">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="last-name" value="Last name" />

                <TextInput
                    id="last-name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.lastName"
                    required="required"
                    autofocus="autofocus"
                    autocomplete="lastName"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="col-span-2">
                <InputLabel for="email" value="Email" />


                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="col-span-2">
                <InputLabel for="phone" value="Phone" />

                <TextInput
                    id="phone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    required="required"
                    autofocus="autofocus"
                    autocomplete="phone"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="col-span-2">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="col-span-2">
                <InputLabel for="password_confirmation" value="Confirm Password" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="flex items-center justify-end mt-4 col-span-2">
                <PrimaryButton class="w-full flex items-center justify-center text-white" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
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
