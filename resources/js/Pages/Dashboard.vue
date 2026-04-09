<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    overview: {
        type: Object,
        required: true,
    },
    recentLessons: {
        type: Array,
        required: true,
    },
    recentCategories: {
        type: Array,
        required: true,
    },
    checklist: {
        type: Array,
        required: true,
    },
});

const page = usePage();

const roles = computed(() => page.props.user?.roles ?? []);
const permissions = computed(() => page.props.user?.permissions ?? []);
const firstName = computed(() => page.props.auth.user.name.split(' ')[0] ?? page.props.auth.user.name);

const statCards = computed(() => [
    {
        title: 'Lessons Ready',
        value: props.overview.lessons,
        note: 'Classroom-ready content available for reuse.',
        tone: 'mint',
    },
    {
        title: 'Categories Built',
        value: props.overview.categories,
        note: 'Topics that keep your library organized.',
        tone: 'sun',
    },
    {
        title: 'Levels Active',
        value: props.overview.levels,
        note: 'Progression stages visible from A1 to C2.',
        tone: 'clay',
    },
    {
        title: 'Your Access',
        value: props.overview.permissions,
        note: 'Permissions currently available in your account.',
        tone: 'ink',
    },
]);

const quickActions = computed(() => {
    const actions = [];

    if (permissions.value.includes('create lessons')) {
        actions.push({
            title: 'Create a lesson',
            description: 'Add a new class resource with level and categories.',
            href: route('lessons.create'),
        });
    }

    if (permissions.value.includes('create categories')) {
        actions.push({
            title: 'Create a category',
            description: 'Organize the library by topics or teaching goals.',
            href: route('categories.create'),
        });
    }

    if (permissions.value.includes('create roles')) {
        actions.push({
            title: 'Create a role',
            description: 'Define who can create, edit and manage content.',
            href: route('roles.create'),
        });
    }

    return actions;
});

