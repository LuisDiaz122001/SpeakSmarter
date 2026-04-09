<script setup>
import { computed, ref } from 'vue';

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    collection: {
        type: Array,
        required: true,
    },
    modelValue: {
        type: Array,
        default: () => [],
    },
});

const currentSelection = ref('');

const normalizedSelection = computed(() => props.modelValue.map((value) => String(value)));

const availableCollection = computed(() =>
    props.collection.filter((item) => !normalizedSelection.value.includes(String(item.id))),
);

const selection = computed(() =>
    props.collection.filter((item) => normalizedSelection.value.includes(String(item.id))),
);

const handleAddToSelection = () => {
    if (!currentSelection.value || normalizedSelection.value.includes(String(currentSelection.value))) {
        return;
    }

    emit('update:modelValue', [...props.modelValue, currentSelection.value]);
    currentSelection.value = '';
};

const handleRemoveSelection = (id) => {
    emit(
        'update:modelValue',
        props.modelValue.filter((itemId) => String(itemId) !== String(id)),
    );
};
</script>

<template>
    <div class="workspace-side-note rounded-[1.25rem] p-4 sm:p-5">
        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="workspace-kicker">Category Selection</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Attach the lesson to the teaching areas it belongs to.
                    </p>
                </div>

                <span class="workspace-chip rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">
                    {{ selection.length }} selected
                </span>
            </div>

            <div class="grid gap-3 sm:grid-cols-[1fr_auto]">
                <select
                    v-model="currentSelection"
                    class="workspace-select block w-full rounded-2xl px-4 py-3 text-sm shadow-sm"
                >
                    <option disabled value="">Select a category</option>
                    <option v-for="item in availableCollection" :key="item.id" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>

                <button
                    type="button"
                    @click="handleAddToSelection"
                    class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="!currentSelection"
                >
                    Add category
                </button>
            </div>

            <div v-if="selection.length" class="flex flex-wrap gap-2">
                <button
                    v-for="item in selection"
                    :key="item.id"
                    type="button"
                    @click="handleRemoveSelection(item.id)"
                    class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-2 text-sm font-medium text-amber-800 transition hover:bg-amber-100"
                >
                    <span>{{ item.name }}</span>
                    <span class="text-xs uppercase tracking-[0.2em] text-amber-600">Remove</span>
                </button>
            </div>

            <div
                v-else
                class="rounded-2xl border border-dashed border-slate-300 bg-white/80 px-4 py-5 text-sm text-slate-500"
            >
                No categories selected yet. Add at least one to make the lesson easier to discover.
            </div>
        </div>
    </div>
</template>
