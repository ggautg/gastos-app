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
    <div v-if="gastosPorCategoria.length > 0" class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <button
            v-for="cat in gastosPorCategoria"
            :key="cat.id"
            type="button"
            @click="emit('toggle', cat.id)"
            class="cs-card p-3.5 text-left"
            :class="{ 'cs-card-hover': categoriaFiltro !== cat.id }"
            :style="categoriaFiltro === cat.id
                ? { background: `color-mix(in srgb, ${cat.color} 10%, var(--cs-paper-card))`, borderColor: cat.color }
                : {}"
        >
            <div class="mb-1 flex items-center gap-2">
                <span class="h-2 w-2 shrink-0 rounded-full" :style="{ backgroundColor: cat.color }"></span>
                <p class="truncate text-xs font-semibold" style="color: var(--cs-muted);">
                    {{ cat.name }}
                </p>
            </div>
            <p class="cs-amount text-sm" style="color: var(--cs-ink);">
                {{ formatGs(cat.total) }}
            </p>
        </button>
    </div>
</template>
