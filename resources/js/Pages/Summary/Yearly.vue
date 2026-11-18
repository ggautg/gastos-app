<script setup>
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { theme } from '@/Composables/useTheme';
import { useChartColors } from '@/Composables/useChartColors';
import { formatGs, formatCompacto } from '@/Composables/useFormat';
import { Bar } from 'vue-chartjs';
import PageHeader from '@/Components/PageHeader.vue';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
} from 'chart.js';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
    year: Number,
    meses: Array,
});

const chartKey = ref(0);
const colors = useChartColors();

watch(theme, () => chartKey.value++);

const nombresMeses = [
    'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
    'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic',
];

function irAAnio(nuevoAnio) {
    router.get(route('summary.yearly'), { year: nuevoAnio }, { preserveState: true });
}

const chartData = computed(() => ({
    labels: nombresMeses,
    datasets: [
        {
            label: 'Ganancias',
            data: props.meses.map(m => m.ganancias),
            backgroundColor: colors.value.emerald,
            borderRadius: 6,
        },
        {
            label: 'Gastos',
            data: props.meses.map(m => m.gastos),
            backgroundColor: colors.value.terra,
            borderRadius: 6,
        },
    ],
}));

const chartOptions = computed(() => ({
    responsive: true,
    plugins: {
        legend: {
            position: 'top',
            labels: {
                boxWidth: 10,
                boxHeight: 10,
                usePointStyle: true,
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
                label: (ctx) => `${ctx.dataset.label}: ${formatGs(ctx.raw)}`,
            },
        },
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: {
                font: { size: 10, family: "'Instrument Sans Variable', sans-serif" },
                color: colors.value.muted,
            },
        },
        y: {
            grid: { color: colors.value.grid },
            border: { display: false },
            ticks: {
                font: { size: 10, family: "'JetBrains Mono Variable', monospace" },
                color: colors.value.muted,
                callback: (value) => formatCompacto(value),
            },
        },
    },
}));

const totalAnual = computed(() => ({
    ganancias: props.meses.reduce((acc, m) => acc + m.ganancias, 0),
    gastos: props.meses.reduce((acc, m) => acc + m.gastos, 0),
}));
</script>

<template>
    <Head title="Resumen anual" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader eyebrow="Balance" title="Resumen anual" />
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-6">

                <div class="flex items-center gap-3">
                    <button
                        @click="irAAnio(year - 1)"
                        class="cs-btn cs-btn-ghost cs-btn-sm"
                    >
                        ← {{ year - 1 }}
                    </button>
                    <span class="cs-amount text-xl" style="color: var(--cs-ink);">{{ year }}</span>
                    <button
                        @click="irAAnio(year + 1)"
                        class="cs-btn cs-btn-ghost cs-btn-sm"
                    >
                        {{ year + 1 }} →
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="cs-kpi">
                        <p class="cs-kpi-label">Ganancias del año</p>
                        <p class="cs-kpi-value" style="color: var(--cs-emerald);">{{ formatGs(totalAnual.ganancias) }}</p>
                    </div>
                    <div class="cs-kpi">
                        <p class="cs-kpi-label">Gastos del año</p>
                        <p class="cs-kpi-value" style="color: var(--cs-terra);">{{ formatGs(totalAnual.gastos) }}</p>
                    </div>
                    <div class="cs-kpi">
                        <p class="cs-kpi-label">Balance del año</p>
                        <p
                            class="cs-kpi-value"
                            :style="{ color: (totalAnual.ganancias - totalAnual.gastos) >= 0 ? 'var(--cs-teal)' : 'var(--cs-terra)' }"
                        >
                            {{ formatGs(totalAnual.ganancias - totalAnual.gastos) }}
                        </p>
                    </div>
                </div>

                <div class="cs-card p-5">
                    <Bar :key="chartKey" :data="chartData" :options="chartOptions" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>