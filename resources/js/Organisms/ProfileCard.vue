<script setup>
import {useForm, usePage} from '@inertiajs/vue3';
import {computed} from "vue";

defineProps({
//     firstName: String,
//     lastName: String,
//     email: String,
    phone: String,
    img: String,
//     csrfToken: String
})

const page = usePage();

const form = useForm({
    profileImage: null
})

const submitForm = ($event) => {
    form.profileImage = $event.target.files[0];
    if (form.profileImage) {
        form.post("/image/edit");
    }
}

const labelProgressClasses = computed(() => ({
    'text-orange-500': !form.progress,
    'text-orange-200': form.progress,
    'cursor-not-allowed': form.progress,
    'select-none': form.progress,
}))
</script>

<template>
    <div class="py-6 mt-6">
        <div class="mx-auto bg-white rounded-3xl shadow-md overflow-hidden p-5 lg:px-8">
            <h2 class="text-center mb-5 text-lg md:text-left md:text-xl">Student Profile</h2>
            <div class="md:flex md:justify-left">
                <div class="mx-auto md:mx-0 w-32">
                    <img class="h-auto max-w-full rounded-full" :src="img" alt="Student profile image"/>
                </div>
                <div class="grid grid-col-1 md:ml-8 content-between">
                    <div>
                        <p class="text-lg capitalize tracking-wide font-semibold text-neutral-800 text-center md:text-left self-center">
                            {{ page.props.auth.user.first_name }} {{ page.props.auth.user.last_name }}</p>
                        <p class="text-md leading-tight font-medium text-gray-400 text-center md:text-left my-1">
                            {{ page.props.auth.user.email }}</p>
                        <p class="text-md leading-tight font-medium text-gray-400 text-center md:text-left mt-1">
                            {{ page.props.auth.user.phone }}</p>

                    </div>
                    <form @submit.prevent method="POST" enctype="multipart/form-data" class="flex justify-between mt-5">
                        <input type="file" class="hidden" name="profile-image"
                               id="profile-image-input" :disabled="form.progress !== null"
                               @change="submitForm($event)"/>
                        <!--                        <input type="hidden" name="_token" :value="csrfToken" />-->
                        <label class="profile-image-label cursor-pointer md:text-left align-bottom "
                               for="profile-image-input" :class="labelProgressClasses">
                            Upload Profile Image</label>
                    </form>

                </div>
            </div>
        </div>
    </div>
</template>

