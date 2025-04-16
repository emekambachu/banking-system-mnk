<script setup>
import {ref, onMounted, reactive} from 'vue';
import handleErrors from "@/js/utils/handleErrors.js";
import AnimateSpinIcon from "@/js/components/Icons/AnimateSpinIcon.vue";
import apiClient from "@/js/utils/apiClient.js";

const loading = ref(false);
const submitted = ref(false);
let errors = ref({});

let form = reactive({
    email: '',
    password: '',
});

const login = async () => {
    errors.value = {};
    submitted.value = false;
    loading.value = true;

    try {
        await axios.get('/sanctum/csrf-cookie');
        const response = await apiClient.post('/login', form);
        if (response.data.success) {
            submitted.value = true;
            errors.value = {};
            window.location.href = '/dashboard';
        }
        console.log(response.data);

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

onMounted(() => {

});

</script>

<template>
    <div class="grid grid-cols-1 gap-4">
        <div class="w-full lg:w-1/4 mx-auto bg-white drop-shadow-lg rounded-lg p-6 my-6">
            <form>
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
                                <label for="password" class="block text-sm/6 font-medium text-gray-900">Password</label>
                                <div class="mt-2">
                                    <input id="password" type="password"
                                           v-model="form.password"
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
                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Login</button>
                </div>
            </form>
        </div>

    </div>
</template>

<style scoped>

</style>
