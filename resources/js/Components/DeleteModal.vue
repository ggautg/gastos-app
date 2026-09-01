<script setup>
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
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 bg-black/30 flex items-center justify-center px-4 z-50"
        @click.self="emit('cancel')"
    >
        <div class="rounded-xl shadow-lg p-6 w-full max-w-sm" style="background: var(--cs-paper-card);">
            <h3 class="font-cs-display text-lg font-semibold mb-2" style="color: var(--cs-ink);">
                {{ title }}
            </h3>
            <p class="text-sm mb-5" style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">
                {{ message }}
            </p>
            <div class="flex justify-end gap-2">
                <button type="button" @click="emit('cancel')" class="px-4 py-2 text-sm" style="color: color-mix(in srgb, var(--cs-ink) 60%, transparent);">
                    Cancelar
                </button>
                <button
                    type="button"
                    @click="emit('confirm')"
                    :disabled="processing"
                    class="px-4 py-2 text-sm font-medium text-white rounded-lg disabled:opacity-50"
                    style="background: #D85A30;"
                >
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</template>