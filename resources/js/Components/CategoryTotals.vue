<script setup>
defineProps({
    gastosPorCategoria: Array,
    categoriaFiltro: [String, Number],
});

const emit = defineEmits(['toggle']);

function formatGs(amount) {
    return '₲ ' + new Intl.NumberFormat('es-PY').format(amount);
}
</script>

<template>
    <div v-if="gastosPorCategoria.length > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <button
            v-for="cat in gastosPorCategoria"
            :key="cat.id"
            type="button"
            @click="emit('toggle', cat.id)"
            class="rounded-xl p-3 border text-left transition"
            :style="{
                background: categoriaFiltro === cat.id ? cat.color + '15' : 'var(--cs-paper-card)',
                borderColor: categoriaFiltro === cat.id ? cat.color : 'color-mix(in srgb, var(--cs-ink) 12%, transparent)',
                borderWidth: categoriaFiltro === cat.id ? '2px' : '1px',
            }"
        >
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full shrink-0" :style="{ backgroundColor: cat.color }"></span>
                <p class="text-xs font-medium truncate" style="color: color-mix(in srgb, var(--cs-ink) 70%, transparent);">
                    {{ cat.name }}
                </p>
            </div>
            <p class="text-sm font-semibold" style="font-family: 'JetBrains Mono', monospace; color: var(--cs-ink);">
                {{ formatGs(cat.total) }}
            </p>
        </button>
    </div>
</template>