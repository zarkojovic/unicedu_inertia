<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/Atoms/Button.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import GenericInput from '@/Atoms/GenericInput.vue';
import {useReCaptcha} from 'vue-recaptcha-v3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    recaptcha: '',
});
//
const {executeRecaptcha, recaptchaLoaded} = useReCaptcha();

const recaptcha = async () => {
    await recaptchaLoaded();
    form.recaptcha = await executeRecaptcha('login');
    submit();
};

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
        preserveScroll: true,
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in"/>
        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <div class="container mb-3">
            <h1 class="md:text-2xl text-xl text-center">Welcome to Poland Study!</h1>
            <h2 class="text-sm text-gray-400 text-center">Please enter your details</h2>
        </div>

        <form class="grid grid-cols-1 md:gap-x-8 gap-2 gap-y-4" @submit.prevent="recaptcha">
            <div>
                <GenericInput
                    v-model="form.email"
                    :error="form.errors.email"
                    :input-id="'email'"
                    :input-name="'email'"
                    :label="'Email'"
                    :type="'email'"
                />
            </div>

            <div>
                <GenericInput
                    v-model="form.password"
                    :error="form.errors.password"
                    :input-id="'password'"
                    :input-name="'password'"
                    :label="'Password'"
                    :type="'password'"
                    @keyup.enter="recaptcha"
                />
            </div>
            <div>
                <Button
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    :type="'primary'"
                    :width="100"
                    @click="recaptcha"
                >
                    Log in
                </Button>
            </div>
        </form>
        <div class="mx-auto mt-2 w-max">

            <span class="text-sm">New to Poland Study? </span>
            <Link
                :href="route('register')"
                class="text-sm text-orange-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none"
            >
                Sign up
            </Link>
        </div>

        <div class="mx-auto w-max">
            <span class="text-sm">Forgot your password? </span>
            <Link
                v-if="canResetPassword"
                :href="route('password.request')"
                class="text-sm text-orange-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none"
            >
                Reset it here
            </Link>
        </div>
    </GuestLayout>
</template>
