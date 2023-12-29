<template>
    <TransitionRoot :show="open" as="template">
        <Dialog as="div" class="relative z-50" @close="handleClose">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                             leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"/>
            </TransitionChild>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300"
                                     enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                     enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                                     leave-from="opacity-100 translate-y-0 sm:scale-100"
                                     leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel
                            class="relative p-1 transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-fit sm:max-w-2xl">
                            <div class="bg-white px-5 pb-5 pt-1">
                                <div class="flex flex-col">

                                    <div class="text-center">
                                        <DialogTitle as="h3"
                                                     class="text-base w-full font-semibold leading-6 text-gray-900 border-b-2 border-gray-300 pb-3 mt-2">
                                            <slot name="modalTitle"><p> Default Title</p></slot>
                                        </DialogTitle>
                                        <div class="mt-5 p-5 px-10">
                                            <slot name="modalContent">
                                            </slot>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t-2 border-gray-300 flex justify-end mx-6 py-3">
                                <slot name="modalFooter">
                                    <Button :type="'danger'" @click="handleClose">Close</Button>
                                </slot>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import {ref} from 'vue';
import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot} from '@headlessui/vue';
import Button from '@/Atoms/Button.vue';
// import Button from '@/Atoms/Button.vue';

const emits = defineEmits(['close', 'confirmed']);

const handleClose = (() => {
    open.value = false;
    emits('close');
});

const handleConfirmation = (() => {
    open.value = false;
    emits('confirmed');
});

const open = ref(true);

</script>
