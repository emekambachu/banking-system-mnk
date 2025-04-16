<script setup>
import {computed, defineProps, defineEmits, onBeforeMount} from 'vue';

const props = defineProps({
    data: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['pagination-change-page']);

const pages = computed(() => {
    const range = [];
    for (let i = 1; i <= props.data.meta.last_page; i++) {
        range.push(i);
    }
    return range;
});

const changePage = (page) => {
    if (page !== props.data.meta.current_page) {
        emit('pagination-change-page', page);
    }
};

onBeforeMount(() => {
    // console.log("PROPS DATA", props.data);
    // console.log("PAGES", pages.value);
})
</script>

<template>
    <nav aria-label="Page navigation">
        <ul class="flex justify-center space-x-2">
            <li :class="['inline-block', { 'opacity-50 pointer-events-none': !props.data.links?.prev }]">
                <a class="px-4 py-2 bg-gray-700 text-gray-200 rounded hover:bg-gray-700"
                   href="#"
                   @click.prevent="changePage(props.data.meta?.current_page - 1)">Previous</a>
            </li>
            <li v-for="page in pages" :key="page"
                :class="['inline-block', { 'bg-blue-500 text-white': page === props.data.meta?.current_page }]">
                <a class="px-4 py-2 bg-gray-700 text-gray-200 rounded hover:bg-gray-700" href="#"
                   @click.prevent="changePage(page)">{{ page }}</a>
            </li>
            <li :class="['inline-block', { 'opacity-50 pointer-events-none': !props.data.links?.next }]">
                <a class="px-4 py-2 bg-gray-700 text-gray-200 rounded hover:bg-gray-700" href="#"
                   @click.prevent="changePage(props.data.meta?.current_page + 1)">Next</a>
            </li>
        </ul>
    </nav>
</template>

<style scoped>
.page-item.disabled .page-link {
    pointer-events: none;
    cursor: default;
    opacity: 0.6;
}
.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}
</style>
