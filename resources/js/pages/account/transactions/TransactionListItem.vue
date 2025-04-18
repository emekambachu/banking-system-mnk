<script setup>
import {ref, defineEmits, defineProps, onMounted} from "vue";
import SlideOverModal from "@/js/components/Modals/SlideOverModal.vue";
import apiClient from "@/js/utils/apiClient.js";
import AnimateSpinIcon from "@/js/components/Icons/AnimateSpinIcon.vue";

const loading = ref(false);
const deleted = ref(false);

const props = defineProps({
    transaction: {
        type: Object,
        required: true
    },
    index: {
        type: Number,
        required: true
    }
});

const transaction = ref(props.transaction);

onMounted(() => {

});

</script>

<template>
    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
            {{ index + 1}}
        </th>
        <td class="px-6 py-4">
            <p>
                {{ transaction.currency+' '+transaction.amount }}
            </p>
        </td>
        <td class="px-6 py-4">
            <p>
                {{ transaction.type }}
            </p>
        </td>
        <td class="px-6 py-4">
            <p>
                {{ transaction.description+' REF:'+transaction.hash }}
            </p>
        </td>
        <td class="px-6 py-4">
            <p>
                {{ transaction.created_at }}
            </p>
        </td>

    </tr>

</template>

<style scoped>

</style>
