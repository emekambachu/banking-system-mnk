<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    title: {
        type: String,
        required: true
    }
});

const emit = defineEmits(['close', 'submit']);

const isOpen = ref(props.show);

watch(() => props.show, (newVal) => {
    isOpen.value = newVal;
});

const closeModal = () => {
    emit('close-modal');
};
</script>

<template>
    <transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 overflow-hidden z-50">
            <div class="absolute inset-0 overflow-hidden">
                <transition name="fade">
                    <div
                        class="absolute inset-0 bg-black opacity-30"
                        @click="closeModal"
                    ></div>
                </transition>
                <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                    <transition name="slide">
                        <div class="w-screen max-w-md">
                            <div class="h-full flex flex-col bg-gray-800 shadow-xl overflow-y-scroll">

                                <div class="px-4 py-6 bg-gray-800 sm:px-6">
                                    <div class="flex items-start justify-between">

                                        <h2 class="text-lg font-medium text-white">{{ title }}</h2>

                                        <div class="ml-3 h-7 flex items-center">
                                            <button @click="closeModal" class="bg-gray-800 rounded-md text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                                <span class="sr-only">Close panel</span>
                                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative flex-1 px-4 sm:px-6">
                                    <slot></slot>
                                </div>

                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
.fade-enter-to,
.fade-leave-from {
    opacity: 1;
}

.slide-enter-active,
.slide-leave-active {
    transition: transform 0.5s ease;
}
.slide-enter-from,
.slide-leave-to {
    transform: translateX(100%);
}
.slide-enter-to,
.slide-leave-from {
    transform: translateX(0%);
}
</style>
