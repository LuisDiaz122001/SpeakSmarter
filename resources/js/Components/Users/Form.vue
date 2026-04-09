<script>
export default {
    name: "UserForm",
}
</script>

<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { computed } from 'vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
        required: true,
    },
    permissionGroups: {
        type: Object,
        required: true,
    },
    rolePermissionMap: {
        type: Object,
        required: true,
    },
    updating: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['submit']);

const selectedRoleCount = computed(() => props.form.roles.length);
const selectedPermissionCount = computed(() => props.form.permissions.length);
const effectivePermissionCount = computed(() => {
    const inheritedPermissions = props.form.roles.flatMap((role) => props.rolePermissionMap[role] ?? []);

    return new Set([...inheritedPermissions, ...props.form.permissions]).size;
});

const roleLabel = (roleName) => ({
    admin: 'Administrador',
    editor: 'Editor',
    client: 'Cliente',
}[roleName] ?? roleName);

const groupLabel = (groupName) => ({
    Users: 'Usuarios',
    Roles: 'Roles',
    Lessons: 'Lecciones',
    Categories: 'Categorias',
}[groupName] ?? groupName);

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

const groupIsSelected = (permissions) =>
    permissions.every((permission) => props.form.permissions.includes(permission.name));

const toggleGroup = (permissions) => {
    const permissionNames = permissions.map((permission) => permission.name);

    if (groupIsSelected(permissions)) {
        props.form.permissions = props.form.permissions.filter((permission) => !permissionNames.includes(permission));

        return;
    }

    props.form.permissions = [...new Set([...props.form.permissions, ...permissionNames])];
};
</script>

