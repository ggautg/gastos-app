<script setup>
/** Confirmación de borrado. Usa el mismo shell accesible que los otros modales. */
import AppModal from './AppModal.vue';

defineProps({
    show: Boolean,
    title: {
        type: String,
        default: '¿Eliminar?',
    },
    message: String,
    processing: Boolean,
    confirmLabel: {
        type: String,
        default: 'Eliminar',
    },
});

const emit = defineEmits(['cancel', 'confirm']);
</script>

<template>
    <AppModal :show="show" :title="title" @close="emit('cancel')">
        <p class="mb-6 text-sm leading-relaxed" style="color: var(--cs-muted);">
            {{ message }}
        </p>
        <div class="flex justify-end gap-2">
            <button type="button" @click="emit('cancel')" class="cs-btn cs-btn-ghost">Cancelar</button>
            <button type="button" @click="emit('confirm')" :disabled="processing" class="cs-btn cs-btn-danger">
                {{ confirmLabel }}
            </button>
        </div>
    </AppModal>
</template>
