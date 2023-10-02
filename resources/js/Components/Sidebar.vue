<template>
    <div class="h-screen  w-100 p-5 border-r border-gray-200 bg-white">
        <div class="flex items-center justify-center">
            <Link :href="route('dashboard')">
                <ApplicationLogo width="80"
                />
            </Link>
        </div>
    </div>
</template>

<script>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import {Link} from "@inertiajs/vue3";

export default {
    components: {Link, ApplicationLogo}
};
</script>
