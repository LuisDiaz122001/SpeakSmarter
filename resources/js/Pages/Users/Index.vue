<script>
export default {
    name: "UsersIndex",
}
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    users: {
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
const currentUserId = computed(() => page.props.auth.user.id);

const canCreate = computed(() => permissions.value.includes('create users'));
const canEdit = computed(() => permissions.value.includes('edit users'));
const canDelete = computed(() => permissions.value.includes('delete users'));

const statCards = computed(() => [
    {
        title: 'Usuarios totales',
        value: props.overview.total,
        note: 'Cuentas creadas dentro de la plataforma.',
    },
    {
        title: 'Administradores',
        value: props.overview.admins,
        note: 'Usuarios con control total del sistema.',
    },
    {
        title: 'Editores',
        value: props.overview.editors,
        note: 'Perfiles orientados a la gestion de contenido.',
    },
    {
        title: 'Clientes',
        value: props.overview.clients,
        note: 'Accesos de consulta y seguimiento.',
    },
    {
        title: 'Con telefono',
        value: props.overview.with_phone,
        note: 'Usuarios con contacto adicional registrado.',
    },
]);

const roleLabel = (roleName) => ({
    admin: 'Administrador',
    editor: 'Editor',
    client: 'Cliente',
}[roleName] ?? roleName);

const permissionLabel = (permissionName) => {
    const [action, module] = permissionName.split(' ');

    const actionLabel = {
        create: 'Crear',
        read: 'Ver',
        edit: 'Editar',
        update: 'Actualizar',
        delete: 'Eliminar',
    }[action] ?? action;

    const moduleLabel = {
        users: 'usuarios',
        roles: 'roles',
        lessons: 'lecciones',
        categories: 'categorias',
    }[module] ?? module;

    return `${actionLabel} ${moduleLabel}`;
};

const deleteUser = (user) => {
    if (user.id === currentUserId.value) {
        return;
    }

    if (confirm(`Seguro que quieres eliminar a ${user.name}?`)) {
        router.delete(route('users.destroy', user.id));
    }
};
</script>

<template>
    <AppLayout title="Usuarios">
        <template #header>
            <div class="flex flex-col gap-2">
                <p class="workspace-kicker">Usuarios</p>
                <h1 class="workspace-display text-3xl leading-tight text-slate-900">Administra accesos, roles y permisos desde un solo panel</h1>
            </div>
        </template>

        <div class="workspace-shell py-8 sm:py-10">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
                <section
                    class="workspace-hero rounded-[2rem] px-6 py-8 text-white sm:px-8"
                    style="background: linear-gradient(135deg, rgba(20, 33, 61, 0.98), rgba(37, 99, 235, 0.92));"
                >
                    <div class="relative z-10 grid gap-8 lg:grid-cols-[1.7fr_0.9fr]">
                        <div class="space-y-4">
                            <p class="text-xs uppercase tracking-[0.35em] text-white/70">Gestion de accesos</p>
                            <h2 class="workspace-display max-w-2xl text-4xl leading-tight sm:text-5xl">
                                Crea usuarios concretos para cada profesor, cliente o colaborador.
                            </h2>
                            <p class="max-w-2xl text-sm leading-7 text-white/78 sm:text-base">
                                Aqui puedes centralizar nombre, contacto, credenciales y nivel de acceso sin depender del registro libre de cada persona.
                            </p>

                            <div class="flex flex-wrap gap-3 text-sm text-white/82">
                                <span class="rounded-full border border-white/20 bg-white/10 px-4 py-2">{{ users.total }} usuarios</span>
                                <span class="rounded-full border border-white/20 bg-white/10 px-4 py-2">{{ overview.admins }} con rol admin</span>
                            </div>
                        </div>

                        <div class="workspace-panel rounded-[1.5rem] p-5 text-slate-900">
                            <p class="workspace-kicker">Accion principal</p>
                            <h3 class="workspace-display mt-3 text-2xl text-slate-900">Prepara accesos listos para entrar</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">
                                El administrador puede crear usuarios con password propia y dejar resueltos los roles o permisos especiales desde el primer acceso.
                            </p>

                            <Link
                                v-if="canCreate"
                                :href="route('users.create')"
                                class="mt-6 inline-flex rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                            >
                                Crear usuario
                            </Link>
                        </div>
                    </div>
                </section>

                <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                    <article
                        v-for="card in statCards"
                        :key="card.title"
                        class="workspace-stat-card rounded-[1.5rem] p-5"
                    >
                        <p class="workspace-kicker">{{ card.title }}</p>
                        <p class="workspace-display mt-4 text-4xl text-slate-900">{{ card.value }}</p>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ card.note }}</p>
                    </article>
                </section>

                <section class="workspace-panel rounded-[1.75rem] p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="workspace-kicker">Directorio interno</p>
                            <h3 class="workspace-display mt-2 text-2xl text-slate-900">Usuarios creados en la plataforma</h3>
                        </div>

                        <Link
                            v-if="canCreate"
                            :href="route('users.create')"
                            class="inline-flex rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Nuevo usuario
                        </Link>
                    </div>

                    <div v-if="users.data.length" class="mt-6 grid gap-4 xl:grid-cols-2">
                        <article
                            v-for="user in users.data"
                            :key="user.id"
                            class="workspace-list-card rounded-[1.4rem] p-5"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="text-lg font-semibold text-slate-900">{{ user.name }}</h4>
                                        <span
                                            v-if="user.id === currentUserId"
                                            class="rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white"
                                        >
                                            Sesion actual
                                        </span>
                                    </div>
                                    <p class="mt-2 text-sm text-slate-600">{{ user.email }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ user.phone || 'Sin telefono registrado' }}</p>
                                </div>

                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500">
                                    {{ user.updated_at }}
                                </span>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <span
                                    v-for="role in user.roles"
                                    :key="`${user.id}-${role}`"
                                    class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700"
                                >
                                    {{ roleLabel(role) }}
                                </span>
                                <span
                                    v-if="!user.roles.length"
                                    class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500"
                                >
                                    Sin rol asignado
                                </span>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <span
                                    v-for="permission in user.permissions.slice(0, 4)"
                                    :key="`${user.id}-${permission}`"
                                    class="rounded-full bg-teal-50 px-3 py-1 text-xs font-medium text-teal-700"
                                >
                                    {{ permissionLabel(permission) }}
                                </span>
                                <span
                                    v-if="user.permissions.length > 4"
                                    class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600"
                                >
                                    +{{ user.permissions.length - 4 }} permisos directos
                                </span>
                                <span
                                    v-if="!user.permissions.length"
                                    class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500"
                                >
                                    Sin permisos directos extra
                                </span>
                            </div>

                            <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                    {{ user.effective_permissions.length }} permisos efectivos
                                </span>

                                <div class="flex items-center gap-2" v-if="canEdit || canDelete">
                                    <Link
                                        v-if="canEdit"
                                        :href="route('users.edit', user.id)"
                                        class="inline-flex rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-teal-200 hover:text-teal-800"
                                    >
                                        Editar
                                    </Link>
                                    <button
                                        v-if="canDelete && user.id !== currentUserId"
                                        type="button"
                                        @click="deleteUser(user)"
                                        class="inline-flex rounded-full bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700"
                                    >
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div v-else class="workspace-empty mt-6 rounded-[1.5rem] px-6 py-10 text-center">
                        <h4 class="workspace-display text-2xl text-slate-900">Aun no has creado usuarios</h4>
                        <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-slate-600">
                            Empieza creando cuentas concretas para tu equipo o para clientes puntuales y asi tendras el acceso completamente controlado.
                        </p>
                        <Link
                            v-if="canCreate"
                            :href="route('users.create')"
                            class="mt-5 inline-flex rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Crear primer usuario
                        </Link>
                    </div>

                    <div class="mt-6 flex items-center justify-between gap-3">
                        <Link
                            v-if="users.current_page > 1"
                            :href="users.prev_page_url"
                            class="inline-flex rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300"
                        >
                            Anterior
                        </Link>
                        <div v-else />

                        <Link
                            v-if="users.current_page < users.last_page"
                            :href="users.next_page_url"
                            class="inline-flex rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300"
                        >
                            Siguiente
                        </Link>
                        <div v-else />
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
