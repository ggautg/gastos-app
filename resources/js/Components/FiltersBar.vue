<script setup>
defineProps({
    categories: Array,
});

const selectedMonth = defineModel('month');
const selectedYear = defineModel('year');
const busqueda = defineModel('busqueda');
const ordenarPor = defineModel('ordenarPor');
const ordenAscendente = defineModel('ordenAscendente');

const emit = defineEmits(['changeMonth']);

const meses = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
];
</script>

<template>
    <div class="flex items-center gap-3 flex-wrap">
        <select v-model="selectedMonth" @change="emit('changeMonth')" class="cs-input text-sm">
            <option v-for="(m, i) in meses" :key="i" :value="i + 1">{{ m }}</option>
        </select>
        <select v-model="selectedYear" @change="emit('changeMonth')" class="cs-input text-sm">
            <option v-for="y in [2024, 2025, 2026, 2027]" :key="y" :value="y">{{ y }}</option>
        </select>
        <input
            v-model="busqueda"
            type="text"
            placeholder="Buscar por descripción o categoría..."
            class="cs-input text-sm flex-1 min-w-[180px]"
        />
        <div class="flex items-center gap-2 text-sm shrink-0">
            <span style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">Ordenar:</span>
            <select v-model="ordenarPor" class="cs-input text-sm">
                <option value="date">Fecha</option>
                <option value="amount">Monto</option>
                <option value="category">Categoría</option>
            </select>
            <button
                type="button"
                @click="ordenAscendente = !ordenAscendente"
                class="px-2 py-1.5 rounded-lg border text-sm"
                style="border-color: color-mix(in srgb, var(--cs-ink) 18%, transparent);"
                :title="ordenAscendente ? 'Ascendente' : 'Descendente'"
            >
                {{ ordenAscendente ? '↑' : '↓' }}
            </button>
        </div>
    </div>
</template>