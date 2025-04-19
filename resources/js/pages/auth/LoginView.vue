<script setup>
import {ref, onMounted, reactive, computed} from 'vue';
import handleErrors from "@/js/utils/handleErrors.js";
import AnimateSpinIcon from "@/js/components/Icons/AnimateSpinIcon.vue";
import apiClient from "@/js/utils/apiClient.js";

const loading = ref({
    login: false,
    two_factor_auth: false,
});
const submitted = ref({
    login: false,
    two_factor_auth: false,
    two_factor_auth_resend: false,
});
const twoFactorAuth = ref(false);
let errors = ref({});

let forms = reactive({
    login: {
        email: '',
        password: '',
    },
    two_factor_auth: {
        secret: '',
        email: computed(() => forms.login.email),
    }
});

const login = async () => {
    errors.value = {};
    submitted.value.login = false;
    loading.value.login = true;

    try {
        await axios.get(`/sanctum/csrf-cookie`);
        const response = await apiClient.post('/login', forms.login);

        if (response.data.success) {
            if(response.data.two_factor_auth){
                twoFactorAuth.value = response.data.two_factor_auth;
                return;
            }
            submitted.value.login = true;
            errors.value = {};
            window.location.href = '/account/dashboard';
        }
        console.log(response.data);

    } catch (error) {
        if (error.response?.data?.errors){
            errors.value = error.response.data.errors;
        }
        if (error.response?.data?.status === 500 || error.response?.data?.errors?.server_error) {
            errors.value['server_error'] = ['An error occurred, please try again'];
            handleErrors.hideErrorInProduction("ERROR_RESPONSE", error.response);
        }
    }

    loading.value.login = false;
};

const submitTwoFactorAuth = async () => {
    errors.value = {};
    submitted.value.two_factor_auth = false;
    loading.value.two_factor_auth = true;

    try {
        const response = await apiClient.post('/login/2fa-verify', forms.two_factor_auth);
        if (response.data.success) {
            submitted.value.two_factor_auth = true;
            errors.value = {};
            window.location.href = '/account/dashboard';
        }
        console.log(response.data);

    } catch (error) {
        if (error.response?.data?.errors){
            errors.value = error.response.data.errors;
        }
        if (error.response?.data?.status === 500 || error.response?.data?.errors?.server_error) {
            errors.value['server_error'] = ['An error occurred, please try again'];
            handleErrors.hideErrorInProduction("ERROR_RESPONSE", error.response);
        }
    }

    loading.value.two_factor_auth = false;
};

const resendTwoFactorAuth = async () => {
    errors.value = {};
    submitted.value.two_factor_auth_resend = false;
    loading.value.two_factor_auth = true;

    try {
        const response = await apiClient.post('/login/2fa-send-code', forms.two_factor_auth);
        if (response.data.success) {
            submitted.value.two_factor_auth_resend = true;
            errors.value = {};
            console.log("Code resent successfully");
        }

        if (response.data.errors){
            errors.value = response.data.errors;
        }

        console.log(response.data);

    } catch (error) {
        if (error.response?.data?.errors){
            errors.value = error.response.data.errors;
        }
        if (error.response?.data?.status === 500 || error.response?.data?.errors?.server_error) {
            errors.value['server_error'] = ['An error occurred, please try again'];
            handleErrors.hideErrorInProduction("ERROR_RESPONSE", error.response);
        }
    }

    loading.value.two_factor_auth = false;
};

onMounted(() => {

});

</script>

<template>
    <div class="grid grid-cols-1 gap-4">
        <div class="w-full lg:w-1/4 mx-auto bg-white drop-shadow-lg rounded-lg p-6 my-6">

<!--            <p v-if="errors.access" class="text-rose-500 text-sm/6">-->
<!--                {{ errors.access[0] }}-->
<!--            </p>-->
<!--            <p v-if="errors.server_error" class="text-rose-500 text-sm/6">-->
<!--                {{ errors.server_error[0] }}-->
<!--            </p>-->

            <p
                v-for="(error, index) in errors"
                :key="index"
                class="text-rose-500 text-sm/6"
            >
                {{ error[0] }}
            </p>

            <form v-if="!twoFactorAuth" @submit.prevent="login">

                <div class="space-y-8">
                    <div class="border-b border-gray-900/10 pb-6">
                        <h2 class="text-base/7 font-semibold text-gray-900">Login</h2>
                        <p class="mt-1 text-sm/6 text-gray-600">
                            Login to your account
                        </p>
                    </div>

                    <div class="border-b border-gray-900/10 pb-12">
                        <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                            <div class="sm:col-span-full">
                                <label for="email" class="block text-sm/6 font-medium text-gray-900">Email address</label>
                                <div class="mt-2">
                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        autocomplete="email"
                                        v-model="forms.login.email"
                                        required
                                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                                    >
                                    <small v-if="errors.email" class="text-rose-500">
                                        {{ errors.email[0] }}
                                    </small>
                                </div>
                            </div>

                            <div class="sm:col-span-full">
                                <label for="password" class="block text-sm/6 font-medium text-gray-900">Password</label>
                                <div class="mt-2">
                                    <input id="password" type="password"
                                           v-model="forms.login.password"
                                           required
                                           class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <small v-if="errors.password" class="text-rose-500">
                                        {{ errors.password[0] }}
                                    </small>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-x-6">
                    <button type="button" class="text-sm/6 font-semibold text-gray-900">Cancel</button>
                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        <span v-if="!loading.login">Login</span>
                        <AnimateSpinIcon v-else class="animate-spin h-5 w-5 text-gray-200" />
                    </button>
                </div>

            </form>

            <div v-if="twoFactorAuth" class="mt-6">
                <h2 class="text-base/7 font-semibold text-gray-900">Two Factor Authentication</h2>
                <p class="mt-1 text-sm/6 text-gray-600 text-center">
                    Please enter the code sent to your email
                </p>
                <p
                    v-if="submitted.two_factor_auth_resend"
                    class="mt-1 text-sm/6 text-gray-600 bg-emerald-300 p-2 text-center"
                >
                    Code resent successfully
                </p>
                <form @submit.prevent="submitTwoFactorAuth">
                    <input
                        type="text"
                        v-model="forms.two_factor_auth.secret"
                        required
                        placeholder="Enter code" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                    >

                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <button
                            @click.prevent="resendTwoFactorAuth"
                            type="submit"
                            class="rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-gray-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600"
                        >
                            Resend Code
                        </button>
                        <button
                            type="submit"
                            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                        >
                            <span v-if="!loading.two_factor_auth">Submit</span>
                            <AnimateSpinIcon v-else class="animate-spin h-5 w-5 text-gray-200" />
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>
</template>

<style scoped>

</style>
