<script setup>
import { ref, computed, watch } from 'vue';
import { Pie } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale } from 'chart.js';
import { theme } from '@/Composables/useTheme';
import { useChartColors, colorLegible } from '@/Composables/useChartColors';
import { formatGs } from '@/Composables/useFormat';

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

const props = defineProps({
    gastosPorCategoria: Array,
});

const mostrar = ref(true);
const chartKey = ref(0);
const colors = useChartColors();

watch(theme, () => chartKey.value++);

const chartData = computed(() => ({
    labels: props.gastosPorCategoria.map(g => g.name),
    datasets: [{
        data: props.gastosPorCategoria.map(g => g.total),
        backgroundColor: props.gastosPorCategoria.map(
            g => colorLegible(g.color, colors.value.dark) ?? colors.value.emerald,
        ),
        borderWidth: 0,
    }],
}));

const chartOptions = computed(() => ({
    responsive: true,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                boxWidth: 12,
                padding: 12,
                font: { size: 11, family: "'Instrument Sans Variable', sans-serif" },
                color: colors.value.muted,
            },
        },
        tooltip: {
            backgroundColor: colors.value.paper,
            titleColor: colors.value.muted,
            bodyColor: colors.value.muted,
            borderColor: colors.value.grid,
            borderWidth: 1,
            callbacks: {
                label: (ctx) => `${ctx.label}: ${formatGs(ctx.raw)}`,
            },
        },
    },
}));
</script>

<template>
    <div v-if="gastosPorCategoria.length > 0" class="cs-card p-5">
        <button
            type="button"
            @click="mostrar = !mostrar"
            class="flex w-full items-center justify-between gap-3 text-left"
        >
            <span class="cs-section-title">Gastos por categoría</span>
            <span class="cs-btn cs-btn-ghost cs-btn-sm">
                {{ mostrar ? 'Ocultar' : 'Mostrar' }}
            </span>
        </button>
        <div v-if="mostrar" class="mx-auto mt-4 max-w-[320px]">
            <Pie :key="chartKey" :data="chartData" :options="chartOptions" />
        </div>
    </div>
</template>