const nextStep = computed(() => props.checklist.find((item) => !item.complete) ?? props.checklist.at(-1));
const visiblePermissions = computed(() => permissions.value.slice(0, 8));
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
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="flex flex-col gap-2">
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">SpeakSmarter Workspace</p>
                <h2 class="display-title text-3xl text-slate-900 leading-tight">
                    A cleaner teaching cockpit for {{ firstName }}
                </h2>
            </div>
        </template>

        <div class="dashboard-shell py-8 sm:py-10">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
                <section class="dashboard-hero relative overflow-hidden rounded-[1.5rem] px-4 py-6 text-white shadow-[0_30px_80px_rgba(15,23,42,0.24)] sm:rounded-[2rem] sm:px-8 sm:py-8">
                    <div class="hero-orb hero-orb-left" />
                    <div class="hero-orb hero-orb-right" />

                    <div class="relative z-10 grid gap-8 lg:grid-cols-[1.7fr_0.9fr]">
                        <div class="space-y-5">
                            <div class="space-y-3">
                                <p class="text-xs uppercase tracking-[0.35em] text-white/70">Today at a glance</p>
                                <h1 class="display-title max-w-2xl text-3xl leading-tight sm:text-4xl lg:text-5xl">
                                    Teach with more clarity, not more clutter.
                                </h1>
                                <p class="max-w-2xl text-sm leading-7 text-white/78 sm:text-base">
                                    This dashboard highlights what is already structured, what needs attention, and the fastest next action for your team.
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 text-sm text-white/80">
                                <span class="w-full rounded-full border border-white/20 bg-white/10 px-4 py-2 sm:w-auto">Completion: {{ overview.completion }}%</span>
                                <span class="w-full rounded-full border border-white/20 bg-white/10 px-4 py-2 sm:w-auto">{{ roles.length ? roles.join(' / ') : 'No roles assigned yet' }}</span>
                            </div>
                        </div>

                        <div class="dashboard-panel rounded-[1.5rem] p-5 text-slate-900">
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Next focus</p>
                            <h3 class="mt-3 display-title text-2xl leading-tight text-slate-900">
                                {{ nextStep.title }}
                            </h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">
                                {{ nextStep.description }}
                            </p>

                            <div class="mt-6 h-2 overflow-hidden rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-gradient-to-r from-teal-500 via-amber-400 to-orange-500" :style="{ width: `${overview.completion}%` }" />
                            </div>

                            <div class="mt-6 space-y-3 text-sm text-slate-600">
                                <div class="flex items-center justify-between">
                                    <span>Completed milestones</span>
                                    <span class="font-semibold text-slate-900">{{ checklist.filter(item => item.complete).length }}/{{ checklist.length }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Available permissions</span>
                                    <span class="font-semibold text-slate-900">{{ overview.permissions }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <article
                        v-for="card in statCards"
                        :key="card.title"
                        class="dashboard-panel metric-card rounded-[1.5rem] p-5"
                        :class="`metric-card--${card.tone}`"
                    >
                        <p class="metric-kicker text-xs uppercase tracking-[0.28em]">{{ card.title }}</p>
                        <p class="mt-4 display-title text-3xl text-slate-900 sm:text-4xl">{{ card.value }}</p>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ card.note }}</p>
                    </article>
                </section>

                <section class="grid gap-6 xl:grid-cols-[1.55fr_1fr]">
                    <div class="space-y-6">
                        <article class="dashboard-panel rounded-[1.75rem] p-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.28em] text-slate-500">Didactic roadmap</p>
                                    <h3 class="mt-2 display-title text-2xl text-slate-900">A simple path to a more teachable platform</h3>
                                </div>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Minimal flow</span>
                            </div>

                            <div class="mt-6 grid gap-4 lg:grid-cols-2">
                                <article
                                    v-for="item in checklist"
                                    :key="item.title"
                                    class="rounded-[1.25rem] border px-4 py-4 transition duration-200"
                                    :class="item.complete ? 'border-emerald-200 bg-emerald-50/70' : 'border-slate-200 bg-white'"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">{{ item.title }}</p>
                                            <p class="mt-2 text-sm leading-7 text-slate-600">{{ item.description }}</p>
                                        </div>
                                        <span
                                            class="mt-1 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-semibold"
                                            :class="item.complete ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500'"
                                        >
                                            {{ item.complete ? 'OK' : 'GO' }}
                                        </span>
                                    </div>
                                </article>
                            </div>
                        </article>

                        <article class="dashboard-panel rounded-[1.75rem] p-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.28em] text-slate-500">Latest lessons</p>
                                    <h3 class="mt-2 display-title text-2xl text-slate-900">Content that was touched recently</h3>
                                </div>
                                <Link
                                    v-if="permissions.includes('read lessons')"
                                    :href="route('lessons.index')"
                                    class="text-sm font-semibold text-teal-700 transition hover:text-teal-800"
                                >
                                    Open lessons
                                </Link>
                            </div>

                            <div v-if="recentLessons.length" class="mt-6 space-y-4">
                                <article
                                    v-for="lesson in recentLessons"
                                    :key="lesson.id"
                                    class="rounded-[1.25rem] border border-slate-200 bg-white px-4 py-4"
                                >
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h4 class="text-base font-semibold text-slate-900">{{ lesson.name }}</h4>
                                                <span v-if="lesson.is_free" class="rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-emerald-700">Free</span>
                                                <span
                                                    v-else
                                                    class="rounded-full bg-slate-900 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-white"
                                                >
                                                    {{ displayPrice(lesson) }}
                                                </span>
                                            </div>
                                            <p class="mt-2 text-sm leading-7 text-slate-600">{{ lesson.description }}</p>
                                        </div>
                                        <span class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ lesson.updated_at }}</span>
                                    </div>

                                    <div class="mt-4 flex flex-wrap items-center gap-2">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                            {{ lesson.level ? `Level ${lesson.level}` : 'Without level' }}
                                        </span>
                                        <span
                                            v-for="category in lesson.categories"
                                            :key="`${lesson.id}-${category}`"
                                            class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700"
                                        >
                                            {{ category }}
                                        </span>
                                    </div>
                                </article>
                            </div>

                            <div v-else class="mt-6 rounded-[1.25rem] border border-dashed border-slate-300 bg-white px-5 py-8 text-center text-sm leading-7 text-slate-500">
                                There are no lessons yet. Start with one strong, reusable lesson and build from there.
                            </div>
                        </article>
                    </div>

                    <div class="space-y-6">
                        <article class="dashboard-panel rounded-[1.75rem] p-6">
                            <p class="text-xs uppercase tracking-[0.28em] text-slate-500">Quick actions</p>
                            <h3 class="mt-2 display-title text-2xl text-slate-900">Start the next meaningful task</h3>

                            <div v-if="quickActions.length" class="mt-6 space-y-3">
                                <Link
                                    v-for="action in quickActions"
                                    :key="action.title"
                                    :href="action.href"
                                    class="group block rounded-[1.25rem] border border-slate-200 bg-white px-4 py-4 transition duration-200 hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-[0_20px_40px_rgba(15,118,110,0.12)]"
                                >
                                    <p class="text-sm font-semibold text-slate-900 group-hover:text-teal-800">{{ action.title }}</p>
                                    <p class="mt-2 text-sm leading-7 text-slate-600">{{ action.description }}</p>
                                </Link>
                            </div>

                            <div v-else class="mt-6 rounded-[1.25rem] border border-dashed border-slate-300 bg-white px-5 py-8 text-center text-sm leading-7 text-slate-500">
                                Your current role does not expose creation actions yet.
                            </div>
                        </article>

                        <article class="dashboard-panel rounded-[1.75rem] p-6">
                            <p class="text-xs uppercase tracking-[0.28em] text-slate-500">Recent categories</p>
                            <h3 class="mt-2 display-title text-2xl text-slate-900">What is shaping the content map</h3>

                            <div v-if="recentCategories.length" class="mt-6 space-y-3">
                                <div
                                    v-for="category in recentCategories"
                                    :key="category.id"
                                    class="flex items-center justify-between gap-3 rounded-[1.1rem] bg-white px-4 py-4"
                                >
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ category.name }}</p>
                                        <p class="mt-1 text-xs uppercase tracking-[0.22em] text-slate-400">{{ category.updated_at }}</p>
                                    </div>
                                    <div class="rounded-full bg-slate-100 px-3 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-600">
                                        {{ category.lessons_count }} lessons
                                    </div>
                                </div>
                            </div>

                            <div v-else class="mt-6 rounded-[1.25rem] border border-dashed border-slate-300 bg-white px-5 py-8 text-center text-sm leading-7 text-slate-500">
                                Categories will appear here as soon as the library starts taking shape.
                            </div>
                        </article>

                        <article class="dashboard-panel rounded-[1.75rem] p-6">
                            <p class="text-xs uppercase tracking-[0.28em] text-slate-500">Access snapshot</p>
                            <h3 class="mt-2 display-title text-2xl text-slate-900">Roles and permissions at a glance</h3>

                            <div class="mt-6 space-y-4">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Roles</p>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <span
                                            v-for="role in roles"
                                            :key="role"
                                            class="rounded-full bg-slate-900 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-white"
                                        >
                                            {{ role }}
                                        </span>
                                        <span v-if="!roles.length" class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                                            No roles
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Permissions</p>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <span
                                            v-for="permission in visiblePermissions"
                                            :key="permission"
                                            class="rounded-full bg-teal-50 px-3 py-1.5 text-xs font-medium text-teal-700"
                                        >
                                            {{ permission }}
                                        </span>
                                        <span v-if="permissions.length > visiblePermissions.length" class="rounded-full bg-amber-50 px-3 py-1.5 text-xs font-medium text-amber-700">
                                            +{{ permissions.length - visiblePermissions.length }} more
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.dashboard-shell {
    --dashboard-paper: #f7f3ea;
    --dashboard-panel: rgba(255, 255, 255, 0.8);
    --dashboard-line: rgba(15, 23, 42, 0.08);
    --dashboard-ink: #14213d;
    --dashboard-mint: #0f766e;
    --dashboard-sun: #d97706;
    --dashboard-clay: #c2410c;
    --font-display: "Iowan Old Style", "Palatino Linotype", "Book Antiqua", Georgia, serif;
    --font-body: "Trebuchet MS", "Gill Sans", "Segoe UI", sans-serif;
    background:
        radial-gradient(circle at top left, rgba(15, 118, 110, 0.14), transparent 32%),
        radial-gradient(circle at top right, rgba(217, 119, 6, 0.14), transparent 26%),
        linear-gradient(180deg, #f6f2e9 0%, #fcfaf5 45%, #f8f5ee 100%);
    font-family: var(--font-body);
}

.display-title {
    font-family: var(--font-display);
    letter-spacing: -0.03em;
}

.dashboard-hero {
    background:
        linear-gradient(135deg, rgba(20, 33, 61, 0.98), rgba(15, 118, 110, 0.9)),
        linear-gradient(180deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0));
}

