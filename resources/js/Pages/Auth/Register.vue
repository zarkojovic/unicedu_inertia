<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';

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
</script>

<template>
    <GuestLayout>
        <Head title="Register"/>

        <div class="container mb-3">
            <h1 class="md:text-2xl text-xl text-center">Welcome to Poland Study!</h1>
            <h2 class="text-sm text-gray-400 text-center">Please enter your details</h2>
        </div>

        <form class="grid grid-cols-2 md:gap-x-8 gap-2 gap-y-5" @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name"/>

                <TextInput
                    id="first-name"
                    v-model="form.first_name"
                    autocomplete="name"
                    autofocus
                    class="mt-1 block w-full"
                    required
                    type="text"
                />

                <InputError :message="form.errors.name" class="mt-2"/>
            </div>

            <div>
                <InputLabel for="last-name" value="Last name"/>

                <TextInput
                    id="last-name"
                    v-model="form.last_name"
                    autocomplete="lastName"
                    autofocus="autofocus"
                    class="mt-1 block w-full"
                    required="required"
                    type="text"
                />

                <InputError :message="form.errors.name" class="mt-2"/>
            </div>

            <div class="col-span-2">
                <InputLabel for="email" value="Email"/>


                <TextInput
                    id="email"
                    v-model="form.email"
                    autocomplete="username"
                    class="mt-1 block w-full"
                    required
                    type="email"
                />

                <InputError :message="form.errors.email" class="mt-2"/>
            </div>

            <div class="col-span-2">
                <InputLabel for="phone" value="Phone"/>

                <TextInput
                    id="phone"
                    v-model="form.phone"
                    autocomplete="phone"
                    autofocus="autofocus"
                    class="mt-1 block w-full"
                    required="required"
                    type="text"
                />

                <InputError :message="form.errors.phone" class="mt-2"/>
            </div>

            <div class="col-span-2">
                <InputLabel for="password" value="Password"/>

                <TextInput
                    id="password"
                    v-model="form.password"
                    autocomplete="new-password"
                    class="mt-1 block w-full"
                    required
                    type="password"
                />

                <InputError :message="form.errors.password" class="mt-2"/>
            </div>

            <div class="col-span-2">
                <InputLabel for="password_confirmation" value="Confirm Password"/>

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    autocomplete="new-password"
                    class="mt-1 block w-full"
                    required
                    type="password"
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
