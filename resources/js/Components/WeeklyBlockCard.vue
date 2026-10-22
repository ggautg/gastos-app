<script setup>
defineProps({
    bloque: Object,
    bloqueMesAnterior: Object,
});

function formatGs(amount) {
    return '₲ ' + new Intl.NumberFormat('es-PY').format(amount);
}

function colorDiferencia(porcentaje) {
    if (porcentaje === null) return 'var(--cs-faint)';
    return porcentaje <= 0 ? 'var(--cs-emerald)' : 'var(--cs-terra)';
}
</script>

<template>
    <div class="cs-card p-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="cs-kpi-label mb-1">Días {{ bloque.etiqueta }}</p>
                <p class="cs-kpi-value">{{ formatGs(bloque.total) }}</p>
            </div>

            <div v-if="bloque.total_anterior !== null" class="text-right">
                <p v-if="bloque.porcentaje !== null" class="cs-kpi-delta justify-end text-sm"
                    :style="{ color: colorDiferencia(bloque.porcentaje) }">
                    {{ bloque.porcentaje >= 0 ? '↑' : '↓' }} {{ Math.abs(bloque.porcentaje) }}%
                </p>
                <p v-else class="text-sm font-medium" style="color: var(--cs-faint);">
                    Sin cambios
                </p>
                <p class="mt-0.5 text-xs" style="color: var(--cs-faint);">
                    semana anterior: {{ formatGs(bloque.total_anterior) }}
                </p>
            </div>
        </div>

        <div v-if="bloqueMesAnterior" class="mt-3 flex items-center justify-between border-t border-dashed pt-3 text-xs"
            style="border-color: var(--cs-line-strong); color: var(--cs-faint);">
            <span>Mismos días, mes anterior</span>
            <span class="flex items-center gap-2">
                <span v-if="bloque.porcentaje_vs_mes_anterior !== null"
                    :style="{ color: colorDiferencia(bloque.porcentaje_vs_mes_anterior) }">
                    {{ bloque.porcentaje_vs_mes_anterior >= 0 ? '↑' : '↓' }} {{
                        Math.abs(bloque.porcentaje_vs_mes_anterior) }}%
                </span>
                <span class="cs-amount">{{ formatGs(bloqueMesAnterior.total) }}</span>
            </span>
        </div>
    </div>
</template>
