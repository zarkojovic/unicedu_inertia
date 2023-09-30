<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/Atoms/Button.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import GenericInput from "@/Atoms/GenericInput.vue";

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
    remember: false,

});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in"/>
        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 md:gap-x-8 gap-2 gap-y-5">
            <div>
                <GenericInput
                    :type="'email'"
                    :label="'Email'"
                    v-model="form.email"
                    :error="form.errors.email"
                    :helper="'Use valid email!'"
                    :is_required="true"
                />
            </div>

            <div>
                <GenericInput
                    :type="'password'"
                    :label="'Password'"
                    v-model="form.password"
                    :error="form.errors.password"
                    :helper="'Password must be min 8 characters!'"
                    :is_required="true"
                />
            </div>

            <div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember"/>
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                </label>
            </div>

            <div class="mx-auto">
                <span class="text-sm">Forgot your password? </span>
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="underline text-sm text-orange-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none"
                >
                    Reset it here
                </Link>


            </div>
            <div>

                <Button
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    :width="100"
                >
                    Log in
                </Button>
            </div>

            <div class="mx-auto">
                <span class="text-sm">New to Poland Study? </span>
                <Link
                    :href="route('register')"
                    class="underline text-sm text-orange-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none"
                >
                    Sign up
                </Link>


            </div>
        </form>
    </GuestLayout>
</template>
