<script setup>
import {computed, defineProps, defineEmits} from 'vue';
import {FaRegularEdit, IoClose, MdSaveOutlined, LaTrashAlt} from "oh-vue-icons/icons";
import {addIcons} from "oh-vue-icons";

// Define props
const {
    type = 'primary',
    outline = false,
    size = 'medium',
    icon,
    width = 0,
    disabled = false,
    isLoading = false
} = defineProps([
    'type',
    'outline',
    'size',
    'icon',
    'width',
    'disabled',
    'isLoading',
]);

addIcons(FaRegularEdit, IoClose, MdSaveOutlined, LaTrashAlt);

// Define emits for custom events
const emits = defineEmits(['click']);

const btnStyleClass = computed(() => {
    switch (outline) {
        case false:
            switch
                (type) {
                case "primary":
                    return 'bg-orange-500 text-white hover:bg-orange-700 focus:bg-orange-700 focus:ring-orange-300'
                case "danger":
                    return 'bg-red-500 text-white hover:bg-red-700  focus:bg-red-700 focus:ring-red-300'
                case 'success':
                    return 'bg-green-500 text-white hover:bg-green-700 focus:bg-green-700 focus:ring-green-300'
                default :
                    return 'bg-orange-500 text-white hover:bg-orange-700 focus:bg-orange-700 focus:ring-orange-300'
            }
            break;
        case true:
            switch (type) {
                case "primary":
                    return 'border border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white focus:ring-orange-300'
                case "danger":
                    return 'border border-red-500  text-red-500  hover:bg-red-500 hover:text-white focus:ring-red-300'
                case 'success':
                    return 'border border-green-500  text-green-500 hover:bg-green-500 hover:text-white focus:ring-green-300'
                default :
                    return 'border border-orange-500  text-orange-500 hover:bg-orange-500 hover:text-white focus:ring-orange-300'
            }
            break;
    }

});

const btnWidthClass = computed(() => {
    switch (width) {
        case 0:
            return '';
        case 50:
            return 'w-1/2'
        case 32:
            return 'w-32'
        default :
            return 'w-full'
    }
})

const btnIconClass = computed(() => {
    switch (icon) {
        case 'save':
            return 'md-save-outlined'
        case 'close':
            return 'io-close'
        case 'edit':
            return 'fa-regular-edit'
        case 'delete':
            return 'la-trash-alt'
        default :
            return null;
    }
});

// Handle button click
const handleClick = () => {
    if (!disabled && !isLoading) {
        emits('click');
    }
};
</script>

<template>
    <button
        class="inline-flex items-center px-4 py-2 dark:bg-gray-200 border border-transparent rounded-lg font-semibold text-xs dark:text-gray-800 uppercase tracking-widest dark:hover:bg-white  active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2  focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:pointer-events-none flex items-center justify-center "
        :class="[btnStyleClass,btnWidthClass]"
        :disabled="disabled || isLoading"
        type="button"
        @click="handleClick"
    >
        <span v-if="isLoading" class="spinner"></span>
        <span v-else>
            <slot/>
            <v-icon v-if="btnIconClass != null" :name="btnIconClass"/>
        </span>
    </button>
</template>

<style scoped>
/* Add your button styling here */
</style>




