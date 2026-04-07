<script>
    export default {
        name: "CategoryForm",
        }
</script>

<script setup>

    import { useForm } from '@inertiajs/vue3';
    import FormSection from '@/Components/FormSection.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';


    defineProps({
        form: {
            type: Object,
            required: true
        },
        updating: {
            type: Boolean,
            default: false,
            required: false
        }
    });

    defineEmits(['submit']);
</script>

<template>
    <FormSection @submitted="$emit('submit')">
        <template #title>
            {{ updating ? 'Update Category' : 'Create New Category' }}
        </template>
        <template #description>
            {{ updating ? 'Update the Selected Category.' : 'Create a New Category From scratch' }}
        </template>
        <template #form>
            <div class="col-span-6 sm:col-span-6">
                <InputLabel for="name" value="Name" />
                <TextInput id="name" v-model="form.name" type="text" autocomplete="name" class="mt-1 block w-full" />
                <InputError :message="$page.props.errors.name" class="mt-2" />
            </div>
        </template>
        <template #actions>
            <PrimaryButton :disabled="form.processing">
                {{ updating ? 'Update' : 'Create ' }}
            </PrimaryButton>
        </template>
    </FormSection>
</template>