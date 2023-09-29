<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/Atoms/Button.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import GenericInput from "@/Atoms/GenericInput.vue";
import FileInput from "@/Atoms/FileInput.vue";
import toast from "@/Stores/toast";

const addToast = () => {
    toast.add({
        message: 'Test message',
        type: 'success',
        duration: 4000
    });
};


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
    fileValue: '',
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

        <form @submit.prevent="submit">
            <div>
                <GenericInput
                    :type="'email'"
                    :label="'Email'"
                    v-model="form.email"
                    :error="form.errors.email"
                    :helper="'Use valid email!'"
                    :is_required="true"
                    :input-name="'email'"
                    :input-id="'email'"
                />
            </div>

            <div class="mt-4">
                <GenericInput
                    :type="'password'"
                    :label="'Password'"
                    v-model="form.password"
                    :error="form.errors.password"
                    :helper="'Password must be min 8 characters!'"
                    :is_required="true"
                    :input-name="'password'"
                    :input-id="'password'"
                />
            </div>

            <div class="mt-4">
                <FileInput
                    v-model="form.fileValue"
                    :label="'Upload file'"
                    :helper="'Upload!!'"
                />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember"/>
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-center my-4">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="underline text-sm text-orange-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none"
                >
                    Forgot your password?
                </Link>

                <Button @click="addToast">
                    Add toast
                </Button>
            </div>
            <div class="flex items-center justify-center mt-4">

                <Button
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    :width="100"
                >
                    Log in
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