<template>
    <form @submit.prevent="$emit('submit')" class="space-y-6">
        <section class="workspace-form-card rounded-[1.75rem] p-6 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-3xl">
                    <p class="workspace-kicker">Gestion de usuarios</p>
                    <h2 class="workspace-display mt-3 text-3xl leading-tight text-slate-900">
                        {{ updating ? 'Ajusta el acceso de este usuario sin perder control.' : 'Crea un acceso nuevo y dejalo listo para entrar.' }}
                    </h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        {{ updating ? 'Puedes cambiar sus datos, renovar contrasena y redefinir los roles o permisos adicionales desde un solo lugar.' : 'Completa los datos base, asigna un rol y agrega permisos directos solo si necesitas una excepcion puntual.' }}
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                    <span class="workspace-chip rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-600">
                        {{ updating ? 'Edicion' : 'Nuevo acceso' }}
                    </span>
                    <span class="rounded-full bg-teal-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-teal-700">
                        {{ effectivePermissionCount }} permisos efectivos
                    </span>
                </div>
            </div>

            <div class="mt-8 grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
                <div class="space-y-6">
                    <div class="workspace-panel rounded-[1.5rem] p-5 sm:p-6">
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <InputLabel for="first_name" value="Nombre" />
                                <TextInput
                                    id="first_name"
                                    v-model="form.first_name"
                                    type="text"
                                    autocomplete="given-name"
                                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                                />
                                <InputError :message="$page.props.errors.first_name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="last_name" value="Apellido" />
                                <TextInput
                                    id="last_name"
                                    v-model="form.last_name"
                                    type="text"
                                    autocomplete="family-name"
                                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                                />
                                <InputError :message="$page.props.errors.last_name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="phone" value="Telefono" />
                                <TextInput
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    autocomplete="tel"
                                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                                />
                                <InputError :message="$page.props.errors.phone" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Correo" />
                                <TextInput
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    autocomplete="email"
                                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                                />
                                <InputError :message="$page.props.errors.email" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="workspace-panel rounded-[1.5rem] p-5 sm:p-6">
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <InputLabel for="password" :value="updating ? 'Nueva contrasena' : 'Contrasena'" />
                                <TextInput
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    autocomplete="new-password"
                                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                                />
                                <p class="mt-3 text-sm leading-6 text-slate-500">
                                    {{ updating ? 'Deja este campo vacio si quieres conservar la contrasena actual.' : 'Usa una contrasena clara para el administrador y segura para el usuario final.' }}
                                </p>
                                <InputError :message="$page.props.errors.password" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="password_confirmation" value="Confirmar contrasena" />
                                <TextInput
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    autocomplete="new-password"
                                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="workspace-panel rounded-[1.5rem] p-5 sm:p-6">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="workspace-kicker">Roles</p>
                                <p class="mt-2 text-sm leading-6 text-slate-500">
                                    Asigna perfiles base para no tener que marcar permiso por permiso cuando no haga falta.
                                </p>
                            </div>

                            <span class="rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">
                                {{ selectedRoleCount }} seleccionados
                            </span>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <label
                                v-for="role in roles"
                                :key="role.name"
                                class="inline-flex items-start justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50/90 px-4 py-4 text-sm text-slate-700"
                            >
                                <div>
                                    <p class="font-semibold text-slate-900">{{ roleLabel(role.name) }}</p>
                                    <p class="mt-1 leading-6 text-slate-600">{{ role.description }}</p>
                                </div>
                                <Checkbox
                                    v-model:checked="form.roles"
                                    :value="role.name"
                                    class="mt-1 h-5 w-5 rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                                />
                            </label>
                        </div>

                        <InputError :message="$page.props.errors.roles" class="mt-2" />
                        <InputError :message="$page.props.errors['roles.0']" class="mt-2" />
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(permissions, groupName) in permissionGroups"
                            :key="groupName"
                            class="workspace-panel rounded-[1.5rem] p-5 sm:p-6"
                        >
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="workspace-kicker">{{ groupLabel(groupName) }}</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">
                                        Permisos adicionales para excepciones puntuales dentro de {{ groupLabel(groupName).toLowerCase() }}.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    @click="toggleGroup(permissions)"
                                    class="inline-flex items-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-teal-200 hover:text-teal-800"
                                >
                                    {{ groupIsSelected(permissions) ? 'Limpiar modulo' : 'Seleccionar modulo' }}
                                </button>
                            </div>

                            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                <label
                                    v-for="permission in permissions"
                                    :key="permission.name"
                                    class="inline-flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50/90 px-4 py-3 text-sm text-slate-700"
                                >
                                    <p class="font-semibold text-slate-900">{{ permissionLabel(permission.name) }}</p>
                                    <Checkbox
                                        v-model:checked="form.permissions"
                                        :value="permission.name"
                                        class="h-5 w-5 rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                                    />
                                </label>
                            </div>
                        </div>
                    </div>

                    <InputError :message="$page.props.errors.permissions" class="mt-2" />
                    <InputError :message="$page.props.errors['permissions.0']" class="mt-2" />
                </div>

                <aside class="space-y-4">
                    <article class="workspace-side-note rounded-[1.5rem] p-5 sm:p-6">
                        <p class="workspace-kicker">Resumen rapido</p>
                        <div class="mt-4 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Roles elegidos</span>
                                <span class="text-sm font-semibold text-slate-900">{{ selectedRoleCount }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Permisos directos</span>
                                <span class="text-sm font-semibold text-slate-900">{{ selectedPermissionCount }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Permisos efectivos</span>
                                <span class="text-sm font-semibold text-slate-900">{{ effectivePermissionCount }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Estado de contrasena</span>
                                <span class="text-sm font-semibold text-slate-900">{{ updating ? 'Opcional' : 'Obligatoria' }}</span>
                            </div>
                        </div>
                    </article>

                    <article class="workspace-side-note rounded-[1.5rem] p-5 sm:p-6">
                        <p class="workspace-kicker">Buenas practicas</p>
                        <div class="mt-4 space-y-4 text-sm leading-7 text-slate-600">
                            <p>Usa roles para definir el marco general y deja los permisos directos solo para casos especiales.</p>
                            <p>Si la persona necesitara soporte, el telefono te ayuda a tener una referencia rapida dentro del panel.</p>
                            <p>No compartas la cuenta admin: crea un acceso propio para cada profesor o colaborador.</p>
                        </div>
                    </article>
                </aside>
            </div>
        </section>

        <div class="flex flex-wrap items-center justify-end gap-3">
            <button
                type="submit"
                class="inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="form.processing"
            >
                {{ form.processing ? 'Guardando...' : updating ? 'Actualizar usuario' : 'Crear usuario' }}
            </button>
        </div>
    </form>
</template>