.dashboard-panel {
    border: 1px solid var(--dashboard-line);
    background: var(--dashboard-panel);
    backdrop-filter: blur(16px);
    box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);
}

.hero-orb {
    position: absolute;
    border-radius: 9999px;
    filter: blur(8px);
    opacity: 0.55;
}

.hero-orb-left {
    top: -3rem;
    left: -2rem;
    height: 10rem;
    width: 10rem;
    background: rgba(251, 191, 36, 0.26);
}

.hero-orb-right {
    right: -2.5rem;
    bottom: -4rem;
    height: 12rem;
    width: 12rem;
    background: rgba(255, 255, 255, 0.16);
}

.metric-card {
    position: relative;
    overflow: hidden;
}

.metric-card::after {
    content: "";
    position: absolute;
    inset: auto auto 1rem 1rem;
    height: 0.65rem;
    width: 0.65rem;
    border-radius: 9999px;
}

.metric-card--mint::after {
    background: var(--dashboard-mint);
}

.metric-card--mint .metric-kicker {
    color: var(--dashboard-mint);
}

.metric-card--sun::after {
    background: var(--dashboard-sun);
}

.metric-card--sun .metric-kicker {
    color: var(--dashboard-sun);
}

.metric-card--clay::after {
    background: var(--dashboard-clay);
}

.metric-card--clay .metric-kicker {
    color: var(--dashboard-clay);
}

.metric-card--ink::after {
    background: var(--dashboard-ink);
}

.metric-card--ink .metric-kicker {
    color: var(--dashboard-ink);
}

@media (max-width: 639px) {
    .dashboard-panel {
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.07);
    }

    .hero-orb-left {
        top: -2rem;
        left: -1.5rem;
        height: 7rem;
        width: 7rem;
    }

    .hero-orb-right {
        right: -1.5rem;
        bottom: -2rem;
        height: 8rem;
        width: 8rem;
    }
}
</style>
