<script setup>
import { useYears } from '@/Composables/useYears';

defineProps({
    years: {
        type: Array,
        default: () => [],
    },
});

const month = defineModel('month');
const year = defineModel('year');

const emit = defineEmits(['change']);

const anios = useYears(year);

const meses = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
];
</script>

<template>
    <div class="cs-toolbar w-fit">
        <select v-model="month" @change="emit('change')" aria-label="Mes" class="cs-input w-auto text-sm">
            <option v-for="(m, i) in meses" :key="i" :value="i + 1">{{ m }}</option>
        </select>
        <select v-model="year" @change="emit('change')" aria-label="Año" class="cs-input w-auto text-sm">
            <option v-for="a in anios" :key="a" :value="a">{{ a }}</option>
        </select>
    </div>
</template>
