<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <ActionSection>
        <template #title>
            Zona delicada
        </template>

        <template #description>
            Elimina la cuenta solo si de verdad ya no la vas a usar.
        </template>

        <template #content>
            <div class="max-w-2xl rounded-[1.25rem] border border-red-100 bg-red-50/70 px-5 py-5 text-sm leading-7 text-red-900">
                Al eliminar tu cuenta se borraran de forma permanente tus datos, sesiones y accesos asociados. Antes de continuar, asegurate de conservar cualquier informacion que quieras guardar.
            </div>

            <div class="mt-5">
                <DangerButton class="rounded-full px-5 py-3" @click="confirmUserDeletion">
                    Eliminar cuenta
                </DangerButton>
            </div>

            <DialogModal :show="confirmingUserDeletion" @close="closeModal">
                <template #title>
                    Confirmar eliminacion
                </template>

                <template #content>
                    Esta accion no se puede deshacer. Escribe tu contrasena actual para confirmar que quieres eliminar esta cuenta de forma permanente.

                    <div class="mt-4">
                        <TextInput
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="workspace-input mt-1 block w-full rounded-2xl px-4 py-3 sm:w-3/4"
                            placeholder="Contrasena"
                            autocomplete="current-password"
                            @keyup.enter="deleteUser"
                        />

                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                </template>

                <template #footer>
                    <SecondaryButton class="rounded-full px-4 py-2" @click="closeModal">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton
                        class="ms-3 rounded-full px-4 py-2"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Eliminar cuenta
                    </DangerButton>
                </template>
            </DialogModal>
        </template>
    </ActionSection>
</template>
