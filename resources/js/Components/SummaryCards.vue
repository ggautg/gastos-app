<script setup>
import { ArrowDownRight, ArrowUpRight } from 'lucide-vue-next';

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
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="cs-kpi">
            <p class="cs-kpi-label">Ganancias</p>
            <p class="cs-kpi-value" style="color: var(--cs-emerald);">
                {{ formatGs(summary.ganancias) }}
            </p>
            <p
                v-if="diferencia(summary.ganancias, comparativa.ganancias_anterior)"
                class="cs-kpi-delta"
                :style="{ color: diferencia(summary.ganancias, comparativa.ganancias_anterior).valor >= 0 ? 'var(--cs-emerald)' : 'var(--cs-terra)' }"
            >
                <ArrowUpRight
                    v-if="diferencia(summary.ganancias, comparativa.ganancias_anterior).valor >= 0"
                    class="h-3 w-3"
                />
                <ArrowDownRight v-else class="h-3 w-3" />
                {{ Math.abs(diferencia(summary.ganancias, comparativa.ganancias_anterior).porcentaje) }}%
                <span class="cs-faint font-sans font-normal">vs. mes anterior</span>
            </p>
        </div>

        <div class="cs-kpi">
            <p class="cs-kpi-label">Gastos</p>
            <p class="cs-kpi-value" style="color: var(--cs-terra);">
                {{ formatGs(summary.gastos) }}
            </p>
            <p
                v-if="diferencia(summary.gastos, comparativa.gastos_anterior)"
                class="cs-kpi-delta"
                :style="{ color: diferencia(summary.gastos, comparativa.gastos_anterior).valor <= 0 ? 'var(--cs-emerald)' : 'var(--cs-terra)' }"
            >
                <ArrowDownRight
                    v-if="diferencia(summary.gastos, comparativa.gastos_anterior).valor <= 0"
                    class="h-3 w-3"
                />
                <ArrowUpRight v-else class="h-3 w-3" />
                {{ Math.abs(diferencia(summary.gastos, comparativa.gastos_anterior).porcentaje) }}%
                <span class="cs-faint font-sans font-normal">vs. mes anterior</span>
            </p>
        </div>

        <div class="cs-kpi">
            <p class="cs-kpi-label">Balance</p>
            <p
                class="cs-kpi-value"
                :style="{ color: summary.balance >= 0 ? 'var(--cs-teal)' : 'var(--cs-terra)' }"
            >
                {{ formatGs(summary.balance) }}
            </p>
            <p class="cs-kpi-delta cs-faint">
                <span class="cs-stat-gold">₲</span> guaraníes
            </p>
        </div>
    </div>
</template>
