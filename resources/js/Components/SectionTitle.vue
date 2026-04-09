<template>
    <div class="md:col-span-1 flex justify-between">
        <div class="workspace-section-title px-4 sm:px-0">
            <h3>
                <slot name="title" />
            </h3>

            <p class="text-sm">
                <slot name="description" />
            </p>
        </div>

        <div class="px-4 sm:px-0">
            <slot name="aside" />
        </div>
    </div>
</template>
