<script>
export default {
    name: "CategoryForm",
}
</script>

<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    form: {
        type: Object,
        required: true,
    },
    updating: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['submit']);
</script>

<template>
    <form @submit.prevent="$emit('submit')" class="space-y-6">
        <section class="workspace-form-card rounded-[1.75rem] p-6 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-2xl">
                    <p class="workspace-kicker">Category Editor</p>
                    <h2 class="workspace-display mt-3 text-3xl leading-tight text-slate-900">
                        {{ updating ? 'Refine the category label without losing clarity.' : 'Create a category that keeps lessons easy to navigate.' }}
                    </h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        {{ updating ? 'Adjust the name when the content map evolves, while keeping it descriptive for teachers and students.' : 'Short and descriptive names create a stronger teaching map and make the library easier to scan.' }}
                    </p>
                </div>

                <span class="workspace-chip rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-600">
                    {{ updating ? 'Editing mode' : 'New category' }}
                </span>
            </div>

            <div class="mt-8 grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
                <div class="workspace-panel rounded-[1.5rem] p-5 sm:p-6">
                    <InputLabel for="name" value="Category name" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        autocomplete="name"
                        class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                    />
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        Use a name that groups a family of lessons, such as Grammar, Listening or Vocabulary.
                    </p>
                    <InputError :message="$page.props.errors.name" class="mt-2" />
                </div>

                <aside class="workspace-side-note rounded-[1.5rem] p-5 sm:p-6">
                    <p class="workspace-kicker">Naming Guide</p>
                    <div class="mt-4 space-y-4 text-sm leading-7 text-slate-600">
                        <p>Keep the label short so it fits naturally across cards and selectors.</p>
                        <p>Avoid duplicate categories that split similar lessons into confusing branches.</p>
                        <p>Prefer teaching language over internal jargon so everyone understands the structure.</p>
                    </div>
                </aside>
            </div>
        </section>

        <div class="flex flex-wrap items-center justify-end gap-3">
            <button
                type="submit"
                class="inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="form.processing"
            >
                {{ form.processing ? 'Saving...' : updating ? 'Update category' : 'Create category' }}
            </button>
        </div>
    </form>
</template>
