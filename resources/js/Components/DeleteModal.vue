<script setup>
import { useBackdropClose } from '@/Composables/useBackdropClose';

defineProps({
    show: Boolean,
    title: {
        type: String,
        default: '¿Eliminar?',
    },
    message: String,
    processing: Boolean,
});

const emit = defineEmits(['cancel', 'confirm']);

const { onPointerDown, onClick: onBackdropClick } = useBackdropClose(() => emit('cancel'));
</script>

<template>
    <div
        v-if="show"
        class="cs-overlay fixed inset-0 z-50 flex items-center justify-center px-4"
        @pointerdown="onPointerDown" @click="onBackdropClick"
    >
        <div class="cs-panel w-full max-w-sm p-6">
            <h3 class="cs-panel-title mb-2">
                {{ title }}
            </h3>
            <p class="mb-6 text-sm leading-relaxed" style="color: var(--cs-muted);">
                {{ message }}
            </p>
            <div class="flex justify-end gap-2">
                <button type="button" @click="emit('cancel')" class="cs-btn cs-btn-ghost">
                    Cancelar
                </button>
                <button
                    type="button"
                    @click="emit('confirm')"
                    :disabled="processing"
                    class="cs-btn cs-btn-danger"
                >
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</template>
