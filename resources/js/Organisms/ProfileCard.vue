<template>
    <div class="py-12">
        <div class="mx-auto bg-white rounded-xl shadow-md overflow-hidden p-4 lg:px-8">
            <h2 class="text-center mb-2 text-lg md:text-left md:text-xl">Student Profile</h2>
            <div class="md:flex md:justify-left">
                <div class="mx-auto md:mx-0 w-36">
                    <img class="h-full w-full object-cover rounded-full" :src="img" alt="Student profile image"/>
                </div>
                <div class="grid grid-col-1 md:ml-8">
                    <p class="text-lg capitalize tracking-wide font-semibold text-center md:text-left self-center">
                        {{ firstName }} {{ lastName }}</p>
                    <p class="mt-1 text-md leading-tight font-medium text-gray-400 text-center md:text-left md:mt-0">
                        {{ email }}</p>
                    <span
                        class="mt-2 px-4 uppercase text-white text-center bg-gradient rounded-tr-lg self-center rounded-b-lg p-1 mx-auto md:mx-0 md:w-1/2 md:mt-0 md:px-0">package</span>
                    <form method="POST" enctype="multipart/form-data" class="flex justify-center mt-2"
                          :action="route('user.image.update')">
                        <label class="profile-image-label text-orange-500 md:text-left"
                               for="profile-image-input">Upload Profile Image (Required)</label>
                        <input type="file" class="hidden" name="profile-image"
                               id="profile-image-input"/>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    firstName: {
        type: String
    },
    lastName: {
        type: String
    },
    email: {
        type: String
    },
    img: {
        type: String
    }
})
</script>
