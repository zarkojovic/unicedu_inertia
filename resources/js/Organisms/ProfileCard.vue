<script setup>
import {useForm, usePage} from '@inertiajs/vue3';
import {computed, ref} from 'vue';
import Modal from '@/Molecules/Modal.vue';
import Button from '@/Atoms/Button.vue';
import PackageIndicator from '@/Atoms/PackageIndicator.vue';

defineProps({
    img: String,
});

const showModal = ref(false);
const imagePreview = ref(false); // Store the image preview URL

const page = usePage();

const form = useForm({
    profileImage: null,
});

const agreedToUsePicture = ref(false);

const isNotValidType = ref(false);

//Disabled if user didn't accept usage of picture or didn't upload picture
const isSubmitButtonDisabled = computed(() => {
    return !(agreedToUsePicture.value && imagePreview.value && !isNotValidType.value);
});
const openProfilePictureModal = () => {
    showModal.value = !showModal.value;
    imagePreview.value = false;
    agreedToUsePicture.value = false;
};
const submitForm = ($event) => {
    const type = $event.target.files[0].type;
    const allowedTypes = ['image/png', 'image/jpg', 'image/jpeg'];
    if (allowedTypes.includes(type)) {
        isNotValidType.value = false;
        form.profileImage = $event.target.files[0];
        if (form.profileImage) {
            imagePreview.value = URL.createObjectURL(form.profileImage);
        }
    } else {
        isNotValidType.value = true;
    }
};

// Handle the confirmation action
const handleConfirmation = () => {
    showModal.value = false;
    form.post('/image/edit');
    form.profileImage = null;
    isNotValidType.value = false;
};

// Handle the modal close action
const handleCloseModal = () => {
    showModal.value = false;
    form.profileImage = null;
    isNotValidType.value = false;
};

const labelProgressClasses = computed(() => ({
    'text-orange-500': !form.progress,
    'text-orange-200': form.progress,
    'cursor-not-allowed': form.progress,
    'select-none': form.progress,
}));
</script>

<template>
    <div class="py-6 mt-10">
        <div class="mx-auto bg-white rounded-3xl shadow-md overflow-hidden p-5 lg:px-8">
            <h2 class="text-center mb-5 text-lg md:text-left md:text-xl">Student Profile</h2>
            <div class="md:flex md:justify-left">
                <div class="mx-auto md:mx-0 w-32">
                    <img :src="img" alt="Student profile image" class="h-auto max-w-full rounded-full"/>
                </div>
                <div class="grid grid-col-1 md:ml-8 content-between">
                    <div class="">
                        <p class="text-lg capitalize tracking-wide font-semibold text-neutral-800 text-center md:text-left self-center">
                            {{ page.props.auth.user.first_name }} {{ page.props.auth.user.last_name }}</p>
                        <p class="text-md leading-tight font-medium text-gray-400 text-center md:text-left my-1">
                            {{ page.props.auth.user.email }}</p>
                        <div class="w-full flex justify-center md:justify-start">
                            <PackageIndicator :package-id="page.props.auth.user.package_id"/>
                        </div>
                    </div>

                    <label :class="labelProgressClasses"
                           class="profile-image-label cursor-pointer text-center md:text-left align-bottom mt-3"
                           @click="openProfilePictureModal">
                        Upload Profile Image</label>
                    <Modal v-if="showModal" :imagePreview="imagePreview" @close="handleCloseModal"
                           @confirmed="handleConfirmation">
                        <template v-slot:modalTitle>
                            <h1 class="text-left text-lg py-5 border-b-2 border-gray-300 text-gray-800">Submit a
                                Profile
                                Picture</h1>
                        </template>
                        <template v-slot:modalContent>
                            <div class="flex justify-center flex-col ">
                                <form class="flex justify-center md:justify-start mt-5" enctype="multipart/form-data"
                                      method="POST"
                                      @submit.prevent>
                                    <label class="w-full h-full" for="profile-image-input">
                                        <span v-if="!imagePreview"
                                              class="bg-gray-200 hover:bg-gray-100 sm:mx-8 md:mx-10 lg:mx-16 transition flex flex-col text-center cursor-pointer py-40 border-dashed border-2 border-gray-400">
                                            <span class="text-2xl font-bold">
                                                <span
                                                    class="text-orange-500">Choose a file</span><span
                                                class="text-gray-700"> to upload</span>

                                            </span>
                                            <span
                                                class="text-sm text-gray-500 mt-2">Maximum file size is 8MB. JPG, JPEG and PNG only. </span>
                                        <input id="profile-image-input" :disabled="form.progress !== null"
                                               class="hidden" name="profile-image" type="file"
                                               @change="submitForm($event)"/>
                                            </span>
                                        <img v-if="imagePreview" :src="imagePreview" alt="Your profile image preview."
                                             class="max-h-96 object-fill mx-auto rounded-sm"/>

                                    </label>

                                </form>
                                <div class="flex items-center bg-orange-100 mt-7 mb-3 py-3 rounded-lg">
                                    <input
                                        v-model="agreedToUsePicture"
                                        class="w-4 h-4 text-orange-500 bg-gray-100 border-gray-300 rounded focus:ring-0 transition focus:ring-offset-0 mx-3"
                                        type="checkbox">

                                    <label
                                        class="text-left text-sm font-semibold  text-gray-600 ">I
                                        agree that Poland Study can use this image for my university
                                        applications.</label>
                                </div>
                                <div v-if="isNotValidType" class="text-red-500">Only jpeg and png formats are allowed!
                                </div>
                            </div>
                        </template>
                        <template v-slot:modalFooter>
                            <div class="flex w-full justify-between">
                                <Button :type="'danger'" class="text-gray-500"
                                        @click="handleCloseModal">
                                    Cancel
                                </Button>

                                <Button
                                    :disabled="isSubmitButtonDisabled"
                                    :type="'success'"
                                    class="me-0"
                                    @click="handleConfirmation">
                                    Upload and
                                    Save
                                </Button>
                            </div>
                        </template>
                    </Modal>
                </div>
            </div>
        </div>
    </div>
</template>

