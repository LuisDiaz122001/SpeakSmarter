<script setup>
import Modal from './Modal.vue';

const emit = defineEmits(['close']);

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const close = () => {
    emit('close');
};
</script>

<template>
    <Modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div class="px-6 py-5">
            <div class="workspace-display text-2xl text-slate-900">
                <slot name="title" />
            </div>

            <div class="mt-4 text-sm leading-7 text-slate-600">
                <slot name="content" />
            </div>
        </div>

        <div class="workspace-modal-footer flex flex-col-reverse gap-3 px-6 py-4 text-end sm:flex-row sm:justify-end">
            <slot name="footer" />
        </div>
    </Modal>
</template>
