<script setup>

</script>

<template>
    <div class="flex flex-col items-center justify-center h-screen">
        <h1 class="text-2xl font-bold text-gray-800">Unauthorized</h1>
        <p class="mt-4 text-gray-600">You do not have permission to access this page.</p>
        <router-link :to="{name: 'unauthorized'}" class="mt-6 text-blue-500 hover:underline">Go back to dashboard</router-link>
    </div>
</template>

<style scoped>

</style>
