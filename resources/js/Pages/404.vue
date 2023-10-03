<template>
    <GuestLayout>
        <Head title="Not Found - Poland Study"/>
        <h1 class="text-xl text-center">Oops... Nothing found here.</h1>
        <Link :href="route('home')" class="block text-center underline text-orange-500">Go back to safety</Link>
    </GuestLayout>
</template>

<script>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
export default {
    name: "404",
    components: {
        GuestLayout,
        Head,
        Link
    },
}
</script>


<style scoped>

</style>
