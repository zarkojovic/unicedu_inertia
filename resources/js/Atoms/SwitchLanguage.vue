<script setup>
import {computed, reactive} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import {FiSquareGb, FiSquareTr} from "oh-vue-icons/icons";
import {addIcons} from "oh-vue-icons";

const langClass = computed(() => {
        if (page.props.lang.current === 'en') {
            return 'fi-square-tr';
        } else {
            return 'fi-square-gb';
        }
    }
);
const langCode = computed(() => {
        if (page.props.lang.current === 'en') {
            return 'tr';
        } else {
            return 'en';
        }
    }
);

const data = reactive({
    lang: langCode,
});

const page = usePage();

const switchLanguage = () => {

    router.post('change-lang', data);
};

addIcons(FiSquareGb, FiSquareTr);

</script>
<template>
    <button
        class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500 me-3"
        @click="switchLanguage">Switch to
        <v-icon :name="langClass"/>

    </button>
</template>

<style scoped>

</style>
