<script setup>
import {computed, defineEmits, defineProps} from 'vue';
import {FaPen, IoClose, LaTrashAlt, MdSaveOutlined} from 'oh-vue-icons/icons';
import {addIcons} from 'oh-vue-icons';

// Define props
const {
    type = 'primary',
    outline = false,
    size = 'medium',
    icon,
    width = 0,
    disabled = false,
    isLoading = false,
} = defineProps([
    'type',
    'outline',
    'size',
    'icon',
    'width',
    'disabled',
    'isLoading',
]);

addIcons(FaPen, IoClose, MdSaveOutlined, LaTrashAlt);

// Define emits for custom events
const emits = defineEmits(['click']);

const btnStyleClass = computed(() => {
    switch (outline) {
        case false:
            switch
                (type) {
                case 'primary':
                    return 'justify-around px-5 rounded-r-2xl rounded-b-2xl rounded-edit bg-orange-500 text-white hover:bg-orange-700 focus:bg-orange-700 focus:ring-orange-300';
                case 'danger':
                    return 'inline-flex px-3 rounded-2xl bg-red-500 text-white hover:bg-red-700  focus:bg-red-700 focus:ring-red-300';
                case 'success':
                    return 'inline-flex px-3 rounded-2xl bg-green-500 text-white hover:bg-green-700  focus:bg-green-700 focus:ring-green-300';
                case 'muted':
                    return 'inline-flex px-3 rounded-2xl bg-gray-500 text-white hover:bg-gray-700 focus:bg-gray-700 focus:ring-gray-300';
                default :
                    return 'bg-orange-500 text-white hover:bg-orange-700 focus:bg-orange-700 focus:ring-orange-300';
            }
            break;
        case true:
            switch (type) {
                case 'primary':
                    return 'border border-orange-500 rounded-2xl px-3 text-orange-500 hover:bg-orange-500 hover:text-white focus:ring-orange-300';
                case 'danger':
                    return 'border border-red-500 rounded-2xl px-3 text-red-500  hover:bg-red-500 hover:text-white focus:ring-red-300';
                case 'success':
                    return 'border border-green-500 rounded-2xl px-3 text-green-500 hover:bg-green-500 hover:text-white focus:ring-green-300';
                case 'muted':
                    return 'border border-gray-500 rounded-2xl px-3 text-gray-500 hover:bg-gray-500 hover:text-white focus:ring-gray-300';
                default :
                    return 'border border-orange-500 rounded-2xl px-3 text-orange-500 hover:bg-orange-500 hover:text-white focus:ring-orange-300';
            }
            break;
    }

});

const btnWidthClass = computed(() => {
    switch (width) {
        case 0:
            return '';
        case 50:
            return 'w-1/2';
        case 32:
            return 'w-32';
        default :
            return 'w-full';
    }
});

const btnIconClass = computed(() => {
    switch (icon) {
        case 'save':
            return 'md-save-outlined';
        case 'close':
            return 'io-close';
        case 'edit':
            return 'fa-pen';
        case 'delete':
            return 'la-trash-alt';
        default :
            return null;
    }
});

// Handle button click
const handleClick = () => {
    if (!disabled || !isLoading) {
        emits('click');
    }
};
</script>

<template>
    <button
        :class="[btnStyleClass,btnWidthClass]"
        :disabled="disabled || isLoading"
        class="items-center py-2 dark:bg-gray-200 border border-transparent font-medium text-xs dark:text-gray-800 uppercase tracking-widest dark:hover:bg-white dark:active:bg-gray-300 focus:outline-none focus:ring-2  focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:pointer-events-none flex"
        type="button"
        @click="handleClick"
    >
        <span v-if="isLoading" class="spinner"></span>
        <span v-else class="flex justify-center">
            <slot/>
            <v-icon v-if="btnIconClass != null" :name="btnIconClass" scale="0.75"/>
        </span>
    </button>
</template>

<style scoped>
/* Add your button styling here */
</style>




