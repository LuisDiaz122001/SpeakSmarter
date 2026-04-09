<script>
export default {
    name: "UsersEdit",
}
</script>

<script setup>
import UserForm from '@/Components/Users/Form.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    managedUser: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
        required: true,
    },
    permissions: {
        type: Object,
        required: true,
    },
    rolePermissionMap: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    first_name: props.managedUser.first_name ?? '',
    last_name: props.managedUser.last_name ?? '',
    phone: props.managedUser.phone ?? '',
    email: props.managedUser.email,
    password: '',
    password_confirmation: '',
    roles: [...props.managedUser.roles],
    permissions: [...props.managedUser.permissions],
});
</script>

<template>
    <AppLayout title="Editar usuario">
        <template #header>
            <div class="flex flex-col gap-2">
                <p class="workspace-kicker">Usuarios</p>
                <h1 class="workspace-display text-3xl leading-tight text-slate-900">Edita los datos y el acceso de {{ managedUser.name }}</h1>
            </div>
        </template>

        <div class="workspace-shell py-8 sm:py-10">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
                <section
                    class="workspace-hero rounded-[2rem] px-6 py-8 text-white sm:px-8"
                    style="background: linear-gradient(135deg, rgba(20, 33, 61, 0.98), rgba(2, 132, 199, 0.9));"
                >
                    <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-3xl">
                            <p class="text-xs uppercase tracking-[0.35em] text-white/70">Edicion de usuario</p>
                            <h2 class="workspace-display mt-3 text-4xl leading-tight sm:text-5xl">
                                Manten a <span class="text-white/80">{{ managedUser.name }}</span> dentro del alcance correcto.
                            </h2>
                            <p class="mt-3 text-sm leading-7 text-white/78 sm:text-base">
                                Puedes actualizar sus datos de contacto, redefinir su acceso o cambiar la contrasena si necesitas facilitarle una nueva entrada.
                            </p>
                        </div>

                        <Link
                            :href="route('users.index')"
                            class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20"
                        >
                            Volver a usuarios
                        </Link>
                    </div>
                </section>

                <UserForm
                    :updating="true"
                    :form="form"
                    :roles="roles"
                    :permission-groups="permissions"
                    :role-permission-map="rolePermissionMap"
                    @submit="form.put(route('users.update', managedUser.id))"
                />
            </div>
        </div>
    </AppLayout>
</template>
