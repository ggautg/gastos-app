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
        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12, font: { size: 11 } } },
        tooltip: {
            callbacks: {
                label: (ctx) => `${ctx.label}: ₲ ${new Intl.NumberFormat('es-PY').format(ctx.raw)}`,
            },
        },
    },
};
</script>

<template>
    <div v-if="gastosPorCategoria.length > 0" class="rounded-xl border p-5" style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);">
        <button
            type="button"
            @click="mostrar = !mostrar"
            class="flex items-center justify-between w-full text-sm font-medium"
            style="color: color-mix(in srgb, var(--cs-ink) 70%, transparent);"
        >
            <span>Gastos por categoría</span>
            <span class="text-xs" style="color: color-mix(in srgb, var(--cs-ink) 40%, transparent);">
                {{ mostrar ? 'Ocultar ▲' : 'Mostrar ▼' }}
            </span>
        </button>
        <div v-if="mostrar" class="max-w-[320px] mx-auto mt-4">
            <Pie :data="chartData" :options="chartOptions" />
        </div>
    </div>
</template>