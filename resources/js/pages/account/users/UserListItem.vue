<script setup>
import {ref, defineEmits, defineProps, onMounted} from "vue";
import TrashIcon from "@/js/components/Icons/TrashIcon.vue";
import EditIcon from "@/js/components/Icons/EditIcon.vue";
import SlideOverModal from "@/js/components/Modals/SlideOverModal.vue";
import UserForm from "@/js/pages/account/users/UserForm.vue";
import apiClient from "@/js/utils/apiClient.js";
import AnimateSpinIcon from "@/js/components/Icons/AnimateSpinIcon.vue";

const showEditForm = ref(false);
const showDeleteForm = ref(false);
const loading = ref(false);
const deleted = ref(false);

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
const emit = defineEmits(['delete-user']);

const updateUser = (event) => {
  user.value = event;
}

const deleteUser = async () => {
  loading.value = true;
  await apiClient.delete(`/users/${user.value.id}`).then((response) => {
    if (response.data.success) {
      deleted.value = true;
      emit('delete-user', user.value.id);
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
                Balance: {{ new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(user.amount) }}
            </p>
            <p>
                Account Type: {{ user.account_type }}
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
                    @click.prevent="showEditForm = !showEditForm"
                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 mr-2 rounded">
                    Edit
                </button>
                <button
                    @click.prevent="showDeleteForm = !showDeleteForm"
                    class="bg-rose-500 hover:bg-rose-700 text-white font-bold py-2 px-4 rounded">
                    Delete
                </button>
            </div>
        </td>
    </tr>

    <SlideOverModal
        title="Update User"
        :show="showEditForm"
        @close-modal="showEditForm = false"
    >
        <UserForm
            :user="user"
            @update-user="updateUser"
        />
    </SlideOverModal>

    <SlideOverModal
        title="Delete User"
        :show="showDeleteForm"
        @close-modal="showDeleteForm = false"
    >
        <h3 class="text-center">Are you sure you want to delete {{ user.name + " " + user.surname }}</h3>
        <p class="text-center">This action cannot be undone.</p>
        <div v-if="!loading && !deleted" class="flex justify-center mt-4">
            <button @click.prevent="showDeleteForm = false" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
            <button @click.prevent="deleteUser(user.id)" class="bg-red-500 text-white px-4 py-2 rounded ml-2">Delete</button>
        </div>
        <div v-else-if="loading && !deleted" class="flex justify-center mt-4">
          <AnimateSpinIcon class="animate-spin h-5 w-5 text-gray-200" />
        </div>
        <div v-else-if="deleted" class="flex justify-center mt-4">
            <p class="text-rose-400">User deleted successfully.</p>
        </div>
    </SlideOverModal>
</template>

<style scoped>

</style>
