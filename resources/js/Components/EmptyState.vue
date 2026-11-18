<script setup>
/**
 * Estado vacío reutilizable. El slot sirve para el botón o enlace
 * que le da al usuario una acción concreta.
 */
defineProps({
    message: { type: String, required: true },
    detail: String,
});
</script>

<template>
    <div class="cs-empty text-center">
        <p>{{ message }}</p>
        <p v-if="detail" class="mt-1 text-sm cs-faint">{{ detail }}</p>
        <div v-if="$slots.default" class="mt-4"><slot /></div>
    </div>
</template>
