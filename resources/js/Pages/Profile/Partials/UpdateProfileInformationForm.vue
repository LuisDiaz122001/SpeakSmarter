<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    user: Object,
});

const nameParts = String(props.user.name ?? '').split(' ').filter(Boolean);
const fallbackFirstName = props.user.first_name ?? nameParts[0] ?? '';
const fallbackLastName = props.user.last_name ?? nameParts.slice(1).join(' ');

const form = useForm({
    _method: 'PUT',
    first_name: fallbackFirstName,
    last_name: fallbackLastName,
    phone: props.user.phone ?? '',
    email: props.user.email,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.transform((data) => ({
        ...data,
        name: `${data.first_name} ${data.last_name}`.trim(),
    }));

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (! photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <FormSection @submitted="updateProfileInformation">
        <template #title>
            Identidad y contacto
        </template>

        <template #description>
            Actualiza la informacion principal de tu cuenta y manten visible la forma correcta de contactarte.
        </template>

        <template #form>
            <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">
                <input
                    id="photo"
                    ref="photoInput"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                >

                <InputLabel for="photo" value="Foto de perfil" />

                <div class="mt-3 flex items-center gap-4 rounded-[1.25rem] border border-slate-200 bg-slate-50/80 px-4 py-4">
                    <div v-show="! photoPreview" class="shrink-0">
                        <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full size-20 object-cover ring-4 ring-white shadow-md">
                    </div>

                    <div v-show="photoPreview" class="shrink-0">
                        <span
                            class="block rounded-full size-20 bg-cover bg-no-repeat bg-center ring-4 ring-white shadow-md"
                            :style="'background-image: url(\'' + photoPreview + '\');'"
                        />
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-slate-900">Imagen visible en tu cuenta</p>
                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Usa una foto limpia y cercana para que tu perfil se vea mas personal y reconocible.
                        </p>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-3">
                    <SecondaryButton class="rounded-full border-slate-200 px-4 py-2 text-slate-700" type="button" @click.prevent="selectNewPhoto">
                        Cambiar foto
                    </SecondaryButton>

                    <SecondaryButton
                        v-if="user.profile_photo_path"
                        type="button"
                        class="rounded-full border-slate-200 px-4 py-2 text-slate-700"
                        @click.prevent="deletePhoto"
                    >
                        Quitar foto
                    </SecondaryButton>
                </div>

                <InputError :message="form.errors.photo" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <InputLabel for="first_name" value="Nombre" />
                <TextInput
                    id="first_name"
                    v-model="form.first_name"
                    type="text"
                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                    required
                    autocomplete="given-name"
                />
                <InputError :message="form.errors.first_name" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <InputLabel for="last_name" value="Apellido" />
                <TextInput
                    id="last_name"
                    v-model="form.last_name"
                    type="text"
                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                    required
                    autocomplete="family-name"
                />
                <InputError :message="form.errors.last_name" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <InputLabel for="phone" value="Telefono" />
                <TextInput
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                    autocomplete="tel"
                />
                <p class="mt-3 text-sm leading-6 text-slate-500">
                    Opcional, pero util si usas la plataforma con soporte humano o seguimiento comercial.
                </p>
                <InputError :message="form.errors.phone" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <InputLabel for="email" value="Correo" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                    required
                    autocomplete="username"
                />
                <InputError :message="form.errors.email" class="mt-2" />

                <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        Tu correo aun no esta verificado.

                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="font-semibold text-teal-700 underline underline-offset-4 hover:text-teal-800 focus:outline-none"
                            @click.prevent="sendEmailVerification"
                        >
                            Reenviar correo de verificacion
                        </Link>
                    </p>

                    <div v-show="verificationLinkSent" class="mt-2 font-medium text-sm text-emerald-600">
                        Te enviamos un nuevo enlace de verificacion.
                    </div>
                </div>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3 text-sm text-emerald-600">
                Cambios guardados.
            </ActionMessage>

            <PrimaryButton class="rounded-full bg-slate-900 px-5 py-3 text-white hover:bg-slate-800 focus:ring-teal-500" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Guardar perfil
            </PrimaryButton>
        </template>
    </FormSection>
</template>
