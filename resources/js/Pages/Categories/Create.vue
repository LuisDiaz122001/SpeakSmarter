<script>
export default {
    name: "CategoriesCreate",
}
</script>

<script setup>
import CategoryForm from '@/Components/Categories/Form.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
});
</script>

<template>
    <AppLayout title="Create Category">
        <template #header>
            <div class="flex flex-col gap-2">
                <p class="workspace-kicker">Categories</p>
                <h1 class="workspace-display text-3xl leading-tight text-slate-900">Create a category with a clean teaching purpose</h1>
            </div>
        </template>

        <div class="workspace-shell py-8 sm:py-10">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
                <section
                    class="workspace-hero rounded-[1.5rem] px-4 py-6 text-white sm:rounded-[2rem] sm:px-8 sm:py-8"
                    style="background: linear-gradient(135deg, rgba(20, 33, 61, 0.98), rgba(15, 118, 110, 0.92));"
                >
                    <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-3xl">
                            <p class="text-xs uppercase tracking-[0.35em] text-white/70">Category Creation</p>
                            <h2 class="workspace-display mt-3 text-3xl leading-tight sm:text-4xl lg:text-5xl">
                                Add a new branch to the content map without making it harder to navigate.
                            </h2>
                            <p class="mt-3 text-sm leading-7 text-white/78 sm:text-base">
                                A strong category keeps lessons grouped in a way that feels intuitive for curriculum planning and content review.
                            </p>
                        </div>

                        <Link
                            :href="route('categories.index')"
                            class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20"
                        >
                            Back to categories
                        </Link>
                    </div>
                </section>

                <CategoryForm :form="form" @submit="form.post(route('categories.store'))" />
            </div>
        </div>
    </AppLayout>
</template>
