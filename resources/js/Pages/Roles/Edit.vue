<script>
export default {
    name: "RolesEdit",
}
</script>

<script setup>
import RoleForm from '@/Components/Roles/Form.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
    permissions: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.role.name,
    permissions: [...props.role.permissions],
});
</script>

<template>
    <AppLayout title="Edit Role">
        <template #header>
            <div class="flex flex-col gap-2">
                <p class="workspace-kicker">Roles</p>
                <h1 class="workspace-display text-3xl leading-tight text-slate-900">Edit role permissions without losing control</h1>
            </div>
        </template>

        <div class="workspace-shell py-8 sm:py-10">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
                <section
                    class="workspace-hero rounded-[2rem] px-6 py-8 text-white sm:px-8"
                    style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.98), rgba(51, 65, 85, 0.92));"
                >
                    <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-3xl">
                            <p class="text-xs uppercase tracking-[0.35em] text-white/70">Role Update</p>
                            <h2 class="workspace-display mt-3 text-4xl leading-tight sm:text-5xl">
                                Keep <span class="text-white/80">{{ props.role.name }}</span> aligned with the access matrix.
                            </h2>
                            <p class="mt-3 text-sm leading-7 text-white/78 sm:text-base">
                                Review permissions carefully so admins remain in full control while editors and clients stay within their intended scope.
                            </p>
                        </div>

                        <Link
                            :href="route('roles.index')"
                            class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20"
                        >
                            Back to roles
                        </Link>
                    </div>
                </section>

                <RoleForm
                    :updating="true"
                    :form="form"
                    :permission-groups="permissions"
                    @submit="form.put(route('roles.update', props.role.id))"
                />
            </div>
        </div>
    </AppLayout>
</template>
