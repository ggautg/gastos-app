<script setup>
defineProps({
    summary: Object,
    comparativa: Object,
});

function formatGs(amount) {
    return '₲ ' + new Intl.NumberFormat('es-PY').format(amount);
}

function diferencia(actual, anterior) {
    if (anterior <= 0) return null;
    return {
        valor: actual - anterior,
        porcentaje: Math.round(((actual - anterior) / anterior) * 100),
    };
}
</script>

<template>
    <div class="grid grid-cols-3 gap-3">
        <div class="rounded-xl border p-5" style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);">
            <p class="text-xs mb-2" style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">Ganancias</p>
            <p class="text-lg font-semibold" style="font-family: 'JetBrains Mono', monospace; color: #1D9E75;">
                {{ formatGs(summary.ganancias) }}
            </p>
            <p v-if="diferencia(summary.ganancias, comparativa.ganancias_anterior)" class="text-xs mt-1"
               :style="{ color: diferencia(summary.ganancias, comparativa.ganancias_anterior).valor >= 0 ? '#1D9E75' : '#D85A30' }">
                {{ diferencia(summary.ganancias, comparativa.ganancias_anterior).valor >= 0 ? '↑' : '↓' }}
                {{ Math.abs(diferencia(summary.ganancias, comparativa.ganancias_anterior).porcentaje) }}%
            </p>
        </div>
        <div class="rounded-xl border p-5" style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);">
            <p class="text-xs mb-2" style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">Gastos</p>
            <p class="text-lg font-semibold" style="font-family: 'JetBrains Mono', monospace; color: #D85A30;">
                {{ formatGs(summary.gastos) }}
            </p>
            <p v-if="diferencia(summary.gastos, comparativa.gastos_anterior)" class="text-xs mt-1"
               :style="{ color: diferencia(summary.gastos, comparativa.gastos_anterior).valor <= 0 ? '#1D9E75' : '#D85A30' }">
                {{ diferencia(summary.gastos, comparativa.gastos_anterior).valor >= 0 ? '↑' : '↓' }}
                {{ Math.abs(diferencia(summary.gastos, comparativa.gastos_anterior).porcentaje) }}%
            </p>
        </div>
        <div class="rounded-xl border p-5" style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);">
            <p class="text-xs mb-2" style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">Balance</p>
            <p class="text-lg font-semibold" style="font-family: 'JetBrains Mono', monospace;" :style="{ color: summary.balance >= 0 ? 'var(--cs-teal)' : '#D85A30' }">
                {{ formatGs(summary.balance) }}
            </p>
        </div>
    </div>
</template>