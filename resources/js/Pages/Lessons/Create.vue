<script>
export default {
    name: "LessonsCreate",
}
</script>

<script setup>
import LessonForm from '@/Components/Lessons/Form.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

defineProps({
    categories: {
        type: Array,
        required: true,
    },
    levels: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    name: '',
    description: '',
    image_uri: '',
    content_uri: '',
    pdf_uri: '',
    is_free: false,
    level_id: '',
    category_ids: [],
});
</script>

<template>
    <AppLayout title="Create Lesson">
        <template #header>
            <div class="flex flex-col gap-2">
                <p class="workspace-kicker">Lessons</p>
                <h1 class="workspace-display text-3xl leading-tight text-slate-900">Create a lesson with stronger didactic context</h1>
            </div>
        </template>

        <div class="workspace-shell py-8 sm:py-10">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
                <section
                    class="workspace-hero rounded-[2rem] px-6 py-8 text-white sm:px-8"
                    style="background: linear-gradient(135deg, rgba(20, 33, 61, 0.98), rgba(194, 65, 12, 0.92));"
                >
                    <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-3xl">
                            <p class="text-xs uppercase tracking-[0.35em] text-white/70">Lesson Creation</p>
                            <h2 class="workspace-display mt-3 text-4xl leading-tight sm:text-5xl">
                                Build a lesson that is easy to place, review and reuse.
                            </h2>
                            <p class="mt-3 text-sm leading-7 text-white/78 sm:text-base">
                                Capture the goal, assign the right level and connect the lesson to categories so the library stays useful over time.
                            </p>
                        </div>

                        <Link
                            :href="route('lessons.index')"
                            class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20"
                        >
                            Back to lessons
                        </Link>
                    </div>
                </section>

                <LessonForm :form="form" :categories="categories" :levels="levels" @submit="form.post(route('lessons.store'))" />
            </div>
        </div>
    </AppLayout>
</template>
