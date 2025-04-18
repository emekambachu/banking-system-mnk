<script setup>
import {ref, onMounted, watch, inject, toRef, onBeforeMount, computed} from 'vue';
import TransactionListItem from "@/js/pages/account/transactions/TransactionListItem.vue";
import apiClient from "@/js/utils/apiClient.js";
import handleErrors from "@/js/utils/handleErrors.js";
import NumberPagination from "@/js/components/paginations/NumberPagination.vue";
import {useRoute} from "vue-router";

// For routing with params, especially when changing
const id = ref('');
const route = useRoute();
id.value = route.params.id;
watch(
    () => route.params.id,
    () => {
        id.value = route.params.id;
    },
);

// Get user from parent component, AccountLayout.vue
const props = defineProps({
    auth_user: { type: Object, required: true }
});
const authUser = ref(props.auth_user);
const myTransactions = computed(() => {
    return window.location.href === '/account/my-transactions';
});

const transactions = ref([]);
const total = ref(0);
const pagination = ref({
    links: {},
    meta: {}
});
const loading = ref(false);

const getTransactions = async (page = 1) => {
    loading.value = true;
    transactions.value = [];

    try {
        let response;
        if(myTransactions.value) {
            response = await apiClient.get('/users/my-transactions?page=' + page);
        } else {
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

onBeforeMount(() => {
    console.log("beforeMount", authUser.value);
    getTransactions();
});

</script>

<template>
    <div>
        <h3 class="text-2xl mb-2">Transactions ({{ total }})</h3>
    </div>

    <div class="relative">
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
                v-if="total > 0"
                v-for="(transaction, index) in transactions"
                :key="transaction.id"
                :index="index"
                :transaction="transaction"
            />
            <tr v-else>
                <td colspan="5" class="text-center py-4">
                    <p class="text-gray-500">No transactions found</p>
                </td>
            </tr>
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
