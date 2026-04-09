<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import ConfirmsPassword from '@/Components/ConfirmsPassword.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    requiresConfirmation: Boolean,
});

const page = usePage();
const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);
const qrCode = ref(null);
const setupKey = ref(null);
const recoveryCodes = ref([]);

const confirmationForm = useForm({
    code: '',
});

const twoFactorEnabled = computed(
    () => ! enabling.value && page.props.auth.user?.two_factor_enabled,
);

watch(twoFactorEnabled, () => {
    if (! twoFactorEnabled.value) {
        confirmationForm.reset();
        confirmationForm.clearErrors();
    }
});

const enableTwoFactorAuthentication = () => {
    enabling.value = true;

    router.post(route('two-factor.enable'), {}, {
        preserveScroll: true,
        onSuccess: () => Promise.all([
            showQrCode(),
            showSetupKey(),
            showRecoveryCodes(),
        ]),
        onFinish: () => {
            enabling.value = false;
            confirming.value = props.requiresConfirmation;
        },
    });
};

const showQrCode = () => {
    return axios.get(route('two-factor.qr-code')).then(response => {
        qrCode.value = response.data.svg;
    });
};

const showSetupKey = () => {
    return axios.get(route('two-factor.secret-key')).then(response => {
        setupKey.value = response.data.secretKey;
    });
}

const showRecoveryCodes = () => {
    return axios.get(route('two-factor.recovery-codes')).then(response => {
        recoveryCodes.value = response.data;
    });
};

const confirmTwoFactorAuthentication = () => {
    confirmationForm.post(route('two-factor.confirm'), {
        errorBag: "confirmTwoFactorAuthentication",
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            confirming.value = false;
            qrCode.value = null;
            setupKey.value = null;
        },
    });
};

const regenerateRecoveryCodes = () => {
    axios
        .post(route('two-factor.recovery-codes'))
        .then(() => showRecoveryCodes());
};

const disableTwoFactorAuthentication = () => {
    disabling.value = true;

    router.delete(route('two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            disabling.value = false;
            confirming.value = false;
        },
    });
};
</script>

<template>
    <ActionSection>
        <template #title>
            Doble factor
        </template>

        <template #description>
            Agrega una capa extra de seguridad para que el acceso a tu cuenta sea mucho mas confiable.
        </template>

        <template #content>
            <h3 v-if="twoFactorEnabled && ! confirming" class="workspace-display text-2xl text-slate-900">
                El doble factor ya esta activo.
            </h3>

            <h3 v-else-if="twoFactorEnabled && confirming" class="workspace-display text-2xl text-slate-900">
                Falta confirmar la activacion.
            </h3>

            <h3 v-else class="workspace-display text-2xl text-slate-900">
                Aun no has activado el doble factor.
            </h3>

            <div class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                <p>
                    Cuando esta opcion esta activa, ademas de tu contrasena se te pedira un codigo temporal generado desde tu aplicacion autenticadora.
                </p>
            </div>

            <div v-if="twoFactorEnabled">
                <div v-if="qrCode">
                    <div class="mt-4 max-w-2xl text-sm leading-7 text-slate-600">
                        <p v-if="confirming" class="font-semibold">
                            Escanea este codigo QR con tu aplicacion autenticadora o usa la clave manual para terminar la activacion.
                        </p>

                        <p v-else>
                            El doble factor esta listo. Puedes volver a escanear este codigo o revisar la clave manual cuando lo necesites.
                        </p>
                    </div>

                    <div class="mt-4 inline-block rounded-[1.25rem] border border-slate-200 bg-white p-4 shadow-sm" v-html="qrCode" />

                    <div v-if="setupKey" class="mt-4 max-w-xl rounded-[1.1rem] bg-slate-50 px-4 py-4 text-sm text-slate-700">
                        <p class="font-semibold">
                            Clave manual: <span v-html="setupKey"></span>
                        </p>
                    </div>

                    <div v-if="confirming" class="mt-4">
                        <InputLabel for="code" value="Codigo" />

                        <TextInput
                            id="code"
                            v-model="confirmationForm.code"
                            type="text"
                            name="code"
                            class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3 sm:w-1/2"
                            inputmode="numeric"
                            autofocus
                            autocomplete="one-time-code"
                            @keyup.enter="confirmTwoFactorAuthentication"
                        />

                        <InputError :message="confirmationForm.errors.code" class="mt-2" />
                    </div>
                </div>

                <div v-if="recoveryCodes.length > 0 && ! confirming">
                    <div class="mt-4 max-w-2xl text-sm leading-7 text-slate-600">
                        <p class="font-semibold">
                            Guarda estos codigos en un lugar seguro. Te serviran para recuperar acceso si pierdes tu dispositivo autenticador.
                        </p>
                    </div>

                    <div class="mt-4 grid max-w-xl gap-1 rounded-[1.25rem] bg-slate-100 px-4 py-4 font-mono text-sm text-slate-700">
                        <div v-for="code in recoveryCodes" :key="code">
                            {{ code }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <div v-if="! twoFactorEnabled">
                    <ConfirmsPassword @confirmed="enableTwoFactorAuthentication">
                        <PrimaryButton type="button" class="rounded-full bg-slate-900 px-5 py-3 text-white hover:bg-slate-800 focus:ring-teal-500" :class="{ 'opacity-25': enabling }" :disabled="enabling">
                            Activar
                        </PrimaryButton>
                    </ConfirmsPassword>
                </div>

                <div v-else>
                    <ConfirmsPassword @confirmed="confirmTwoFactorAuthentication">
                        <PrimaryButton
                            v-if="confirming"
                            type="button"
                            class="me-3 rounded-full bg-slate-900 px-5 py-3 text-white hover:bg-slate-800 focus:ring-teal-500"
                            :class="{ 'opacity-25': enabling || confirmationForm.processing }"
                            :disabled="enabling || confirmationForm.processing"
                        >
                            Confirmar
                        </PrimaryButton>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="regenerateRecoveryCodes">
                        <SecondaryButton
                            v-if="recoveryCodes.length > 0 && ! confirming"
                            class="me-3 rounded-full px-4 py-2"
                        >
                            Regenerar codigos
                        </SecondaryButton>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="showRecoveryCodes">
                        <SecondaryButton
                            v-if="recoveryCodes.length === 0 && ! confirming"
                            class="me-3 rounded-full px-4 py-2"
                        >
                            Ver codigos
                        </SecondaryButton>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
                        <SecondaryButton
                            v-if="confirming"
                            class="rounded-full px-4 py-2"
                            :class="{ 'opacity-25': disabling }"
                            :disabled="disabling"
                        >
                            Cancelar
                        </SecondaryButton>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
                        <DangerButton
                            v-if="! confirming"
                            class="rounded-full px-4 py-2"
                            :class="{ 'opacity-25': disabling }"
                            :disabled="disabling"
                        >
                            Desactivar
                        </DangerButton>
                    </ConfirmsPassword>
                </div>
            </div>
        </template>
    </ActionSection>
</template>
