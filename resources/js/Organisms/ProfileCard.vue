<script setup>
import {useForm, usePage} from '@inertiajs/vue3';
import {computed} from "vue";

defineProps({
//     firstName: String,
//     lastName: String,
//     email: String,
    img: String,
//     csrfToken: String
})

const page = usePage();

const form = useForm({
    profileImage: null
})

const submitForm = ($event) => {
    form.profileImage = $event.target.files[0];
    if (form.profileImage){
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
    <div class="py-12">
        <div class="mx-auto bg-white rounded-xl shadow-md overflow-hidden p-4 lg:px-8">
            <h2 class="text-center mb-2 text-lg md:text-left md:text-xl">Student Profile</h2>
            <div class="md:flex md:justify-left">
                <div class="mx-auto md:mx-0 w-36">
                    <img class="h-auto max-w-full rounded-full" :src="img" alt="Student profile image"/>
                </div>
                <div class="grid grid-col-1 md:ml-8">
                    <p class="text-lg capitalize tracking-wide font-semibold text-center md:text-left self-center">{{ page.props.auth.user.first_name }} {{ page.props.auth.user.last_name }}</p>
                    <p class="mt-1 text-md leading-tight font-medium text-gray-400 text-center md:text-left md:mt-0">{{ page.props.auth.user.email }}</p>
                    <span class="mt-2 px-4 uppercase text-white text-center bg-gradient rounded-tr-lg self-center rounded-b-lg p-1 mx-auto md:mx-0 md:w-1/2 md:mt-0 md:px-0">package</span>
                    <form @submit.prevent method="POST" enctype="multipart/form-data" class="flex justify-center mt-2">
                        <label class="profile-image-label cursor-pointer md:text-left"
                        for="profile-image-input" :class="labelProgressClasses">
                            Upload Profile Image (Required)</label>
                        <input type="file" class="hidden" name="profile-image"
                               id="profile-image-input" :disabled="form.progress !== null" @change="submitForm($event)"/>
<!--                        <input type="hidden" name="_token" :value="csrfToken" />-->
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

