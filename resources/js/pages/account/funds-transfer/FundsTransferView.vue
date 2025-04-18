<script setup>
import {ref, onMounted, watch, inject, reactive} from 'vue';
import apiClient from "@/js/utils/apiClient.js";
import handleErrors from "@/js/utils/handleErrors.js";
import AnimateSpinIcon from "@/js/components/Icons/AnimateSpinIcon.vue";
import formValidations from "@/js/utils/formValidations.js";

// Get user data from parent component layout
const loading = ref({
    fund_transfer: false,
    beneficiary: false,
});
const submitted = ref(false);
const currencies = ref([]);
const beneficiary = ref('');
const previewConvertedAmount = ref('');
const errors = ref({});

const forms = reactive({
    account: {
        account_number: ''
    },
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
        if (error.response){
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
        console.log("FORMS Account NUMBER", forms.account);
        let response = await apiClient.post('/users/send-funds/get-beneficiary', forms.account);
        if(response.data.success){
            beneficiary.value = response.data.beneficiary;
            forms.fund_transfer.receiver_id = response.data.beneficiary.id;
            console.log("Beneficiary", response.data);
        }else{
            errors.value = response.data.errors;
        }
        console.log("Beneficial data", response.data);

    } catch (error) {
        console.log("Beneficial errors", error);
        if(error.response.data.errors){
            errors.value = error.response.data.errors;
        }
        if (error.response) {
            handleErrors.hideErrorInProduction("ERROR_RESPONSE", error.response)
        }
    }
    loading.value.beneficiary = false;
}

const previewConversion = () => {

    if(!forms.fund_transfer.amount){
        previewConvertedAmount.value = '';
    }

    if(forms.fund_transfer.amount && forms.fund_transfer.currency) {
        let amount = forms.fund_transfer.amount;
        let currency = forms.fund_transfer.currency.value;

        // validate amount to prevent NaN, undefined, null, -0, etc
        let valid = formValidations.validateAmount(forms.fund_transfer.amount);
        if(!valid){
            errors.value = {
                amount: ['Invalid amount']
            }
            forms.fund_transfer.amount = '';
            previewConvertedAmount.value = ''
            return;
        }else{
            errors.value = {};
        }

        previewConvertedAmount.value = (amount * currency).toFixed(2);
        console.log("Preview Converted Amount", previewConvertedAmount.value);
    }
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

                <div v-if="Object.keys(errors).length" class="col-span-full text-center">
                    <p v-for="(error, index) in errors" :key="index" class="text-red-400 text-sm font-medium text-gray-900">
                        {{ error[0] }}
                    </p>
                </div>

                <div class="mb-5 col-span-full">

                    <div class="text-md">
                        <p v-if="beneficiary">
                            <span class="font-bold">Account Name: </span> {{ beneficiary.first_name + " " + beneficiary.last_name }}
                        </p>
                        <p v-if="previewConvertedAmount">
                            <span class="font-bold">Beneficiary will receive:</span>
                            {{ previewConvertedAmount }}<br>
                        </p>
                    </div>

                    <label for="account_number" class="block mb-2 text-sm font-medium text-gray-900">
                        Beneficiary Account Number
                    </label>
                    <input
                        v-model="forms.account.account_number"
                        @change="getBeneficiary"
                        type="text"
                        id="account_number"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required
                    />
                </div>

                <div class="col-span-full">
                    <small>
                        <small>Rate Last Updated: {{ currencies.date }}</small>
                    </small>
                </div>

                <div class="mb-5 col-span-1">
                    <label for="currencies" class="block mb-2 text-sm font-medium text-gray-900">
                        Currencies
                    </label>
                    <select
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        id="currencies"
                        v-model="forms.fund_transfer.currency"
                        @change="previewConversion"
                    >
                        <option
                            v-for="(currency, key, index) in currencies.rates"
                            :key="index"
                            :value="{
                                key: key,
                                value: currency
                            }">
                            {{ key }} : {{ currency }}
                        </option>
                    </select>
                </div>

                <div class="mb-5 cols-span-1">
                    <label for="amount" class="block mb-2 text-sm font-medium text-gray-900">Amount</label>
                    <input
                        v-model="forms.fund_transfer.amount"
                        @change="previewConversion"
                        type="text"
                        id="amount"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required
                    />
                </div>

                <div class="mb-5 col-span-full">
                    <label for="description" class="block mb-2 text-sm font-medium text-gray-900">Description</label>
                    <textarea
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        id="description"
                        v-model="forms.fund_transfer.description">
                        Optional description for the transaction
                    </textarea>
                </div>
            </div>

            <p v-if="submitted" class="font-bold bg-emerald-500 text-amber-50 p-1 rounded-b-md text-center mb-2">
                Funds Transfer Sent
            </p>

            <button
                v-if="!loading.fund_transfer"
                type="submit"
                class="text-white text-sm border border-blue-500 rounded-lg px-2 py-1 bg-blue-500 w-1/2">
                Send funds
            </button>

            <AnimateSpinIcon v-else class="mx-auto" />

        </form>
    </div>

</template>

<style scoped>

</style>
