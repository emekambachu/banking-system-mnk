<script setup>
import {ref, onMounted, reactive} from 'vue';
import handleErrors from "@/js/utils/handleErrors.js";
import apiClient from "@/js/utils/apiClient.js";
import formValidations from "@/js/utils/formValidations.js";
import AnimateSpinIcon from "@/js/components/Icons/AnimateSpinIcon.vue";
import EyeCloseIcon from "@/js/components/Icons/EyeCloseIcon.vue";
import EyeOpenIcon from "@/js/components/Icons/EyeOpenIcon.vue";

const loading = ref(false);
const submitted = ref(false);
const showPassword = ref(false);
let errors = ref({});

let form = reactive({
    first_name: '',
    last_name: '',
    email: '',
    mobile: '',
    date_of_birth: '',
    address: '',
    password: '',
    password_confirmation: '',
});

const register = async () => {
    errors.value = {};
    submitted.value = false;
    loading.value = true;

    try {
        let response = await apiClient.post(`/register`, form);
        if(response.data.success){
            submitted.value = true;
        }

    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        }
        if (error.response) {
            errors.value['server_error'] = ['An error occurred, please try again'];
            handleErrors.hideErrorInProduction("ERROR_RESPONSE", error.response)
        }
    }
    loading.value = false;
};

const togglePassword = () => {
    showPassword.value = !showPassword.value;
}

const validatePhone = (event) => {
    if(form.mobile === ""){
        return false
    }
    let valid = formValidations.validateMobileNumber(event.target.value);
    if (!valid) {
        errors.value['mobile'] = ["Wrong format, international mobile number required"];
        return false;
    }else{
        errors.value['mobile'] = [];
        return true;
    }
}

const validatePassword = (event, password, password_confirm) => {
    let identical = formValidations.passwordConfirmation(password, password_confirm);
    if(!identical){
        errors.value['password'] = ['Password and confirmation do not match'];
        return false;
    }else{
        errors.value['password'] = [];
        return true;
    }
}

const maxDate = ref('');
const showDateForEighteenYearsOld = () => {
    const today = new Date();
    const seventeenYearsAgo = new Date(today.getFullYear() - 17, today.getMonth(), today.getDate());
    maxDate.value = seventeenYearsAgo.toISOString().split('T')[0];
};

const clearDateIfLessThanEighteenYearsOld = () => {
    const today = new Date();
    const seventeenYearsAgo = new Date(today.getFullYear() - 17, today.getMonth(), today.getDate());
    const dateOfBirth = new Date(form.date_of_birth);
    if (dateOfBirth > seventeenYearsAgo) {
        form.date_of_birth = '';
    }
};

onMounted(() => {
    showDateForEighteenYearsOld();
});

</script>

