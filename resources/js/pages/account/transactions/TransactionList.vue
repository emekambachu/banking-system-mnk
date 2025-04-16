<script setup>
import {ref, onBeforeMount} from 'vue';
import TransactionListItem from "@/js/pages/account/transactions/TransactionListItem.vue";
import apiClient from "@/js/utils/apiClient.js";
import handleErrors from "@/js/utils/handleErrors.js";
import SlideOverModal from "@/js/components/Modals/SlideOverModal.vue";
import NumberPagination from "@/js/components/paginations/NumberPagination.vue";

const users = ref([]);
const total = ref(0);
const pagination = ref({
    links: {},
    meta: {}
});
const loading = ref(false);
const showForm = ref(false);

const getUsers = async (page = 1) => {
    loading.value = true;
    try {
        const response = await apiClient.get('/users?page=' + page);
        if(response.data.success){
            console.log("Get Users", response.data.users);
            users.value = response.data.users.data;
            pagination.value.links = response.data.users.links;
            pagination.value.meta = response.data.users.meta;
            total.value = response.data.total;
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
                    Bio
                </th>
                <th scope="col" class="px-6 py-3">
                    Account Details
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

        <NumberPagination
            class="mt-5"
            :data="pagination"
            @pagination-change-page="getUsers"
        />

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
