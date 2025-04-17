<script setup>
import {ref, onMounted, reactive} from 'vue';
import UserListItem from "@/js/pages/account/users/UserListItem.vue";
import apiClient from "@/js/utils/apiClient.js";
import handleErrors from "@/js/utils/handleErrors.js";
import SlideOverModal from "@/js/components/Modals/SlideOverModal.vue";
import UserForm from "@/js/pages/account/users/UserForm.vue";
import NumberPagination from "@/js/components/paginations/NumberPagination.vue";

const users = ref([]);
const total = ref(0);
const pagination = ref({
    links: {},
    meta: {}
});
const loading = ref(false);
const showForm = ref(false);

const form = reactive({
    search_value: '',
    balance_greater_than: '',
    balance_less_than: '',
    date_joined_from: '',
    date_joined_before: '',
});

const searchValues = ref([]);

const getUsers = async (page = 1, type = 'get') => {
    loading.value = true;
    users.value = [];

    try {
        let response;
        if(type === 'get') {
            response = await apiClient.get('/users?page=' + page);
        } else {
            if(form.search_value === '' && form.balance_greater_than === '' && form.balance_less_than === '' && form.date_joined_from === '' && form.date_joined_before === '') {
                return false;
            }
            response = await apiClient.post('/users/search?page=' + page, form);
        }

        if(response.data.success){
            console.log("Get Users", response.data.users);
            users.value = response.data.users.data;
            pagination.value.links = response.data.users.links;
            pagination.value.meta = response.data.users.meta;
            total.value = response.data.total;
            console.log(response.data.users);

            if(type === 'search') {
                searchValues.value = response.data.search_values;
            } else {
                searchValues.value = [];
            }
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

onMounted(() => {
    getUsers();
});

</script>

<template>
    <div>
        <h1 class="text-3xl mb-2">User List</h1>
        <button @click.prevent="showForm = true" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Add User</button>
    </div>

    <div class="relative bg-gray-600 p-3 text-white">
        <div v-if="searchValues.length" class="flex items-center justify-center text-lg">
            <span class="font-bold mr-1">{{ total }} results for</span>
            <p v-for="(result, index) in searchValues" :key="index">
                {{ result+', ' }}
            </p>
        </div>
        <form @submit.prevent="getUsers(1, 'search')" class="w-full">
            <div class="grid grid-cols-5 gap-2">
                <div class="col-span-1">
                    <label>
                        Search Value
                    </label>
                    <input
                        id="search_value"
                        type="text"
                        v-model="form.search_value"
                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                    >
                </div>
                <div class="col-span-1">
                    <label>
                        Balance Greater Than
                    </label>
                    <input
                        id="search_value"
                        type="number"
                        v-model="form.balance_greater_than"
                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                    >
                </div>
                <div class="col-span-1">
                    <label>
                        Balance Less Than
                    </label>
                    <input
                        id="search_value"
                        type="number"
                        v-model="form.balance_less_than"
                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                    >
                </div>
                <div class="col-span-1">
                    <label>
                        Date Joined From
                    </label>
                    <input
                        id="search_value"
                        type="date"
                        v-model="form.date_joined_from"
                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                    >
                </div>
                <div class="col-span-1">
                    <label>
                        Date Joined Before
                    </label>
                    <input
                        id="search_value"
                        type="date"
                        v-model="form.date_joined_before"
                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                    >
                </div>
                <div class="col-span-5 mx-auto">
                    <button
                        type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mr-2 rounded">Submit</button>
                </div>
            </div>
        </form>
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
                <UserListItem
                    v-for="(user, index) in users"
                    :key="user.id"
                    :index="index"
                    :user="user"
                    @delete-user="deleteUser"
                />
            </tbody>
        </table>

        <NumberPagination
            v-if="total > 0"
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
