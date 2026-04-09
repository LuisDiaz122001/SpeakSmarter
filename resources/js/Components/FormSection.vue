<script setup>
import { computed, useSlots } from 'vue';
import SectionTitle from './SectionTitle.vue';

defineEmits(['submitted']);

const hasActions = computed(() => !! useSlots().actions);
</script>

<template>
    <div class="workspace-section-shell md:grid md:grid-cols-3 md:gap-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <SectionTitle>
            <template #title>
                <slot name="title" />
            </template>
            <template #description>
                <slot name="description" />
            </template>
        </SectionTitle>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form @submit.prevent="$emit('submitted')">
                <div
                    class="workspace-section-panel px-5 py-6 sm:p-7"
                    :class="hasActions ? 'sm:rounded-t-[1.5rem]' : 'sm:rounded-[1.5rem]'"
                >
                    <div class="grid grid-cols-6 gap-6">
                        <slot name="form" />
                    </div>
                </div>

                <div v-if="hasActions" class="workspace-section-actions flex items-center justify-end px-5 py-4 text-end sm:px-7 sm:rounded-b-[1.5rem]">
                    <slot name="actions" />
                </div>
            </form>
        </div>
    </div>
</template>
