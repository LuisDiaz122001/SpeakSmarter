<script>
export default {
    name: "RolesIndex",
}
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    roles: {
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
const canCreate = computed(() => permissions.value.includes('create roles'));
const canEdit = computed(() => permissions.value.includes('edit roles'));
const canDelete = computed(() => permissions.value.includes('delete roles'));

const statCards = computed(() => [
    {
        title: 'Roles',
        value: props.overview.total,
        note: 'The number of access profiles currently defined.',
    },
    {
        title: 'Permissions',
        value: props.overview.permissions,
        note: 'Actions available across the permission system.',
    },
    {
        title: 'Assigned Users',
        value: props.overview.assigned_users,
        note: 'Accounts already attached to at least one role.',
    },
    {
        title: 'Custom Roles',
        value: props.overview.custom_roles,
        note: 'Roles beyond admin, editor and client.',
    },
]);

const deleteRole = (id) => {
    if (confirm('Are you sure you want to delete this role?')) {
        router.delete(route('roles.destroy', id));
    }
};
</script>

<template>
    <AppLayout title="Roles">
        <template #header>
            <div class="flex flex-col gap-2">
                <p class="workspace-kicker">Access Control</p>
                <h1 class="workspace-display text-3xl leading-tight text-slate-900">Roles designed for clear and safe access</h1>
            </div>
        </template>

        <div class="workspace-shell py-8 sm:py-10">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
                <section
                    class="workspace-hero rounded-[1.5rem] px-4 py-6 text-white sm:rounded-[2rem] sm:px-8 sm:py-8"
                    style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.98), rgba(51, 65, 85, 0.92));"
                >
                    <div class="relative z-10 grid gap-8 lg:grid-cols-[1.7fr_0.9fr]">
                        <div class="space-y-4">
                            <p class="text-xs uppercase tracking-[0.35em] text-white/70">Roles</p>
                            <h2 class="workspace-display max-w-2xl text-3xl leading-tight sm:text-4xl lg:text-5xl">
                                Build permission rules that stay easy to understand.
                            </h2>
                            <p class="max-w-2xl text-sm leading-7 text-white/78 sm:text-base">
                                This area helps you compare roles, understand their reach and keep admin, editor and client access intentionally separated.
                            </p>

                            <div class="flex flex-wrap gap-3 text-sm text-white/82">
                                <span class="rounded-full border border-white/20 bg-white/10 px-4 py-2">{{ roles.total }} roles</span>
                                <span class="rounded-full border border-white/20 bg-white/10 px-4 py-2">Admin area</span>
                            </div>
                        </div>

                        <div class="workspace-panel rounded-[1.5rem] p-5 text-slate-900">
                            <p class="workspace-kicker">Control Surface</p>
                            <h3 class="workspace-display mt-3 text-2xl text-slate-900">Manage who can see and change content</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">
                                Roles work best when each one has a clear purpose. Keep admin broad, editor focused and client lightweight.
                            </p>

                            <div class="mt-6 space-y-3 text-sm text-slate-600">
                                <div class="flex items-center justify-between">
                                    <span>Create roles</span>
                                    <span class="font-semibold text-slate-900">{{ canCreate ? 'Enabled' : 'Hidden' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Delete roles</span>
                                    <span class="font-semibold text-slate-900">{{ canDelete ? 'Enabled' : 'Hidden' }}</span>
                                </div>
                            </div>

                            <Link
                                v-if="canCreate"
                                :href="route('roles.create')"
                                class="mt-6 inline-flex rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                            >
                                Create role
                            </Link>
                        </div>
                    </div>
                </section>

                <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
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
                            <p class="workspace-kicker">Role Matrix</p>
                            <h3 class="workspace-display mt-2 text-2xl text-slate-900">Admin, editor and client at a glance</h3>
                        </div>

                        <Link
                            v-if="canCreate"
                            :href="route('roles.create')"
                            class="inline-flex rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            New role
                        </Link>
                    </div>

                    <div v-if="roles.data.length" class="mt-6 grid gap-4 xl:grid-cols-2">
                        <article
                            v-for="role in roles.data"
                            :key="role.id"
                            class="workspace-list-card rounded-[1.4rem] p-5"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="text-lg font-semibold text-slate-900">{{ role.name }}</h4>
                                        <span
                                            class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em]"
                                            :class="role.name === 'admin' ? 'bg-slate-900 text-white' : role.name === 'editor' ? 'bg-teal-100 text-teal-700' : role.name === 'client' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600'"
                                        >
                                            {{ role.name }}
                                        </span>
                                    </div>
                                    <p class="mt-3 text-sm leading-7 text-slate-600">
                                        {{ role.permissions_count }} {{ role.permissions_count === 1 ? 'permission' : 'permissions' }} across
                                        {{ role.surfaces.length }} {{ role.surfaces.length === 1 ? 'module' : 'modules' }}.
                                    </p>
                                </div>

                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500">
                                    {{ role.users_count }} {{ role.users_count === 1 ? 'user' : 'users' }}
                                </span>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <span
                                    v-for="surface in role.surfaces"
                                    :key="`${role.id}-${surface}`"
                                    class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700"
                                >
                                    {{ surface }}
                                </span>
                            </div>

                            <p class="mt-4 text-sm leading-7 text-slate-600">
                                {{ role.permissions.join(', ') || 'No permissions assigned yet.' }}
                            </p>

                            <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                                <span
                                    class="rounded-full px-3 py-1 text-xs font-medium"
                                    :class="role.name === 'admin' ? 'bg-slate-100 text-slate-700' : role.name === 'editor' ? 'bg-teal-50 text-teal-700' : role.name === 'client' ? 'bg-amber-50 text-amber-700' : 'bg-slate-50 text-slate-600'"
                                >
                                    {{ role.name === 'admin' ? 'Full control' : role.name === 'editor' ? 'Operational access' : role.name === 'client' ? 'Read-only access' : 'Custom access' }}
                                </span>

                                <div class="flex items-center gap-2" v-if="canEdit || canDelete">
                                    <Link
                                        v-if="canEdit"
                                        :href="route('roles.edit', role.id)"
                                        class="inline-flex rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-teal-200 hover:text-teal-800"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        v-if="canDelete"
                                        type="button"
                                        @click="deleteRole(role.id)"
                                        class="inline-flex rounded-full bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div v-else class="workspace-empty mt-6 rounded-[1.5rem] px-6 py-10 text-center">
                        <h4 class="workspace-display text-2xl text-slate-900">No roles available</h4>
                        <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-slate-600">
                            Start by defining access profiles for admins, editors and clients so the platform stays predictable.
                        </p>
                        <Link
                            v-if="canCreate"
                            :href="route('roles.create')"
                            class="mt-5 inline-flex rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Create first role
                        </Link>
                    </div>

                    <div class="mt-6 flex items-center justify-between gap-3">
                        <Link
                            v-if="roles.current_page > 1"
                            :href="roles.prev_page_url"
                            class="inline-flex rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300"
                        >
                            Previous
                        </Link>
                        <div v-else />

                        <Link
                            v-if="roles.current_page < roles.last_page"
                            :href="roles.next_page_url"
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
