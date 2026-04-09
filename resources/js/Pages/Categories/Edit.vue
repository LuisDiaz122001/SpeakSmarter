<script>
export default {
    name: "CategoryEdit",
}
</script>

<script setup>
import CategoryForm from '@/Components/Categories/Form.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.category.name,
});
</script>

<template>
    <AppLayout title="Edit Category">
        <template #header>
            <div class="flex flex-col gap-2">
                <p class="workspace-kicker">Categories</p>
                <h1 class="workspace-display text-3xl leading-tight text-slate-900">Edit category details with a cleaner structure</h1>
            </div>
        </template>

        <div class="workspace-shell py-8 sm:py-10">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
                <section
                    class="workspace-hero rounded-[2rem] px-6 py-8 text-white sm:px-8"
                    style="background: linear-gradient(135deg, rgba(20, 33, 61, 0.98), rgba(13, 148, 136, 0.92));"
                >
                    <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-3xl">
                            <p class="text-xs uppercase tracking-[0.35em] text-white/70">Category Update</p>
                            <h2 class="workspace-display mt-3 text-4xl leading-tight sm:text-5xl">
                                Keep <span class="text-white/80">{{ props.category.name }}</span> aligned with the way the library is evolving.
                            </h2>
                            <p class="mt-3 text-sm leading-7 text-white/78 sm:text-base">
                                Small naming refinements can make the content map easier to understand for editors and clients.
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

                <CategoryForm
                    :updating="true"
                    :form="form"
                    @submit="form.put(route('categories.update', props.category.id))"
                />
            </div>
        </div>
    </AppLayout>
</template>
