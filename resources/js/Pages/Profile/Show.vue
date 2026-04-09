<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});

const page = usePage();
const roleLabel = (role) => ({
    admin: 'Administrador',
    editor: 'Editor',
    client: 'Cliente',
}[role] ?? role);

const currentRoles = computed(() => page.props.user?.roles ?? []);
const profileStats = computed(() => [
    {
        label: 'Correo',
        value: page.props.auth.user.email,
    },
    {
        label: 'Roles',
        value: currentRoles.value.length ? currentRoles.value.map(roleLabel).join(' / ') : 'Sin rol asignado',
    },
    {
        label: 'Seguridad',
        value: page.props.auth.user.two_factor_enabled ? '2FA activa' : '2FA pendiente',
    },
]);
</script>

<template>
    <AppLayout title="Perfil">
        <template #header>
            <div class="flex flex-col gap-2">
                <p class="workspace-kicker">Cuenta personal</p>
                <h2 class="workspace-display text-3xl leading-tight text-slate-900">
                    Un perfil mas claro para gestionar tu acceso y seguridad
                </h2>
            </div>
        </template>

        <div class="workspace-profile-shell py-8 sm:py-10">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
                <section
                    class="workspace-hero rounded-[2rem] px-6 py-8 text-white sm:px-8"
                    style="background: linear-gradient(135deg, rgba(20, 33, 61, 0.98), rgba(15, 118, 110, 0.9));"
                >
                    <div class="relative z-10 grid gap-8 lg:grid-cols-[1.6fr_0.95fr]">
                        <div class="space-y-4">
                            <p class="text-xs uppercase tracking-[0.35em] text-white/70">Perfil</p>
                            <h1 class="workspace-display max-w-2xl text-4xl leading-tight sm:text-5xl">
                                Manten tus datos, tu seguridad y tus sesiones bajo control.
                            </h1>
                            <p class="max-w-2xl text-sm leading-7 text-white/78 sm:text-base">
                                Esta vista reune lo esencial para que tu cuenta se sienta limpia, moderna y facil de revisar sin perder contexto.
                            </p>
                        </div>

                        <div class="grid gap-3">
                            <article
                                v-for="stat in profileStats"
                                :key="stat.label"
                                class="workspace-profile-stat rounded-[1.35rem] px-4 py-4"
                            >
                                <p class="text-xs uppercase tracking-[0.2em] text-white/60">{{ stat.label }}</p>
                                <p class="mt-3 text-sm font-semibold leading-6 text-white">{{ stat.value }}</p>
                            </article>
                        </div>
                    </div>
                </section>

                <div class="max-w-7xl mx-auto w-full">
                <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                    <UpdateProfileInformationForm :user="$page.props.auth.user" />

                    <SectionBorder />
                </div>

                <div v-if="$page.props.jetstream.canUpdatePassword">
                    <UpdatePasswordForm class="mt-10 sm:mt-0" />

                    <SectionBorder />
                </div>

                <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                    <TwoFactorAuthenticationForm
                        :requires-confirmation="confirmsTwoFactorAuthentication"
                        class="mt-10 sm:mt-0"
                    />

                    <SectionBorder />
                </div>

                <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" />

                <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                    <SectionBorder />

                    <DeleteUserForm class="mt-10 sm:mt-0" />
                </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
