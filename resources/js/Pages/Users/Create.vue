<script>
export default {
    name: "UsersCreate",
}
</script>

<script setup>
import UserForm from '@/Components/Users/Form.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

defineProps({
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
    first_name: '',
    last_name: '',
    phone: '',
    email: '',
    password: '',
    password_confirmation: '',
    roles: [],
    permissions: [],
});
</script>

<template>
    <AppLayout title="Crear usuario">
        <template #header>
            <div class="flex flex-col gap-2">
                <p class="workspace-kicker">Usuarios</p>
                <h1 class="workspace-display text-3xl leading-tight text-slate-900">Crea accesos nuevos para profesores, clientes o colaboradores</h1>
            </div>
        </template>

        <div class="workspace-shell py-8 sm:py-10">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
                <section
                    class="workspace-hero rounded-[2rem] px-6 py-8 text-white sm:px-8"
                    style="background: linear-gradient(135deg, rgba(20, 33, 61, 0.98), rgba(37, 99, 235, 0.9));"
                >
                    <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-3xl">
                            <p class="text-xs uppercase tracking-[0.35em] text-white/70">Alta de usuario</p>
                            <h2 class="workspace-display mt-3 text-4xl leading-tight sm:text-5xl">
                                Entrega acceso listo para usar sin depender de un registro manual.
                            </h2>
                            <p class="mt-3 text-sm leading-7 text-white/78 sm:text-base">
                                Desde aqui puedes crear cuentas para docentes, alumnos o perfiles internos y dejar definidos sus roles y permisos desde el primer minuto.
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
                    :form="form"
                    :roles="roles"
                    :permission-groups="permissions"
                    :role-permission-map="rolePermissionMap"
                    @submit="form.post(route('users.store'))"
                />
            </div>
        </div>
    </AppLayout>
</template>
