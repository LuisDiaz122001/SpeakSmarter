<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    title: String,
});

const page = usePage();
const showingNavigationDropdown = ref(false);

const currentRoles = computed(() => page.props.user?.roles ?? []);
const primaryRoleLabel = computed(() => {
    const labels = {
        admin: 'Administrador',
        editor: 'Editor',
        client: 'Cliente',
    };

    return labels[currentRoles.value[0]] ?? 'Cuenta activa';
});

const userInitials = computed(() => {
    const parts = String(page.props.auth.user.name ?? '')
        .split(' ')
        .filter(Boolean)
        .slice(0, 2);

    return parts.map((part) => part[0]).join('').toUpperCase() || 'SS';
});

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationMark class="block h-9 w-auto" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 lg:-my-px lg:ms-10 lg:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>
                            </div>
                            <div class="hidden space-x-8 lg:-my-px lg:ms-10 lg:flex" v-if="$page.props.user.permissions.some(p => p.trim() === 'read categories')">
                                <NavLink :href="route('categories.index')" :active="route().current('categories.*')">
                                    Categories
                                </NavLink>
                            </div>
                            <div class="hidden space-x-8 lg:-my-px lg:ms-10 lg:flex" v-if="$page.props.user.permissions.some(p => p.trim() === 'read lessons')">
                                <NavLink :href="route('lessons.index')" :active="route().current('lessons.*')">
                                    Lessons
                                </NavLink>
                            </div>
                            <div class="hidden space-x-8 lg:-my-px lg:ms-10 lg:flex" v-if="$page.props.user.permissions.some(p => p.trim() === 'read roles')">
                                <NavLink :href="route('roles.index')" :active="route().current('roles.*')">
                                    Roles
                                </NavLink>
                            </div>
                            <div class="hidden space-x-8 lg:-my-px lg:ms-10 lg:flex" v-if="$page.props.user.permissions.some(p => p.trim() === 'read users')">
                                <NavLink :href="route('users.index')" :active="route().current('users.*')">
                                    Usuarios
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden lg:flex lg:items-center lg:ms-6">
                            <div class="ms-3 relative">
                                <!-- Teams Dropdown -->
                                <Dropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.current_team.name }}

                                                <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <div class="block px-4 py-2 text-xs text-gray-400">
                                                Gestionar equipo
                                            </div>

                                            <!-- Team Settings -->
                                            <DropdownLink :href="route('teams.show', $page.props.auth.user.current_team)">
                                                Ajustes del equipo
                                            </DropdownLink>

                                            <DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">
                                                Crear equipo
                                            </DropdownLink>

                                            <!-- Team Switcher -->
                                            <template v-if="$page.props.auth.user.all_teams.length > 1">
                                                <div class="border-t border-gray-200" />

                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Cambiar equipo
                                                </div>

                                                <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                                    <form @submit.prevent="switchToTeam(team)">
                                                        <DropdownLink as="button">
                                                            <div class="flex items-center">
                                                                <svg v-if="team.id == $page.props.auth.user.current_team_id" class="me-2 size-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>

                                                                <div>{{ team.name }}</div>
                                                            </div>
                                                        </DropdownLink>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="72" :content-classes="['workspace-account-panel', 'p-2']">
                                    <template #trigger>
                                        <button type="button" class="workspace-account-trigger focus:outline-none focus:ring-2 focus:ring-teal-500/30">
                                            <span class="workspace-account-avatar">
                                                <img
                                                    v-if="$page.props.jetstream.managesProfilePhotos"
                                                    class="h-full w-full object-cover"
                                                    :src="$page.props.auth.user.profile_photo_url"
                                                    :alt="$page.props.auth.user.name"
                                                >
                                                <span v-else>{{ userInitials }}</span>
                                            </span>

                                            <span class="hidden min-w-0 text-start lg:block">
                                                <span class="block truncate text-sm font-semibold text-slate-900">{{ $page.props.auth.user.name }}</span>
                                                <span class="block text-xs uppercase tracking-[0.18em] text-slate-500">{{ primaryRoleLabel }}</span>
                                            </span>

                                            <svg class="size-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <div class="rounded-[1.1rem] bg-slate-50 px-4 py-4">
                                            <p class="text-xs uppercase tracking-[0.26em] text-slate-400">Cuenta</p>
                                            <p class="mt-2 text-sm font-semibold text-slate-900">{{ $page.props.auth.user.name }}</p>
                                            <p class="mt-1 text-sm text-slate-600">{{ $page.props.auth.user.email }}</p>
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                <span
                                                    v-for="role in currentRoles"
                                                    :key="role"
                                                    class="rounded-full bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-600"
                                                >
                                                    {{ role === 'admin' ? 'Administrador' : role === 'editor' ? 'Editor' : role === 'client' ? 'Cliente' : role }}
                                                </span>
                                                <span v-if="!currentRoles.length" class="rounded-full bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">
                                                    Sin rol
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mt-2 space-y-1">
                                            <Link :href="route('profile.show')" class="workspace-account-link">
                                                <p class="text-sm font-semibold text-slate-900">Perfil</p>
                                                <p class="mt-1 text-xs leading-5 text-slate-500">Actualiza tus datos, seguridad y configuracion personal.</p>
                                            </Link>

                                            <Link v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')" class="workspace-account-link">
                                                <p class="text-sm font-semibold text-slate-900">Tokens API</p>
                                                <p class="mt-1 text-xs leading-5 text-slate-500">Gestiona accesos de integracion para tus conexiones externas.</p>
                                            </Link>
                                        </div>

                                        <div class="my-2 border-t border-slate-200" />

                                        <form @submit.prevent="logout">
                                            <button type="submit" class="workspace-account-link w-full text-start">
                                                <p class="text-sm font-semibold text-red-700">Cerrar sesion</p>
                                                <p class="mt-1 text-xs leading-5 text-slate-500">Salir de la cuenta actual en este dispositivo.</p>
                                            </button>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center lg:hidden">
                            <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out" @click="showingNavigationDropdown = ! showingNavigationDropdown">
                                <svg
                                    class="size-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="lg:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <div class="pt-2 pb-3 space-y-1" v-if="$page.props.user.permissions.some(p => p.trim() === 'read categories')">
                        <ResponsiveNavLink :href="route('categories.index')" :active="route().current('categories.*')">
                            Categories
                        </ResponsiveNavLink>
                    </div>

                    <div class="pt-2 pb-3 space-y-1" v-if="$page.props.user.permissions.some(p => p.trim() === 'read lessons')">
                        <ResponsiveNavLink :href="route('lessons.index')" :active="route().current('lessons.*')">
                            Lessons
                        </ResponsiveNavLink>
                    </div>

                    <div class="pt-2 pb-3 space-y-1" v-if="$page.props.user.permissions.some(p => p.trim() === 'read roles')">
                        <ResponsiveNavLink :href="route('roles.index')" :active="route().current('roles.*')">
                            Roles
                        </ResponsiveNavLink>
                    </div>

                    <div class="pt-2 pb-3 space-y-1" v-if="$page.props.user.permissions.some(p => p.trim() === 'read users')">
                        <ResponsiveNavLink :href="route('users.index')" :active="route().current('users.*')">
                            Usuarios
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
                                <img class="size-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </div>

                            <div>
                                <div class="font-medium text-base text-gray-800">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                Perfil
                            </ResponsiveNavLink>

                            <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                                Tokens API
                            </ResponsiveNavLink>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <ResponsiveNavLink as="button">
                                    Cerrar sesion
                                </ResponsiveNavLink>
                            </form>

                            <!-- Team Management -->
                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                <div class="border-t border-gray-200" />

                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Gestionar equipo
                                </div>

                                <!-- Team Settings -->
                                <ResponsiveNavLink :href="route('teams.show', $page.props.auth.user.current_team)" :active="route().current('teams.show')">
                                    Ajustes del equipo
                                </ResponsiveNavLink>

                                <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')" :active="route().current('teams.create')">
                                    Crear equipo
                                </ResponsiveNavLink>

                                <!-- Team Switcher -->
                                <template v-if="$page.props.auth.user.all_teams.length > 1">
                                    <div class="border-t border-gray-200" />

                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        Cambiar equipo
                                    </div>

                                    <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                        <form @submit.prevent="switchToTeam(team)">
                                            <ResponsiveNavLink as="button">
                                                <div class="flex items-center">
                                                    <svg v-if="team.id == $page.props.auth.user.current_team_id" class="me-2 size-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <div>{{ team.name }}</div>
                                                </div>
                                            </ResponsiveNavLink>
                                        </form>
                                    </template>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
