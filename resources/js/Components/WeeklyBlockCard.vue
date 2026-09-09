<script setup>
defineProps({
    bloque: Object,
    bloqueMesAnterior: Object,
});

function formatGs(amount) {
    return '₲ ' + new Intl.NumberFormat('es-PY').format(amount);
}

function colorDiferencia(porcentaje) {
    if (porcentaje === null) return 'color-mix(in srgb, var(--cs-ink) 40%, transparent)';
    return porcentaje <= 0 ? '#1D9E75' : '#D85A30';
}
</script>

<template>
    <div class="rounded-xl border p-5" style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs mb-1" style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">
                    Días {{ bloque.etiqueta }}
                </p>
                <p class="text-lg font-semibold" style="font-family: 'JetBrains Mono', monospace; color: var(--cs-ink);">
                    {{ formatGs(bloque.total) }}
                </p>
            </div>

            <div v-if="bloque.total_anterior !== null" class="text-right">
                <p v-if="bloque.porcentaje !== null" class="text-sm font-medium" :style="{ color: colorDiferencia(bloque.porcentaje) }">
                    {{ bloque.porcentaje >= 0 ? '↑' : '↓' }} {{ Math.abs(bloque.porcentaje) }}%
                </p>
                <p v-else class="text-sm font-medium" style="color: color-mix(in srgb, var(--cs-ink) 45%, transparent);">
                    Sin cambios
                </p>
                <p class="text-xs" style="color: color-mix(in srgb, var(--cs-ink) 45%, transparent);">
                    semana anterior: {{ formatGs(bloque.total_anterior) }}
                </p>
            </div>
        </div>

        <div
            v-if="bloqueMesAnterior"
            class="mt-3 pt-3 flex justify-between text-xs"
            style="border-top: 1px dashed color-mix(in srgb, var(--cs-ink) 12%, transparent); color: color-mix(in srgb, var(--cs-ink) 50%, transparent);"
        >
            <span>Mismos días, mes anterior</span>
            <span style="font-family: 'JetBrains Mono', monospace;">{{ formatGs(bloqueMesAnterior.total) }}</span>
        </div>
    </div>
</template>