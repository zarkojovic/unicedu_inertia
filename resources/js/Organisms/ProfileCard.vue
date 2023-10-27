<script setup>
import {useForm, usePage} from '@inertiajs/vue3';
import {computed} from "vue";
import Modal from "@/Molecules/Modal.vue";
import { ref } from 'vue'
import Button from "@/Atoms/Button.vue";

defineProps({
    img: String,
})

// const that = this;

const showModal = ref(false);
const imagePreview = ref(''); // Store the image preview URL

const page = usePage();

const form = useForm({
    profileImage: null
})

const submitForm = ($event) => {
    form.profileImage = $event.target.files[0];
    if (form.profileImage) {
        // form.post("/image/edit");
        imagePreview.value = URL.createObjectURL(form.profileImage);
        showModal.value = true;
    }
}

// Handle the confirmation action
const handleConfirmation = () => {
    showModal.value = false;
    form.post("/image/edit");
}

// Handle the modal close action
const handleCloseModal = () => {
    showModal.value = false;
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
                        <label class="profile-image-label cursor-pointer md:text-left align-bottom "
                               for="profile-image-input" :class="labelProgressClasses">
                            Upload Profile Image</label>
                    </form>
                    <Modal v-if="showModal" :imagePreview="imagePreview" @confirmed="handleConfirmation" @close="handleCloseModal">
                        <template v-slot:modalTitle>
                            <h1 class="text-center bg-amber-200 p-5 rounded-md">Warning! This image will be used for all your future college applications:</h1>
                        </template>
                        <template v-slot:modalContent>
                            <div class="flex justify-center">
                                <img :src="imagePreview" class="md:w-5/12 w-10/12 h-auto rounded-sm" alt="image preview"/>
                            </div>
                        </template>
                        <template v-slot:modalFooter>
                            <h1 class="text-center mb-3">Are you sure you want to upload this image to the server?</h1>
                            <div class="flex justify-end">
                                <Button :type="'success'" @click="handleConfirmation" class="me-2">Confirm</Button>
                                <Button :type="'danger'" @click="handleCloseModal">Cancel</Button>
                            </div>
                        </template>
                    </Modal>
                </div>
            </div>
        </div>
    </div>
</template>

