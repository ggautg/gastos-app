<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { Bar } from 'vue-chartjs';
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

const nombresMeses = [
    'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
    'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic',
];

function irAAnio(nuevoAnio) {
    router.get(route('summary.yearly'), { year: nuevoAnio }, { preserveState: true });
}

function formatGs(amount) {
    return '₲ ' + new Intl.NumberFormat('es-PY').format(amount);
}

const chartData = computed(() => ({
    labels: nombresMeses,
    datasets: [
        {
            label: 'Ganancias',
            data: props.meses.map(m => m.ganancias),
            backgroundColor: '#10b981',
            borderRadius: 4,
        },
        {
            label: 'Gastos',
            data: props.meses.map(m => m.gastos),
            backgroundColor: '#ef4444',
            borderRadius: 4,
        },
    ],
}));

const chartOptions = {
    responsive: true,
    plugins: {
        legend: { position: 'top' },
        tooltip: {
            callbacks: {
                label: (ctx) =>
                    `${ctx.dataset.label}: ₲ ${new Intl.NumberFormat('es-PY').format(ctx.raw)}`,
            },
        },
    },
    scales: {
        y: {
            ticks: {
                callback: (value) => new Intl.NumberFormat('es-PY', { notation: 'compact' }).format(value),
            },
        },
    },
};

const totalAnual = computed(() => ({
    ganancias: props.meses.reduce((acc, m) => acc + m.ganancias, 0),
    gastos: props.meses.reduce((acc, m) => acc + m.gastos, 0),
}));
</script>

<template>
    <Head title="Resumen anual" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-slate-800">Resumen anual</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-6">

                <div class="flex items-center gap-3">
                    <button
                        @click="irAAnio(year - 1)"
                        class="px-3 py-1.5 rounded-lg border border-slate-300 text-sm hover:bg-slate-50"
                    >
                        ← {{ year - 1 }}
                    </button>
                    <span class="text-lg font-semibold text-slate-800">{{ year }}</span>
                    <button
                        @click="irAAnio(year + 1)"
                        class="px-3 py-1.5 rounded-lg border border-slate-300 text-sm hover:bg-slate-50"
                    >
                        {{ year + 1 }} →
                    </button>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <p class="text-xs text-slate-500 mb-1">Ganancias del año</p>
                        <p class="text-lg font-semibold text-emerald-700">{{ formatGs(totalAnual.ganancias) }}</p>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <p class="text-xs text-slate-500 mb-1">Gastos del año</p>
                        <p class="text-lg font-semibold text-red-700">{{ formatGs(totalAnual.gastos) }}</p>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <p class="text-xs text-slate-500 mb-1">Balance del año</p>
                        <p
                            class="text-lg font-semibold"
                            :class="(totalAnual.ganancias - totalAnual.gastos) >= 0 ? 'text-teal-700' : 'text-red-700'"
                        >
                            {{ formatGs(totalAnual.ganancias - totalAnual.gastos) }}
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-4">
                    <Bar :data="chartData" :options="chartOptions" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>