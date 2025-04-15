<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    width: {
        type: String,
        default: '36'
    },
    height: {
        type: String,
        default: '36'
    }
})
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" :width="width" :height="height" stroke-width="2"> <path d="M4 7h16"></path> <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path> <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path> <path d="M10 12l4 4m0 -4l-4 4"></path> </svg>
</template>

<style scoped>

</style>
