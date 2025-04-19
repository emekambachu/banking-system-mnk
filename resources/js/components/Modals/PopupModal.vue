<script setup>
import { defineProps, defineEmits } from "vue";

const props = defineProps({
    title: String,
    show: Boolean,
});

const emit = defineEmits(["close-modal"]);
</script>

<template>
    <!-- Backdrop fade -->
    <transition
        enter-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            @click.self="emit('close-modal')"
            class="fixed inset-0 z-50 flex items-center justify-center"
            style="background-color: rgba(128, 128, 128, 0.5);"
        >
            <!-- Modal panel scale+fade -->
            <transition
                enter-active-class="transition transform ease-out duration-300"
                enter-from-class="opacity-0 scale-90"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition transform ease-in duration-200"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-90"
            >
                <div
                    v-if="show"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md mx-auto p-6"
                >
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ title }}
                        </h2>
                        <button
                            @click="emit('close-modal')"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        >
                            &times;
                        </button>
                    </div>
                    <div><slot /></div>
                </div>
            </transition>
        </div>
    </transition>
</template>
