<script>
// Contador de módulo: cada modal montado necesita un id único para aria-labelledby.
let consecutivo = 0;
</script>

<script setup>
/**
 * Shell único de los modales: fondo, panel, título, cierre con Escape,
 * foco inicial, ciclo de foco con Tab y bloqueo del scroll de fondo.
 * Los formularios de cada modal se pasan por slot.
 */
import { nextTick, onBeforeUnmount, ref, watch } from 'vue';
import { useBackdropClose } from '@/Composables/useBackdropClose';

const props = defineProps({
    show: Boolean,
    title: { type: String, required: true },
    size: { type: String, default: 'sm' }, // 'sm' | 'md'
    closeOnBackdrop: { type: Boolean, default: true },
});

const emit = defineEmits(['close']);

const titleId = `cs-modal-title-${++consecutivo}`;
const panel = ref(null);

let focoAnterior = null;
let overflowAnterior = '';

const { onPointerDown, onClick: onBackdropClick } = useBackdropClose(() => {
    if (props.closeOnBackdrop) emit('close');
});

function elementosEnfocables() {
    if (!panel.value) return [];
    const selector = 'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
    return [...panel.value.querySelectorAll(selector)].filter((el) => el.offsetParent !== null);
}

function onKeydown(event) {
    if (event.key === 'Escape') {
        event.stopPropagation();
        emit('close');
        return;
    }

    if (event.key !== 'Tab') return;

    const enfocables = elementosEnfocables();
    if (!enfocables.length) return;

    const primero = enfocables[0];
    const ultimo = enfocables[enfocables.length - 1];

    if (event.shiftKey && document.activeElement === primero) {
        event.preventDefault();
        ultimo.focus();
    } else if (!event.shiftKey && document.activeElement === ultimo) {
        event.preventDefault();
        primero.focus();
    }
}

watch(() => props.show, async (abierto) => {
    if (abierto) {
        focoAnterior = document.activeElement;
        overflowAnterior = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        await nextTick();
        elementosEnfocables()[0]?.focus();
    } else {
        document.body.style.overflow = overflowAnterior;
        focoAnterior?.focus?.();
        focoAnterior = null;
    }
});

onBeforeUnmount(() => {
    document.body.style.overflow = overflowAnterior;
});
</script>

<template>
    <div v-if="show" class="cs-overlay fixed inset-0 z-50 flex items-center justify-center px-4" role="dialog"
        aria-modal="true" :aria-labelledby="titleId" @pointerdown="onPointerDown" @click="onBackdropClick"
        @keydown="onKeydown">
        <div ref="panel" class="cs-panel w-full p-6" :class="size === 'md' ? 'max-w-md' : 'max-w-sm'">
            <h3 :id="titleId" class="cs-panel-title mb-4">{{ title }}</h3>
            <slot />
        </div>
    </div>
</template>
