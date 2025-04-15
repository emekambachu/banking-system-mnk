<script setup>
import {ref, defineEmits, defineProps} from "vue";
import TrashIcon from "@/js/components/Icons/TrashIcon.vue";
import EditIcon from "@/js/components/Icons/EditIcon.vue";
import SlideOverModal from "@/js/components/Modals/SlideOverModal.vue";
import UserForm from "@/js/pages/user/UserForm.vue";
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
            {{ user.name + " " + user.surname }}
        </td>
        <td class="px-6 py-4">
            {{ user.email}}
        </td>
        <td class="px-6 py-4">
            {{ user.phone }}
        </td>
        <td class="px-6 py-4">
            {{ user.country }}
        </td>
        <td class="px-6 py-4">
            {{ user.gender }}
        </td>
        <td class="px-6 py-4">
            <img :src="user.selfie" class="w-10 h-10 rounded-full">
        </td>
        <td class="px-6 py-4">
            {{ user.introduction }}
        </td>
        <td class="px-6 py-4">
            <div class="flex">
                <EditIcon width="20" height="20" class="cursor-pointer" @click.prevent="showEditForm = !showEditForm"/>
                <TrashIcon width="20" height="20" class="cursor-pointer" @click.prevent="showDeleteForm = !showDeleteForm"/>
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
