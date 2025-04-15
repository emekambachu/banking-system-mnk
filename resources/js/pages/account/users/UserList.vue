<script setup>
import {ref, onBeforeMount} from 'vue';
import UserItem from "@/js/pages/user/UserItem.vue";
import apiClient from "@/js/utils/apiClient.js";
import handleErrors from "@/js/utils/handleErrors.js";
import SlideOverModal from "@/js/components/Modals/SlideOverModal.vue";
import UserForm from "@/js/pages/user/UserForm.vue";

const users = ref([]);
const loading = ref(false);
const showForm = ref(false);

const getUsers = async () => {
    loading.value = true;
    try {
        const response = await apiClient.get('/users');
        if(response.data.success){
            users.value = response.data.users;
            console.log(response.data.users);
        }

    } catch (error) {
        if (error.response) {
            handleErrors.hideErrorInProduction("ERROR_RESPONSE", error.response)
        }
    }
    loading.value = false;
};

const updateUserList = (event) => {
    users.value.unshift(event);
}

const deleteUser = (event) => {
    users.value = users.value.filter(user => user.id !== event);
}

onBeforeMount(() => {
    getUsers();
});

</script>

<template>
    <div>
        <h1 class="text-3xl mb-2">User List</h1>
        <button @click.prevent="showForm = true" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Add User</button>
    </div>

    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">

            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    S/N
                </th>
                <th scope="col" class="px-6 py-3">
                    Names
                </th>
                <th scope="col" class="px-6 py-3">
                    Email
                </th>
                <th scope="col" class="px-6 py-3">
                    Phone
                </th>
                <th scope="col" class="px-6 py-3">
                    Country
                </th>
                <th scope="col" class="px-6 py-3">
                    Gender
                </th>
                <th scope="col" class="px-6 py-3">
                    Selfie
                </th>
                <th scope="col" class="px-6 py-3">
                    Introduction
                </th>
                <th scope="col" class="px-6 py-3">
                    Action
                </th>
            </tr>
            </thead>

            <tbody>
                <UserItem
                    v-for="(user, index) in users"
                    :key="user.id"
                    :index="index"
                    :user="user"
                    @delete-user="deleteUser"
                />
            </tbody>
        </table>
    </div>

    <SlideOverModal
        title="Create User"
        :show="showForm"
        @close-modal="showForm = false">
        <UserForm
            @create-user="updateUserList"
        />
    </SlideOverModal>

</template>

<style scoped>

</style>
