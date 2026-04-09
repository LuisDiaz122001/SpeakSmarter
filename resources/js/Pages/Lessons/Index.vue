<script>
export default {
    name: "LessonsIndex",
}
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    lessons: {
        type: Object,
        required: true,
    },
    overview: {
        type: Object,
        required: true,
    },
});

const page = usePage();

const permissions = computed(() => page.props.user?.permissions ?? []);
const roles = computed(() => page.props.user?.roles ?? []);

const canCreate = computed(() => permissions.value.includes('create lessons'));
const canEdit = computed(() => permissions.value.includes('edit lessons'));
const canDelete = computed(() => permissions.value.includes('delete lessons'));

const accessLabel = computed(() => {
    if (roles.value.includes('admin')) return 'Admin';
    if (roles.value.includes('editor')) return 'Editor';
    if (roles.value.includes('client')) return 'Client';

    return 'User';
});

const accessMessage = computed(() => {
    if (canCreate.value) {
        return 'You can create, refine and curate lesson content from this space.';
    }

    return 'You can review the lesson library, but management actions stay restricted.';
});

const priceFormatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
});

const displayPrice = (lesson) => {
    if (lesson.is_free) {
        return 'Free';
    }

    if (lesson.price === null) {
        return 'On request';
    }

    return priceFormatter.format(Number(lesson.price));
};

const statCards = computed(() => [
    {
        title: 'Total Lessons',
        value: props.overview.total,
        note: 'All lesson records currently available.',
    },
    {
        title: 'Free Lessons',
        value: props.overview.free,
        note: 'Open content that can be shared more easily.',
    },
    {
        title: 'With Price',
        value: props.overview.priced,
        note: 'Paid lessons already prepared for the public catalog.',
    },
    {
        title: 'With Level',
        value: props.overview.with_level,
        note: 'Lessons already aligned to a progression stage.',
    },
    {
        title: 'Categorized',
        value: props.overview.categorized,
        note: 'Lessons already connected to the content map.',
    },
]);

const deleteLesson = (id) => {
    if (confirm('Are you sure you want to delete this lesson?')) {
        router.delete(route('lessons.destroy', id));
    }
};
</script>

