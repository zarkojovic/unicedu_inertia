<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/Atoms/Button.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import GenericInput from "@/Atoms/GenericInput.vue";
import toast from '@/Stores/toast.js';
import ListInput from "@/Atoms/ListInput.vue";
import {ref} from "vue";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});


const radioItems = ['Item 1', 'Item 2', 'Item 3'];
const selectedRadioItem = ref([]);

const form = useForm({
    email: '',
    password: '',
    remember: false,

});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });

    // form.post('/test');


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

        <form @submit.prevent="submit" class="grid grid-cols-1 md:gap-x-8 gap-2 gap-y-4">
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

            <div>
                <GenericInput
                    :input-name="'password'"
                    :input-id="'password'"
                    :type="'password'"
                    :label="'Password'"
                    v-model="form.password"
                    :error="form.errors.password"
                    :helper="'Password must be min 8 characters!'"
                    :is_required="true"
                    @keyup.enter="submit"
                />
            </div>


            <div>
                <Button
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    :width="100"
                    :type="'primary'"
                    @click="submit"
                >
                    Log in
                </Button>
            </div>
        </form>
        <div class="mx-auto mt-2 w-max">
            <span class="text-sm">New to Poland Study? </span>
            <Link
                :href="route('register')"
                class="underline text-sm text-orange-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none"
            >
                Sign up
            </Link>
        </div>

        <div class="mx-auto w-max">
            <span class="text-sm">Forgot your password? </span>
            <Link
                v-if="canResetPassword"
                :href="route('password.request')"
                class="underline text-sm text-orange-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none"
            >
                Reset it here
            </Link>
        </div>
    </GuestLayout>
</template>
