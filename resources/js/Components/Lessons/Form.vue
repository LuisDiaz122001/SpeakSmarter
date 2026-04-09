<script>
export default {
    name: "LessonForm",
}
</script>

<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import CollectionSelector from '@/Common/CollectionSelector.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { computed } from 'vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    updating: {
        type: Boolean,
        default: false,
    },
    categories: {
        type: Array,
        required: true,
    },
    levels: {
        type: Array,
        required: true,
    },
});

defineEmits(['submit']);

const selectedCategoryCount = computed(() => props.form.category_ids?.length ?? 0);
const attachedResources = computed(() =>
    [props.form.image_uri, props.form.content_uri, props.form.pdf_uri].filter(Boolean).length,
);
const priceHint = computed(() => props.form.is_free ? 'This lesson will be shown as free in the public catalog.' : 'Add the public price so visitors can compare lessons before signing in.');
</script>

<template>
    <form @submit.prevent="$emit('submit')" class="space-y-6">
        <section class="workspace-form-card rounded-[1.75rem] p-6 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-3xl">
                    <p class="workspace-kicker">Lesson Editor</p>
                    <h2 class="workspace-display mt-3 text-3xl leading-tight text-slate-900">
                        {{ updating ? 'Refine the lesson so it stays ready for class.' : 'Create a lesson that feels structured from the start.' }}
                    </h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        {{ updating ? 'Update the lesson details, polish its resources and keep its teaching context aligned.' : 'Capture the learning goal, attach the right resources and connect the lesson to level and categories.' }}
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                    <span class="workspace-chip rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-600">
                        {{ updating ? 'Editing mode' : 'New lesson' }}
                    </span>
                    <span class="rounded-full bg-amber-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">
                        {{ selectedCategoryCount }} categories linked
                    </span>
                </div>
            </div>

            <div class="mt-8 grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
                <div class="space-y-6">
                    <div class="workspace-panel rounded-[1.5rem] p-5 sm:p-6">
                        <div class="grid gap-5 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <InputLabel for="name" value="Lesson name" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    autocomplete="name"
                                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                                />
                                <InputError :message="$page.props.errors.name" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <InputLabel for="description" value="Description" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="5"
                                    class="workspace-textarea mt-2 block w-full rounded-2xl px-4 py-3 text-sm shadow-sm"
                                />
                                <p class="mt-3 text-sm leading-6 text-slate-500">
                                    Summarize the learning goal, tone and how this lesson should be used.
                                </p>
                                <InputError :message="$page.props.errors.description" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="image_uri" value="Image URI" />
                                <TextInput
                                    id="image_uri"
                                    v-model="form.image_uri"
                                    type="text"
                                    autocomplete="image_uri"
                                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                                />
                                <InputError :message="$page.props.errors.image_uri" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="content_uri" value="Content URI" />
                                <TextInput
                                    id="content_uri"
                                    v-model="form.content_uri"
                                    type="text"
                                    autocomplete="content_uri"
                                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                                />
                                <InputError :message="$page.props.errors.content_uri" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="pdf_uri" value="PDF URI" />
                                <TextInput
                                    id="pdf_uri"
                                    v-model="form.pdf_uri"
                                    type="text"
                                    autocomplete="pdf_uri"
                                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                                />
                                <InputError :message="$page.props.errors.pdf_uri" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="price" value="Public price" />
                                <TextInput
                                    id="price"
                                    v-model="form.price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    autocomplete="off"
                                    class="workspace-input mt-2 block w-full rounded-2xl px-4 py-3"
                                />
                                <p class="mt-3 text-sm leading-6 text-slate-500">
                                    {{ priceHint }}
                                </p>
                                <InputError :message="$page.props.errors.price" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="level_id" value="Level" />
                                <select
                                    id="level_id"
                                    v-model="form.level_id"
                                    class="workspace-select mt-2 block w-full rounded-2xl px-4 py-3 text-sm shadow-sm"
                                >
                                    <option value="">Select a level</option>
                                    <option v-for="level in levels" :key="level.id" :value="level.id">{{ level.name }}</option>
                                </select>
                                <InputError :message="$page.props.errors.level_id" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="workspace-panel rounded-[1.5rem] p-5 sm:p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <InputLabel for="is_free" value="Access Model" />
                                <p class="mt-2 text-sm leading-6 text-slate-500">
                                    Decide whether this lesson should be visible as free content.
                                </p>
                            </div>

                            <label class="inline-flex items-center gap-3 rounded-full bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                                <Checkbox id="is_free" v-model:checked="form.is_free" />
                                <span>This lesson is free</span>
                            </label>
                        </div>
                        <InputError :message="$page.props.errors.is_free" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="categories" value="Categories" />
                        <CollectionSelector
                            id="categories"
                            v-model="form.category_ids"
                            :collection="categories"
                            class="mt-2"
                        />
                        <InputError :message="$page.props.errors.category_ids" class="mt-2" />
                        <InputError :message="$page.props.errors['category_ids.0']" class="mt-2" />
                    </div>
                </div>

                <aside class="space-y-4">
                    <article class="workspace-side-note rounded-[1.5rem] p-5 sm:p-6">
                        <p class="workspace-kicker">Structure Snapshot</p>
                        <div class="mt-4 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Level assigned</span>
                                <span class="text-sm font-semibold text-slate-900">{{ form.level_id ? 'Yes' : 'Pending' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Categories linked</span>
                                <span class="text-sm font-semibold text-slate-900">{{ selectedCategoryCount }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Public price</span>
                                <span class="text-sm font-semibold text-slate-900">{{ form.is_free ? 'Free' : form.price || 'Pending' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Resources attached</span>
                                <span class="text-sm font-semibold text-slate-900">{{ attachedResources }}</span>
                            </div>
                        </div>
                    </article>

                    <article class="workspace-side-note rounded-[1.5rem] p-5 sm:p-6">
                        <p class="workspace-kicker">Teaching Notes</p>
                        <div class="mt-4 space-y-4 text-sm leading-7 text-slate-600">
                            <p>Pair a level with at least one category so the lesson is easier to place in the curriculum.</p>
                            <p>Keep the description practical and outcome-oriented instead of writing a long generic summary.</p>
                            <p>Only attach resource links that are ready to be used in class or review.</p>
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
                {{ form.processing ? 'Saving...' : updating ? 'Update lesson' : 'Create lesson' }}
            </button>
        </div>
    </form>
</template>
