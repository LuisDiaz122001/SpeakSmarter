<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import ActionSection from '@/Components/ActionSection.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    sessions: Array,
});

const confirmingLogout = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmLogout = () => {
    confirmingLogout.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const logoutOtherBrowserSessions = () => {
    form.delete(route('other-browser-sessions.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingLogout.value = false;

    form.reset();
};
</script>

<template>
    <ActionSection>
        <template #title>
            Sesiones abiertas
        </template>

        <template #description>
            Revisa en que dispositivos esta abierta tu cuenta y cierra los accesos que ya no necesites.
        </template>

        <template #content>
            <div class="max-w-2xl text-sm leading-7 text-slate-600">
                Si notas actividad fuera de lugar o simplemente quieres limpiar accesos antiguos, desde aqui puedes cerrar las otras sesiones activas en navegadores y dispositivos distintos al actual.
            </div>

            <div v-if="sessions.length > 0" class="mt-5 space-y-6">
                <div v-for="(session, i) in sessions" :key="i" class="flex items-center rounded-[1.25rem] border border-slate-200 bg-slate-50/75 px-4 py-4">
                    <div>
                        <svg v-if="session.agent.is_desktop" class="size-8 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                        </svg>

                        <svg v-else class="size-8 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                    </div>

                    <div class="ms-3">
                        <div class="text-sm font-semibold text-slate-800">
                            {{ session.agent.platform ? session.agent.platform : 'Desconocido' }} - {{ session.agent.browser ? session.agent.browser : 'Desconocido' }}
                        </div>

                        <div>
                            <div class="text-xs uppercase tracking-[0.18em] text-slate-500">
                                {{ session.ip_address }},

                                <span v-if="session.is_current_device" class="font-semibold text-emerald-600">Este dispositivo</span>
                                <span v-else>Ultima actividad {{ session.last_active }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center mt-5">
                <PrimaryButton class="rounded-full bg-slate-900 px-5 py-3 text-white hover:bg-slate-800 focus:ring-teal-500" @click="confirmLogout">
                    Cerrar otras sesiones
                </PrimaryButton>

                <ActionMessage :on="form.recentlySuccessful" class="ms-3 text-sm text-emerald-600">
                    Sesiones cerradas.
                </ActionMessage>
            </div>

            <DialogModal :show="confirmingLogout" @close="closeModal">
                <template #title>
                    Confirmar cierre de sesiones
                </template>

                <template #content>
                    Escribe tu contrasena para confirmar que quieres cerrar las otras sesiones abiertas en tus dispositivos.

                    <div class="mt-4">
                        <TextInput
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="workspace-input mt-1 block w-full rounded-2xl px-4 py-3 sm:w-3/4"
                            placeholder="Contrasena"
                            autocomplete="current-password"
                            @keyup.enter="logoutOtherBrowserSessions"
                        />

                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                </template>

                <template #footer>
                    <SecondaryButton class="rounded-full px-4 py-2" @click="closeModal">
                        Cancelar
                    </SecondaryButton>

                    <PrimaryButton
                        class="ms-3 rounded-full bg-slate-900 px-4 py-2 text-white hover:bg-slate-800 focus:ring-teal-500"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="logoutOtherBrowserSessions"
                    >
                        Cerrar sesiones
                    </PrimaryButton>
                </template>
            </DialogModal>
        </template>
    </ActionSection>
</template>
