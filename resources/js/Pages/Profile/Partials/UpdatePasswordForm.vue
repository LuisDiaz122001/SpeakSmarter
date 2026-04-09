<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <FormSection @submitted="updatePassword">
        <template #title>
            Contrasena y acceso
        </template>

        <template #description>
            Refuerza la seguridad de tu cuenta con una contrasena nueva, clara y resistente.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="current_password" value="Contrasena actual" />
                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                    autocomplete="current-password"
                />
                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="password" value="Nueva contrasena" />
                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                    autocomplete="new-password"
                />
                <p class="mt-3 text-sm leading-6 text-slate-500">
                    Procura mezclar palabras faciles de recordar para ti con longitud suficiente para mantener la cuenta segura.
                </p>
                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="password_confirmation" value="Confirmar contrasena" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                    autocomplete="new-password"
                />
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3 text-sm text-emerald-600">
                Contrasena actualizada.
            </ActionMessage>

            <PrimaryButton class="rounded-full bg-slate-900 px-5 py-3 text-white hover:bg-slate-800 focus:ring-teal-500" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Guardar contrasena
            </PrimaryButton>
        </template>
    </FormSection>
</template>
