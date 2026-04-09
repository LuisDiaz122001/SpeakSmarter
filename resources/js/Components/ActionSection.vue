<script setup>
import SectionTitle from './SectionTitle.vue';
</script>

<template>
    <div class="workspace-section-shell md:grid md:grid-cols-3 md:gap-8">
        <SectionTitle>
            <template #title>
                <slot name="title" />
            </template>
            <template #description>
                <slot name="description" />
            </template>
        </SectionTitle>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="workspace-section-panel px-5 py-6 sm:p-7 sm:rounded-[1.5rem]">
                <slot name="content" />
            </div>
        </div>
    </div>
</template>
