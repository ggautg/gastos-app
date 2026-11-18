<script setup>
/** Encabezado común de las páginas: etiqueta, título y texto de apoyo. */
defineProps({
    eyebrow: String,
    title: { type: String, required: true },
    subtitle: String,
});
</script>

<template>
    <div class="space-y-1.5">
        <p v-if="eyebrow" class="cs-eyebrow">{{ eyebrow }}</p>
        <h2 class="cs-page-title">{{ title }}</h2>
        <p v-if="subtitle" class="text-sm cs-muted">{{ subtitle }}</p>
        <slot />
    </div>
</template>
