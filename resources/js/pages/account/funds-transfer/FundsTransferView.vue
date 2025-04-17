<script setup>
import {ref, onMounted, watch, inject, reactive} from 'vue';
import TransactionListItem from "@/js/pages/account/transactions/TransactionListItem.vue";
import apiClient from "@/js/utils/apiClient.js";
import handleErrors from "@/js/utils/handleErrors.js";
import NumberPagination from "@/js/components/paginations/NumberPagination.vue";
import AnimateSpinIcon from "@/js/components/Icons/AnimateSpinIcon.vue";

// Get user data from parent component layout
const authUser = inject('authUser');
const loading = ref({
    fund_transfer: false,
    beneficiary: false,
});
const submitted = ref(false);
const currencies = ref([]);
const beneficiary = ref("");

const forms = reactive({
    account_number: null,
    fund_transfer: {
        receiver_id: null,
        amount: 0.00,
        currency: '',
        description: '',
    }
});

const sendFunds = async () => {
    loading.value.fund_transfer = true;
    try {
        let response = await apiClient.post('/users/send-funds');

        if(response.data.success){
            submitted.value = true;
            console.log("Funds Transfer Sent");
        }

    } catch (error) {
        if (error.response) {
            handleErrors.hideErrorInProduction("ERROR_RESPONSE", error.response)
        }
    }
    loading.value.fund_transfer = false;
};

const getCurrencies = async () => {
    try {
        let response = await apiClient.get('/currencies');

        if(response.data.success){
            currencies.value = response.data.currencies;
            console.log("Currencies", currencies.value);
        }

    } catch (error) {
        if (error.response) {
            handleErrors.hideErrorInProduction("ERROR_RESPONSE", error.response)
        }
    }
}

const getBeneficiary = async () => {
    loading.value.beneficiary = true;
    try {
        let response = await apiClient.post('/users/send-funds/get-beneficiary', forms.account_number);
        if(response.data.success){
            beneficiary.value = response.data.beneficiary;
            forms.fund_transfer.receiver_id = response.data.beneficiary.id;
            console.log("Beneficiary", response.data);
        }

    } catch (error) {
        if (error.response) {
            handleErrors.hideErrorInProduction("ERROR_RESPONSE", error.response)
        }
    }
    loading.value.beneficiary = false;
}

onMounted(() => {
    getCurrencies();
});

</script>

<template>
    <div>
        <h1 class="text-3xl mb-2">Funds Transfer</h1>
    </div>

    <div class="relative overflow-x-auto">
        <form @submit.prevent="sendFunds" class="max-w-sm mx-auto">

            <div class="mb-5 grid grid-cols-2 gap-2">

                <div class="mb-5 col-span-full">
                    <label for="account_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Beneficiary Account Number
                    </label>
                    <input
                        v-model="forms.account_number"
                        @change="getBeneficiary"
                        type="text"
                        id="account_number"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required
                    />
                </div>

                <div class="mb-5 col-span-full">
                    <label for="currencies" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Currencies
                    </label>
                    <small>Rate Last Updated: {{ currencies.rates }}</small>
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" id="currencies" v-model="forms.fund_transfer.currency">
                        <option v-for="(currency, key, index) in currencies.rates" :key="index" :value="key">
                            {{ key }} : {{ currency }}
                        </option>
                    </select>
                </div>

                <div class="mb-5">
                    <label for="amount" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                    <input
                        v-model="forms.fund_transfer.amount"
                        type="text"
                        id="amount"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required
                    />
                </div>

                <div class="mb-5">
                    <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
                    <input
                        v-model="forms.fund_transfer.description"
                        type="text"
                        id="address"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    />
                </div>
            </div>

            <div v-if="Object.keys(errors).length && !errors.server_error" class="col-span-full text-center">
                <p class="text-red-400 text-md font-medium text-gray-900">
                    One or more fields are invalid
                </p>
            </div>

            <p v-if="submitted" class="font-bold bg-emerald-500 text-amber-50 p-1 rounded-b-md text-center mb-2">
                Funds Transfer Sent
            </p>

            <button
                v-if="!loading"
                type="submit"
                class="text-blue-500 text-sm border border-blue-500 dark:text-white rounded-lg px-2 py-1 bg-blue-500 w-1/2">
                Submit
            </button>

            <AnimateSpinIcon v-else class="mx-auto" />

        </form>
    </div>

</template>

<style scoped>

</style>
