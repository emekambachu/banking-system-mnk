<script setup>
import { ref, reactive, onBeforeMount } from 'vue';
import handleErrors from "@/js/utils/handleErrors.js";
import apiClient from "@/js/utils/apiClient.js";
import formValidations from "@/js/utils/formValidations.js";
import AnimateSpinIcon from "@/js/components/Icons/AnimateSpinIcon.vue";

const loading = ref(false);
const submitted = ref(false);
let errors = ref({});

const emit = defineEmits(['create-user']);

let formGroups = reactive([ // Initialize with one form group
    {
        first_name: '',
        last_name: '',
        email: '',
        mobile: '',
        address: '',
        date_of_birth: '',
        password: '',
        password_confirmation: '',
    }
]);

const submitForm = async () => {
    errors.value = {};
    submitted.value = false;
    loading.value = true;

    try {
        let response = await apiClient.post('/users/create', formGroups);
        console.log("Response", response);

        if(response.data.success) {

            console.log(response.data.users);

            submitted.value = true;
            errors.value = {};

            formGroups = [{
                first_name: '',
                last_name: '',
                email: '',
                mobile: '',
                address: '',
                date_of_birth: '',
                password: '',
                password_confirmation: '',
            }];
            emit('create-user', response.data.users);
        }

    } catch (error) {
        console.log("Error", error);
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        }
        if (error.response.server_error) {
            errors.value['server_error'] = ['An error occurred, please try again'];
            handleErrors.hideErrorInProduction("ERROR_RESPONSE", error.response)
        }
    }
    loading.value = false;
};

const addFormGroup = () => {
    formGroups.push({
        first_name: '',
        last_name: '',
        email: '',
        mobile: '',
        address: '',
        date_of_birth: '',
        password: '',
        password_confirmation: '',
    });
};

const removeFormGroup = (index) => {
    if (formGroups.value.length > 1) {
        formGroups.value.splice(index, 1);
    }
};

onBeforeMount(() => {});

</script>

<template>
    <div>
        <div v-if="Object.keys(errors).length" class="card text-center">
            <p v-for="(error, index) in errors" :key="index" class="text-red-400">
                {{ error[0] }}
            </p>
        </div>

        <form @submit.prevent="submitForm" class="max-w-sm mx-auto">
            <div v-for="(formGroup, groupIndex) in formGroups" :key="groupIndex" class="mb-5 grid grid-cols-2 gap-2">
                <div class="mb-5">
                    <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        First Name
                    </label>
                    <input
                        v-model="formGroup.first_name"
                        type="text"
                        id="first_name"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required
                    />
                </div>

                <div class="mb-5">
                    <label for="last_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Last Name</label>
                    <input
                        v-model="formGroup.last_name"
                        type="text"
                        id="last_name"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required
                    />
                </div>

                <div class="mb-5">
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                    <input
                        v-model="formGroup.email"
                        type="email"
                        id="email"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required
                    />
                </div>

                <div class="mb-5">
                    <label for="mobile" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mobile</label>
                    <input
                        v-model="formGroup.mobile"
                        type="text"
                        id="mobile"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    />
                </div>

                <div class="mb-5">
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your password</label>
                    <input
                        v-model="formGroup.password"
                        type="password"
                        id="password"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required
                    />
                </div>

                <div class="mb-5">
                    <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Your password confirmation
                    </label>
                    <input
                        v-model="formGroup.password_confirmation"
                        type="password"
                        id="password_confirmation"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required
                    />
                </div>

                <div class="mb-5">
                    <label for="date_of_birth" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date of Birth</label>
                    <input
                        v-model="formGroup.date_of_birth"
                        type="date"
                        id="date_of_birth"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required
                    />
                </div>

                <div class="mb-5">
                    <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address</label>
                    <input
                        v-model="formGroup.address"
                        type="text"
                        id="address"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    />
                </div>

                <button
                    v-if="groupIndex !== 0"
                    @click.prevent="removeFormGroup(groupIndex)"
                    type="button"
                    class="text-red-500 text-sm border border-red-500 dark:text-white rounded-lg px-2 py-1">
                    Remove this form
                </button>

                <div class="col-span-full">
                    <hr class="text-white"/>
                </div>
            </div>

            <div v-if="Object.keys(errors).length && !errors.server_error" class="col-span-full text-center">
                <p class="text-red-400 text-md font-medium text-gray-900">
                    One or more fields are invalid
                </p>
            </div>

            <div v-if="errors.user" class="col-span-full text-center">
                <p class="text-red-400 text-md font-medium text-gray-900">
                    {{ errors.user[0] }}
                </p>
            </div>

            <p v-if="submitted" class="font-bold bg-emerald-500 text-amber-50 p-1 rounded-b-md text-center mb-2">
                Created successfully
            </p>

            <button
                v-if="!loading"
                type="submit"
                class="text-blue-500 text-sm border border-blue-500 dark:text-white rounded-lg px-2 py-1 bg-blue-500 w-1/2">
                Submit
            </button>

            <AnimateSpinIcon v-else class="mx-auto" />

            <button
                @click.prevent="addFormGroup"
                type="button"
                class="text-emerald-500 text-sm border border-emerald-500 dark:text-white rounded-lg px-2 py-1 w-1/2">
                Add new form group
            </button>

        </form>
    </div>
</template>

<style scoped>
</style>
