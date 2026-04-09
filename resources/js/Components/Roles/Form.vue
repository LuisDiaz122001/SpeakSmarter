<script>
export default {
    name: "RoleForm",
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
    permissionGroups: {
        type: Object,
        required: true,
    },
    updating: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['submit']);

const selectedPermissionsCount = computed(() => props.form.permissions.length);
const groupCount = computed(() => Object.keys(props.permissionGroups).length);

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
                    <p class="workspace-kicker">Role Editor</p>
                    <h2 class="workspace-display mt-3 text-3xl leading-tight text-slate-900">
                        {{ updating ? 'Adjust the role without blurring its purpose.' : 'Create a role with a clear permission boundary.' }}
                    </h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        {{ updating ? 'Keep the role focused so the access model stays understandable for admins, editors and clients.' : 'Good roles are intentional. Give each one a small, coherent set of abilities instead of broad access by accident.' }}
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                    <span class="workspace-chip rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-600">
                        {{ selectedPermissionsCount }} permissions
                    </span>
                    <span class="rounded-full bg-teal-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-teal-700">
                        {{ groupCount }} modules
                    </span>
                </div>
            </div>

            <div class="mt-8 grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
                <div class="space-y-6">
                    <div class="workspace-panel rounded-[1.5rem] p-5 sm:p-6">
                        <InputLabel for="name" value="Role name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            autocomplete="name"
                            class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                        />
                        <p class="mt-3 text-sm leading-6 text-slate-500">
                            Examples: admin, editor, client or any custom profile your workflow needs.
                        </p>
                        <InputError :message="$page.props.errors.name" class="mt-2" />
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(permissions, groupName) in permissionGroups"
                            :key="groupName"
                            class="workspace-panel rounded-[1.5rem] p-5 sm:p-6"
                        >
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="workspace-kicker">{{ groupName }}</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">
                                        Choose how much access this role should have inside {{ groupName.toLowerCase() }}.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    @click="toggleGroup(permissions)"
                                    class="inline-flex items-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-teal-200 hover:text-teal-800"
                                >
                                    {{ groupIsSelected(permissions) ? 'Clear module' : 'Select module' }}
                                </button>
                            </div>

                            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                <label
                                    v-for="permission in permissions"
                                    :key="permission.name"
                                    class="inline-flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50/90 px-4 py-3 text-sm text-slate-700"
                                >
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ permission.label }}</p>
                                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ permission.name }}</p>
                                    </div>
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
                        <p class="workspace-kicker">Access Guide</p>
                        <div class="mt-4 space-y-4 text-sm leading-7 text-slate-600">
                            <p><span class="font-semibold text-slate-900">Admin</span> should keep full system visibility and management access.</p>
                            <p><span class="font-semibold text-slate-900">Editor</span> should focus on lessons and categories, not role management.</p>
                            <p><span class="font-semibold text-slate-900">Client</span> should usually stay in read-only mode unless there is a strong business need.</p>
                        </div>
                    </article>

                    <article class="workspace-side-note rounded-[1.5rem] p-5 sm:p-6">
                        <p class="workspace-kicker">Selection Summary</p>
                        <div class="mt-4 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Role mode</span>
                                <span class="text-sm font-semibold text-slate-900">{{ updating ? 'Editing' : 'Creating' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Permissions selected</span>
                                <span class="text-sm font-semibold text-slate-900">{{ selectedPermissionsCount }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Modules available</span>
                                <span class="text-sm font-semibold text-slate-900">{{ groupCount }}</span>
                            </div>
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
                {{ form.processing ? 'Saving...' : updating ? 'Update role' : 'Create role' }}
            </button>
        </div>
    </form>
</template>
