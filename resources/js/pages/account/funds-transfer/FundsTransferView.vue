<script setup>
import {ref, onMounted, watch, inject} from 'vue';
import TransactionListItem from "@/js/pages/account/transactions/TransactionListItem.vue";
import apiClient from "@/js/utils/apiClient.js";
import handleErrors from "@/js/utils/handleErrors.js";
import NumberPagination from "@/js/components/paginations/NumberPagination.vue";
import {useRoute} from "vue-router";

// Get user data from parent component layout
const authUser = inject('authUser'); // Access the provided `user` data
const loading = ref(false);

const sendFunds = async () => {
    loading.value = true;

    try {
        let response;
        if(type === 'my-transactions') {
            response = await apiClient.get('/users/my-transactions?page=' + page);
        }

        if(type === 'user-transactions') {
            response = await apiClient.get('/users/' + id.value + '/transactions?page=' + page);
        }

        if(response.data.success){
            console.log("Get Users", response.data.transactions);
            transactions.value = response.data.transactions.data;
            pagination.value.links = response.data.transactions.links;
            pagination.value.meta = response.data.transactions.meta;
            total.value = response.data.total;

            console.log(response.data.transactions);
        }

    } catch (error) {
        if (error.response) {
            handleErrors.hideErrorInProduction("ERROR_RESPONSE", error.response)
        }
    }
    loading.value = false;
};

onMounted(() => {
    getTransactions();
});

</script>

<template>
    <div>
        <h1 class="text-3xl mb-2">Transactions</h1>
    </div>

    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">

            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    S/N
                </th>
                <th scope="col" class="px-6 py-3">
                    Amount
                </th>
                <th scope="col" class="px-6 py-3">
                    Type
                </th>
                <th scope="col" class="px-6 py-3">
                    Description
                </th>
                <th scope="col" class="px-6 py-3">
                    Date
                </th>
            </tr>
            </thead>

            <tbody>
            <TransactionListItem
                v-for="(transaction, index) in transactions"
                :key="transaction.id"
                :index="index"
                :transaction="transaction"
            />
            </tbody>
        </table>

        <NumberPagination
            v-if="total > 0"
            class="mt-5"
            :data="pagination"
            @pagination-change-page="getTransactions"
        />

    </div>

</template>

<style scoped>

</style>
