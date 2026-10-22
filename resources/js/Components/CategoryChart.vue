<script setup>
import { ref, computed } from 'vue';
import { Pie } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

const props = defineProps({
    gastosPorCategoria: Array,
});

const mostrar = ref(false);

const chartData = computed(() => ({
    labels: props.gastosPorCategoria.map(g => g.name),
    datasets: [{
        data: props.gastosPorCategoria.map(g => g.total),
        backgroundColor: props.gastosPorCategoria.map(g => g.color),
        borderWidth: 0,
    }],
}));

const chartOptions = {
    responsive: true,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                boxWidth: 12,
                padding: 12,
                font: { size: 11, family: "'Instrument Sans Variable', sans-serif" },
                color: 'rgba(128, 140, 134, 0.9)',
            },
        },
        tooltip: {
            callbacks: {
                label: (ctx) => `${ctx.label}: ₲ ${new Intl.NumberFormat('es-PY').format(ctx.raw)}`,
            },
        },
    },
};
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
            <Pie :data="chartData" :options="chartOptions" />
        </div>
    </div>
</template>
