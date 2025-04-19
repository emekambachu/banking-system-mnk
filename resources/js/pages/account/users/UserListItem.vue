<script setup>
import {ref, defineProps} from "vue";
import apiClient from "@/js/utils/apiClient.js";
import PopupModal from "@/js/components/Modals/PopupModal.vue";
import AnimateSpinIcon from "@/js/components/Icons/AnimateSpinIcon.vue";

const showStatusModal = ref(false);
const loading = ref(false);

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  index: {
    type: Number,
    required: true
  },
    auth_user: {
        type: Object,
        required: true
    }
});

const user = ref(props.user);

const updateUserStatus = async () => {
  loading.value = true;
  await apiClient.put(`/users/${user.value.id}/update-status`).then((response) => {
    if (response.data.success) {
        user.value = response.data.user;
        showStatusModal.value = false;
      console.log("Updated", response.data);
    }
  }).catch((error) => {
    console.log(error);
  });
  loading.value = false;
}

</script>

<template>
    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
            {{ index + 1}}
        </th>
        <td class="px-6 py-4">
            <p>
                Name: {{ user.first_name + " " + user.last_name }}
            </p>
            <p>
                Email: {{ user.email }}
            </p>
            <p>
                Mobile: {{ user.mobile }}
            </p>
            <p>
                Address: {{ user.address }}
            </p>
        </td>
        <td class="px-6 py-4">
            <p>
                Account Number: {{ user.account_number }}
            </p>
            <p>
                Balance: {{ user.currency }} {{ new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(user.amount) }}
            </p>
            <p>
                Account Type: {{ user.account_type }}
            </p>
        </td>
        <td>
            <p>
                Status:
                <span v-if="user.status === 1" class="text-emerald-700">Approved</span>
                <span v-else class="text-rose-600">Blocked</span>
            </p>
            <p>
                Date Joined: {{ user.created_at }}
            </p>
        </td>
        <td class="px-6 py-4">
            <div class="flex">
                <router-link
                    v-if="auth_user.roles.includes('admin')"
                    :to="{ name: 'transactions', params: { id: user.id } }">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mr-2 rounded">
                        Transactions
                    </button>
                </router-link>
                <button
                    v-if="auth_user.roles.includes('admin')"
                    @click.prevent="showStatusModal = !showStatusModal"
                    class="text-white font-bold py-2 px-4 mr-2 rounded"
                    :class="[user.status === 1 ? 'bg-rose-700 hover:bg-rose-800' : 'bg-emerald-600 hover:bg-emerald-800']"
                >
                    <span v-if="user.status === 1">
                        Block User
                    </span>
                    <span v-else>
                        Approve User
                    </span>
                </button>
            </div>
        </td>
    </tr>

    <PopupModal
        title="Update status"
        :show="showStatusModal"
        :user="user"
        @close-modal="showStatusModal = false"
    >

        <div class="flex flex-col">
            <p class="text-gray-500 mb-2 text-lg">
                <span
                    v-if="user.status === 1">Block </span> <span v-else>Approve </span>
                <strong>{{ user.first_name + " " + user.last_name }}</strong>?
            </p>
            <div class="flex justify-end">
                <button
                    @click.prevent="updateUserStatus"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mr-2 rounded"
                >
                    <AnimateSpinIcon v-if="loading" class="animate-spin" />
                    <span v-else>
                        <span v-if="user.status === 1">Block</span>
                        <span v-else>Approve</span>
                    </span>
                </button>
                <button
                    @click.prevent="showStatusModal = false"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                >
                    Cancel
                </button>
            </div>
        </div>

    </PopupModal>

</template>

<style scoped>

</style>
