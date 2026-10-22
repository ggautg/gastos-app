<script setup>
defineProps({
    presupuestos: Array,
});

function formatGs(amount) {
    return '₲ ' + new Intl.NumberFormat('es-PY').format(amount);
}

function colorBarra(porcentaje) {
    if (porcentaje >= 100) return 'var(--cs-terra)';
    if (porcentaje >= 80) return 'var(--cs-gold)';
    return 'var(--cs-emerald)';
}
</script>

<template>
    <div v-if="presupuestos.length > 0" class="cs-card space-y-4 p-5">
        <div class="flex items-center justify-between gap-3">
            <p class="cs-section-title">Presupuestos del mes</p>
            <span class="cs-badge" style="background: color-mix(in srgb, var(--cs-gold) 14%, transparent); color: var(--cs-gold);">
                {{ presupuestos.length }} activos
            </span>
        </div>

        <div v-for="p in presupuestos" :key="p.name">
            <div class="mb-1.5 flex items-baseline justify-between gap-3 text-xs">
                <span class="font-semibold" style="color: var(--cs-ink);">{{ p.name }}</span>
                <span class="cs-amount" style="color: var(--cs-faint); font-weight: 500;">
                    {{ formatGs(p.gastado) }} <span class="opacity-60">/</span> {{ formatGs(p.budget) }}
                </span>
            </div>
            <div class="cs-progress" :style="{ color: colorBarra(p.porcentaje) }">
                <div
                    class="cs-progress-bar"
                    :style="{ width: Math.min(p.porcentaje, 100) + '%', background: colorBarra(p.porcentaje) }"
                ></div>
            </div>
            <div class="mt-1 flex items-center justify-between gap-3 text-xs">
                <p v-if="p.porcentaje >= 100" style="color: var(--cs-terra);">
                    Te pasaste ₲{{ new Intl.NumberFormat('es-PY').format(p.gastado - p.budget) }}
                </p>
                <p v-else class="cs-faint">Disponible ₲{{ new Intl.NumberFormat('es-PY').format(p.budget - p.gastado) }}</p>
                <span class="cs-amount" :style="{ color: colorBarra(p.porcentaje) }">{{ p.porcentaje }}%</span>
            </div>
        </div>
    </div>
</template>
