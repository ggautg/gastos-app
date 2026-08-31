<script setup>
defineProps({
    presupuestos: Array,
});

function formatGs(amount) {
    return '₲ ' + new Intl.NumberFormat('es-PY').format(amount);
}

function colorBarra(porcentaje) {
    if (porcentaje >= 100) return '#D85A30';
    if (porcentaje >= 80) return '#C68A2E';
    return '#1D9E75';
}
</script>

<template>
    <div v-if="presupuestos.length > 0" class="rounded-xl border p-5 space-y-3" style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);">
        <p class="text-sm font-medium" style="color: color-mix(in srgb, var(--cs-ink) 70%, transparent);">Presupuestos del mes</p>
        <div v-for="p in presupuestos" :key="p.name">
            <div class="flex justify-between text-xs mb-1">
                <span class="font-medium" style="color: var(--cs-ink);">{{ p.name }}</span>
                <span style="color: color-mix(in srgb, var(--cs-ink) 50%, transparent);">
                    {{ formatGs(p.gastado) }} / {{ formatGs(p.budget) }}
                </span>
            </div>
            <div class="w-full h-2 rounded-full overflow-hidden" style="background: color-mix(in srgb, var(--cs-ink) 8%, transparent);">
                <div class="h-full rounded-full transition-all" :style="{ width: Math.min(p.porcentaje, 100) + '%', background: colorBarra(p.porcentaje) }"></div>
            </div>
            <p v-if="p.porcentaje >= 100" class="text-xs mt-1" style="color: #D85A30;">
                Te pasaste ₲{{ new Intl.NumberFormat('es-PY').format(p.gastado - p.budget) }}
            </p>
        </div>
    </div>
</template>