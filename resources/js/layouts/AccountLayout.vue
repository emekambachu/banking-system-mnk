<script setup>
import {onBeforeMount, ref} from "vue";
import apiClient from "@/js/utils/apiClient.js";
import handleErrors from "@/js/utils/handleErrors.js";
import axios from "axios";
import router from "@/js/router/index.js";

const showMobileMenu = ref(false);
const showProfileDropDown = ref(false);
const user = ref(null);

const toggleMobileMenu = () => {
    showMobileMenu.value = !showMobileMenu.value;
}

const toggleProfileDropDown = () => {
    showProfileDropDown.value = !showProfileDropDown.value;
}

const authenticateUser = async () => {
    try {
        await axios.get('/sanctum/csrf-cookie');
        const response = await apiClient.get('/authenticate');
        if(response.data.success){
            user.value = response.data.user;
        } else {
            await router.push('/login');
        }
    } catch (error) {
        handleErrors.hideErrorInProduction('Auth Error', error.response);
        await router.push('/login');
    }
};

const logout = async () => {
    try {
        const response = await axios.get('/logout');
        if(response.data.success){
            window.location.href = '/login';
        }
    } catch (error) {
        handleErrors.hideErrorInProduction('Auth Error', error.response);
    }
};



onBeforeMount(() => {
    authenticateUser();
});
</script>

<template>
    <nav class="bg-gray-800">
        <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
            <div class="relative flex h-16 items-center justify-between">
                <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                    <!-- Mobile menu button-->
                    <button @click.prevent="toggleMobileMenu" type="button" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:ring-2 focus:ring-white focus:outline-hidden focus:ring-inset" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="absolute -inset-0.5"></span>
                        <span class="sr-only">Open main menu</span>
                        <!--
                          Icon when menu is closed.

                          Menu open: "hidden", Menu closed: "block"
                        -->
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <!--
                          Icon when menu is open.

                          Menu open: "block", Menu closed: "hidden"
                        -->
                        <svg class="hidden size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                    <div class="flex shrink-0 items-center">
                        <img class="h-8 w-auto" src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company">
                    </div>
                    <div class="hidden sm:ml-6 sm:block">
                        <div class="flex space-x-4">
                            <RouterLink
                                :to="{ name:'dashboard' }"
                                class="rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white" aria-current="page">
                                Dashboard
                            </RouterLink>

                            <RouterLink
                                v-if="user?.roles?.includes('admin')"
                                :to="{ name:'users' }"
                                class="rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white" aria-current="page">
                                Users
                            </RouterLink>

                            <RouterLink
                                :to="{ name:'my-transactions' }"
                                class="rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white" aria-current="page">
                                Transactions
                            </RouterLink>

                            <RouterLink
                                :to="{ name:'funds-transfer' }"
                                class="rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white" aria-current="page">
                                Funds Transfer
                            </RouterLink>
                        </div>
                    </div>
                </div>

                <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                    <button type="button" class="relative rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">View notifications</span>
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </button>

                    <!-- Profile dropdown -->
                    <div class="relative ml-3">
                        <div>
                            <button
                                @click.prevent="toggleProfileDropDown"
                                type="button" class="relative flex rounded-full bg-gray-800 text-sm focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="absolute -inset-1.5"></span>
                                <span class="sr-only">Open user menu</span>
                                <img class="size-8 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                            </button>
                        </div>

                        <div v-if="showProfileDropDown" class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                            <!-- Active: "bg-gray-100 outline-hidden", Not Active: "" -->
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 font-extrabold border-b-black" role="menuitem" tabindex="-1" id="user-menu-item-0">{{ user.first_name+' '+user.last_name }}</a>
                            <hr/>

                            <a
                                @click.prevent="logout"
                                href="#"
                                class="block px-4 py-2 text-sm text-gray-700"
                                role="menuitem"
                                tabindex="-1"
                                id="user-menu-item-2">
                                Sign out
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state. -->
        <div v-if="showMobileMenu" class="sm:hidden" id="mobile-menu">
            <div class="space-y-1 px-2 pt-2 pb-3">
                <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->

                <RouterLink
                    :to="{ name:'dashboard' }"
                    class="block rounded-md bg-gray-900 px-3 py-2 text-base font-medium text-white" aria-current="page">
                    Dashboard
                </RouterLink>

                <RouterLink
                    :to="{ name:'users' }"
                    class="block rounded-md bg-gray-900 px-3 py-2 text-base font-medium text-white" aria-current="page">
                    Users
                </RouterLink>

                <RouterLink
                    :to="{ name:'my-transactions' }"
                    class="block rounded-md bg-gray-900 px-3 py-2 text-base font-medium text-white" aria-current="page">
                    Transactions
                </RouterLink>

                <RouterLink
                    :to="{ name:'funds-transfer' }"
                    class="block rounded-md bg-gray-900 px-3 py-2 text-base font-medium text-white" aria-current="page">
                    Funds Transfer
                </RouterLink>

            </div>
        </div>

    </nav>

    <!--Load component only if user auth hsd been downloaded-->
    <div v-if="user">
        <router-view v-slot="{ Component }">
            <component :is="Component" :auth_user="user" />
        </router-view>
    </div>

</template>

<style scoped>

</style>
