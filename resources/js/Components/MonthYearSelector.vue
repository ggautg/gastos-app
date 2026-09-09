<script setup>
defineProps({
    years: {
        type: Array,
        default: () => [2024, 2025, 2026, 2027],
    },
});

const month = defineModel('month');
const year = defineModel('year');

const emit = defineEmits(['change']);

const meses = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
];
</script>

<template>
    <div class="flex items-center gap-3">
        <select v-model="month" @change="emit('change')" class="cs-input text-sm">
            <option v-for="(m, i) in meses" :key="i" :value="i + 1">{{ m }}</option>
        </select>
        <select v-model="year" @change="emit('change')" class="cs-input text-sm">
            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
        </select>
    </div>
</template>