<template>
    <div class="grid grid-cols-1 gap-4">
        <div class="w-full lg:w-1/4 mx-auto bg-white drop-shadow-lg rounded-lg p-6 my-6">
            <form @submit.prevent="register">
                <div class="space-y-8">

                    <div class="border-b border-gray-900/10 pb-6">
                        <h2 class="text-base/7 font-semibold text-gray-900">Signup</h2>
                        <p class="mt-1 text-sm/6 text-gray-600">
                            Create your account
                        </p>
                        <small v-if="errors.server_error" class="text-rose-500">
                            {{ errors.server_error[0] }}
                        </small>
                    </div>

                    <div class="border-b border-gray-900/10 pb-12">

                        <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                            <div class="sm:col-span-3">
                                <label for="first-name" class="block text-sm/6 font-medium text-gray-900">First name</label>
                                <div class="mt-2">
                                    <input
                                        type="text" name="first-name" id="first-name" autocomplete="first-name"
                                        v-model="form.first_name"
                                        required
                                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                                    >
                                    <small v-if="errors.first_name" class="text-rose-500">
                                        {{ errors.first_name[0] }}
                                    </small>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="last-name" class="block text-sm/6 font-medium text-gray-900">Last name</label>
                                <div class="mt-2">
                                    <input
                                        type="text"
                                        id="last-name"
                                        autocomplete="family-name"
                                        v-model="form.last_name"
                                        required
                                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <small v-if="errors.last_name" class="text-rose-500">
                                        {{ errors.last_name[0] }}
                                    </small>
                                </div>
                            </div>

                            <div class="sm:col-span-full">
                                <label for="email" class="block text-sm/6 font-medium text-gray-900">Email address</label>
                                <div class="mt-2">
                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        autocomplete="email"
                                        v-model="form.email"
                                        required
                                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                                    >
                                    <small v-if="errors.email" class="text-rose-500">
                                        {{ errors.email[0] }}
                                    </small>
                                </div>
                            </div>

                            <div class="sm:col-span-full">
                                <label for="mobile" class="block text-sm/6 font-medium text-gray-900">Mobile</label>
                                <div class="mt-2">
                                    <input
                                        id="mobile"
                                        type="text"
                                        autocomplete="mobile"
                                        v-model="form.mobile"
                                        @change="validatePhone"
                                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <small v-if="errors.mobile" class="text-rose-500">
                                        {{ errors.mobile[0] }}
                                    </small>
                                </div>
                            </div>

                            <div class="col-span-full">
                                <label for="address" class="block text-sm/6 font-medium text-gray-900">
                                    Address
                                </label>
                                <div class="mt-2">
                                    <input
                                        type="text" name="address" id="address" autocomplete="address"
                                        v-model="form.address"
                                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                                    >
                                </div>
                            </div>

                            <div class="sm:col-span-full">
                                <label for="date_of_birth" class="block text-sm/6 font-medium text-gray-900">
                                    Date of Birth
                                </label>
                                <div class="mt-2">
                                    <input
                                        id="date_of_birth"
                                        type="date"
                                        :max="maxDate"
                                        @change="clearDateIfLessThanEighteenYearsOld"
                                        v-model="form.date_of_birth"
                                        required
                                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                    <small v-if="errors.date_of_birth" class="text-rose-500">
                                        {{ errors.first_name[0] }}
                                    </small>
                                </div>
                            </div>

                            <div class="sm:col-span-full">
                                <label for="password" class="block text-sm/6 font-medium text-gray-900">Password</label>
                                <div class="mt-2 flex">
                                    <input
                                        id="password"
                                        :type="showPassword ? 'text' : 'password'"
                                        v-model="form.password"
                                        @change="validatePassword($event, form.password, form.password_confirmation)"
                                        required
                                        class="w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                                    >
                                    <span>
                                        <EyeCloseIcon
                                            v-if="!showPassword"
                                            @click.prevent="togglePassword"
                                            :width="'30'"
                                            class="text-gray-500"
                                        />
                                        <EyeOpenIcon
                                            v-else
                                            @click.prevent="togglePassword"
                                            :width="'30'"
                                            class="text-gray-500"
                                        />
                                    </span>
                                </div>
                                <small v-if="errors.password" class="text-rose-500">
                                    {{ errors.password[0] }}
                                </small>
                            </div>

                            <div class="sm:col-span-full">
                                <label for="password_confirm" class="block text-sm/6 font-medium text-gray-900">
                                    Password Confirm
                                </label>
                                <div class="mt-2 flex">
                                    <input
                                        id="password_confirm"
                                        :type="showPassword ? 'text' : 'password'"
                                        v-model="form.password_confirmation"
                                        @change="validatePassword($event, form.password, form.password_confirmation)"
                                        required
                                        class="w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                                    >
                                    <span>
                                        <EyeCloseIcon
                                            v-if="!showPassword"
                                            @click.prevent="togglePassword"
                                            :width="'30'"
                                            class="text-gray-500"
                                        />
                                        <EyeOpenIcon
                                            v-else
                                            @click.prevent="togglePassword"
                                            :width="'30'"
                                            class="text-gray-500"
                                        />
                                    </span>
                                </div>
                            </div>

                            <!--Alerts-->
                            <div class="sm:col-span-full">
                                <p v-if="errors.server_error" class="text-rose-500">
                                    {{ errors.server_error[0] }}
                                </p>
                                <div v-if="submitted" class="bg-emerald-300 p-2 text-center">
                                    <p>Registration completed, you will be able to login upon approval</p>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="mt-6 flex items-center justify-end gap-x-6">
                    <button type="button" class="text-sm/6 font-semibold text-gray-900">Cancel</button>
                    <button v-if="!loading" type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Register</button>
                    <button v-else type="button" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        <AnimateSpinIcon class="animate-spin h-5 w-5 text-gray-200" />
                    </button>
                </div>
            </form>
        </div>

    </div>
</template>

<style scoped>

</style>