<template>
    <AppLayout title="Lessons">
        <template #header>
            <div class="flex flex-col gap-2">
                <p class="workspace-kicker">Lesson Library</p>
                <h1 class="workspace-display text-3xl leading-tight text-slate-900">Lessons that feel clearer and easier to maintain</h1>
            </div>
        </template>

        <div class="workspace-shell py-8 sm:py-10">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
                <section
                    class="workspace-hero rounded-[1.5rem] px-4 py-6 text-white sm:rounded-[2rem] sm:px-8 sm:py-8"
                    style="background: linear-gradient(135deg, rgba(20, 33, 61, 0.98), rgba(194, 65, 12, 0.92));"
                >
                    <div class="relative z-10 grid gap-8 lg:grid-cols-[1.7fr_0.9fr]">
                        <div class="space-y-4">
                            <p class="text-xs uppercase tracking-[0.35em] text-white/70">Lessons</p>
                            <h2 class="workspace-display max-w-2xl text-3xl leading-tight sm:text-4xl lg:text-5xl">
                                Keep each lesson readable, reusable and ready for class.
                            </h2>
                            <p class="max-w-2xl text-sm leading-7 text-white/78 sm:text-base">
                                This view highlights how much of the library is organized, leveled and ready to support a more didactic teaching flow.
                            </p>

                            <div class="flex flex-wrap gap-3 text-sm text-white/82">
                                <span class="rounded-full border border-white/20 bg-white/10 px-4 py-2">{{ lessons.total }} total</span>
                                <span class="rounded-full border border-white/20 bg-white/10 px-4 py-2">{{ accessLabel }} access</span>
                            </div>
                        </div>

                        <div class="workspace-panel rounded-[1.5rem] p-5 text-slate-900">
                            <p class="workspace-kicker">Access State</p>
                            <h3 class="workspace-display mt-3 text-2xl text-slate-900">{{ canCreate ? 'Build the library' : 'Review the library' }}</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ accessMessage }}</p>

                            <div class="mt-6 space-y-3 text-sm text-slate-600">
                                <div class="flex items-center justify-between">
                                    <span>Edit permission</span>
                                    <span class="font-semibold text-slate-900">{{ canEdit ? 'Enabled' : 'Hidden' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Delete permission</span>
                                    <span class="font-semibold text-slate-900">{{ canDelete ? 'Enabled' : 'Hidden' }}</span>
                                </div>
                            </div>

                            <Link
                                v-if="canCreate"
                                :href="route('lessons.create')"
                                class="mt-6 inline-flex rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                            >
                                Create lesson
                            </Link>
                        </div>
                    </div>
                </section>

                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                    <article
                        v-for="card in statCards"
                        :key="card.title"
                        class="workspace-stat-card rounded-[1.5rem] p-5"
                    >
                        <p class="workspace-kicker">{{ card.title }}</p>
                        <p class="workspace-display mt-4 text-3xl text-slate-900 sm:text-4xl">{{ card.value }}</p>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ card.note }}</p>
                    </article>
                </section>

                <section class="workspace-panel rounded-[1.75rem] p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="workspace-kicker">Lesson Overview</p>
                            <h3 class="workspace-display mt-2 text-2xl text-slate-900">Latest lesson cards</h3>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <span
                                class="rounded-full px-4 py-2 text-sm font-medium"
                                :class="canCreate ? 'bg-teal-50 text-teal-700' : 'bg-amber-50 text-amber-700'"
                            >
                                {{ canCreate ? 'Management mode' : 'Read-only mode' }}
                            </span>
                            <Link
                                v-if="canCreate"
                                :href="route('lessons.create')"
                                class="inline-flex rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                            >
                                New lesson
                            </Link>
                        </div>
                    </div>

                    <div v-if="lessons.data.length" class="mt-6 grid gap-4 xl:grid-cols-2">
                        <article
                            v-for="lesson in lessons.data"
                            :key="lesson.id"
                            class="workspace-list-card rounded-[1.4rem] p-5"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="text-lg font-semibold text-slate-900">{{ lesson.name }}</h4>
                                        <span
                                            v-if="lesson.is_free"
                                            class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700"
                                        >
                                            Free
                                        </span>
                                    </div>
                                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ lesson.description }}</p>
                                </div>

                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500">
                                    {{ lesson.updated_at }}
                                </span>
                            </div>

                            <div class="mt-4 flex flex-wrap items-center gap-2">
                                <span
                                    class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em]"
                                    :class="lesson.is_free ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-900 text-white'"
                                >
                                    {{ displayPrice(lesson) }}
                                </span>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                    {{ lesson.level ? `Level ${lesson.level.name}` : 'Without level' }}
                                </span>
                                <span
                                    v-for="category in lesson.categories"
                                    :key="`${lesson.id}-${category.id}`"
                                    class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700"
                                >
                                    {{ category.name }}
                                </span>
                                <span
                                    v-if="!lesson.categories.length"
                                    class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500"
                                >
                                    No categories yet
                                </span>
                            </div>

                            <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                                <span
                                    class="rounded-full px-3 py-1 text-xs font-medium"
                                    :class="lesson.level && lesson.categories.length ? 'bg-emerald-50 text-emerald-700' : 'bg-orange-50 text-orange-700'"
                                >
                                    {{ lesson.level && lesson.categories.length ? 'Structured lesson' : 'Needs more structure' }}
                                </span>

                                <div class="flex items-center gap-2" v-if="canEdit || canDelete">
                                    <Link
                                        v-if="canEdit"
                                        :href="route('lessons.edit', lesson.id)"
                                        class="inline-flex rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-teal-200 hover:text-teal-800"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        v-if="canDelete"
                                        type="button"
                                        @click="deleteLesson(lesson.id)"
                                        class="inline-flex rounded-full bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div v-else class="workspace-empty mt-6 rounded-[1.5rem] px-6 py-10 text-center">
                        <h4 class="workspace-display text-2xl text-slate-900">No lessons yet</h4>
                        <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-slate-600">
                            Start with one strong lesson that has a level and at least one category, then build a cleaner content library from there.
                        </p>
                        <Link
                            v-if="canCreate"
                            :href="route('lessons.create')"
                            class="mt-5 inline-flex rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Create first lesson
                        </Link>
                    </div>

                    <div class="mt-6 flex items-center justify-between gap-3">
                        <Link
                            v-if="lessons.current_page > 1"
                            :href="lessons.prev_page_url"
                            class="inline-flex rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300"
                        >
                            Previous
                        </Link>
                        <div v-else />

                        <Link
                            v-if="lessons.current_page < lessons.last_page"
                            :href="lessons.next_page_url"
                            class="inline-flex rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300"
                        >
                            Next
                        </Link>
                        <div v-else />
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
