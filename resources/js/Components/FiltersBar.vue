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
    <div class="cs-toolbar">
        <select v-model="selectedMonth" @change="emit('changeMonth')" class="cs-input w-auto text-sm">
            <option v-for="(m, i) in meses" :key="i" :value="i + 1">{{ m }}</option>
        </select>
        <select v-model="selectedYear" @change="emit('changeMonth')" class="cs-input w-auto text-sm">
            <option v-for="y in [2024, 2025, 2026, 2027]" :key="y" :value="y">{{ y }}</option>
        </select>
        <input
            v-model="busqueda"
            type="text"
            placeholder="Buscar por descripción o categoría..."
            class="cs-input min-w-[180px] flex-1 text-sm"
        />
        <div class="flex shrink-0 items-center gap-2 text-sm">
            <span class="cs-kpi-label">Ordenar</span>
            <select v-model="ordenarPor" class="cs-input w-auto text-sm">
                <option value="date">Fecha</option>
                <option value="amount">Monto</option>
                <option value="category">Categoría</option>
            </select>
            <button
                type="button"
                @click="ordenAscendente = !ordenAscendente"
                class="cs-btn cs-btn-ghost cs-btn-sm"
                :title="ordenAscendente ? 'Ascendente' : 'Descendente'"
            >
                {{ ordenAscendente ? '↑' : '↓' }}
            </button>
        </div>
    </div>
</template>
