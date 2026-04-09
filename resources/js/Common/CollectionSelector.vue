<script setup>

import { ref } from 'vue';

const props = defineProps({
    collection: {
        type: Array,
        required: true
    },
});

const currentSelection = ref('');
const selection = ref([]);

const handleAddToSelection = () => {
    const selectedItem = props.collection.find(
        item => item.id === currentSelection.value
    );

    if (
        selectedItem &&
        !selection.value.some(item => item.id === selectedItem.id)
    ) {
        selection.value.push(selectedItem);
    }
};

const handleRemoveSelection = (id) => {
    selection.value = selection.value.filter(item => item.id !== id);
};

</script>

<template>
    <div class="w-full">
        <div class="flex">
            <div class="w-full space-y-3">
                <select v-model="currentSelection" class="block w-full border-gray-300 rounded-md shadow-sm">
                    <option disabled value="">Select an option</option>
                    <option v-for="(item, index) in collection" :key="index" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>
                <div class="flex items-start gap-3">
                    <button type="button" @click="handleAddToSelection"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 whitespace-nowrap">
                        Add
                    </button>
                    <ul class="flex-1 space-y-1">
                        <li v-for="item in selection" :key="item.id"
                            class="flex items-center justify-between px-3 py-2 bg-gray-100 rounded-md">
                            {{ item.name }}
                            <span @click="handleRemoveSelection(item.id)"
                                class="ml-3 text-red-500 hover:text-red-700 cursor-pointer">
                                X
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>