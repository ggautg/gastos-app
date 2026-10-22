<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import MonthYearSelector from '@/Components/MonthYearSelector.vue';

const props = defineProps({
    month: Number,
    year: Number,
    totalActual: Number,
    totalAnterior: Number,
    comparaciones: Array,
});

const selectedMonth = ref(props.month);
const selectedYear = ref(props.year);

function goToMonth() {
    router.get(route('insights.index'), {
        month: selectedMonth.value,
        year: selectedYear.value,
    }, { preserveState: true });
}

function formatGs(amount) {
    return '₲ ' + new Intl.NumberFormat('es-PY').format(amount);
}

function fraseCategoria(c) {
    const verbo = c.porcentaje >= 0 ? 'subió' : 'bajó';
    return `Tu gasto en ${c.nombre} ${verbo} un ${Math.abs(c.porcentaje)}%`;
}

function colorPorcentaje(porcentaje) {
    return porcentaje >= 0 ? 'var(--cs-terra)' : 'var(--cs-emerald)';
}

const masSubio = props.comparaciones.find(c => c.porcentaje > 0);
const masBajo = props.comparaciones.find(c => c.porcentaje < 0);
</script>

<template>
    <Head title="Resumen" />

    <AuthenticatedLayout>
        <template #header>
            <div class="space-y-1.5">
                <p class="cs-eyebrow">Análisis</p>
                <h2 class="cs-page-title">Resumen</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-2xl space-y-5 px-4 sm:px-6 lg:px-8">

                <MonthYearSelector
                    v-model:month="selectedMonth"
                    v-model:year="selectedYear"
                    @change="goToMonth"
                />

                <div
                    v-if="totalAnterior === 0"
                    class="cs-card p-8 text-center"
                >
                    <p class="cs-empty">
                        Todavía no tenés suficiente historial para comparar este mes con el anterior.
                        Volvé a mirar acá el próximo mes.
                    </p>
                </div>

                <template v-else>
                    <div class="cs-card p-6">
                        <p class="cs-eyebrow mb-3">Comparativa mensual</p>
                        <p class="font-cs-display text-[1.35rem] leading-relaxed" style="color: var(--cs-ink); font-variation-settings: 'opsz' 40, 'SOFT' 28;">
                            Gastaste
                            <span class="cs-amount" :style="{ color: colorPorcentaje(totalActual - totalAnterior) }">
                                {{ formatGs(totalActual) }}
                            </span>
                            este mes,
                            <span v-if="totalActual > totalAnterior">
                                un poco más que los {{ formatGs(totalAnterior) }} del mes pasado.
                            </span>
                            <span v-else-if="totalActual < totalAnterior">
                                menos que los {{ formatGs(totalAnterior) }} del mes pasado.
                            </span>
                            <span v-else>
                                exactamente igual que el mes pasado.
                            </span>
                        </p>
                    </div>

                    <div v-if="comparaciones.length === 0" class="cs-card p-8 text-center">
                        <p class="cs-empty">
                            No hay categorías con gasto en ambos meses para comparar en detalle.
                        </p>
                    </div>

                    <div v-else class="space-y-3">
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div v-if="masSubio" class="cs-card p-5">
                                <p class="cs-kpi-label mb-1.5">Lo que más subió</p>
                                <p class="text-sm leading-relaxed" style="color: var(--cs-ink-soft);">
                                    {{ fraseCategoria(masSubio) }}
                                    <span class="cs-amount block text-xs" style="color: var(--cs-faint);">
                                        {{ formatGs(masSubio.anterior) }} → {{ formatGs(masSubio.actual) }}
                                    </span>
                                </p>
                            </div>

                            <div v-if="masBajo" class="cs-card p-5">
                                <p class="cs-kpi-label mb-1.5">Lo que más bajó</p>
                                <p class="text-sm leading-relaxed" style="color: var(--cs-ink-soft);">
                                    {{ fraseCategoria(masBajo) }}
                                    <span class="cs-amount block text-xs" style="color: var(--cs-faint);">
                                        {{ formatGs(masBajo.anterior) }} → {{ formatGs(masBajo.actual) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="cs-card p-5">
                            <p class="cs-section-title mb-2">Detalle por categoría</p>
                            <div v-for="c in comparaciones" :key="c.nombre" class="flex items-center justify-between gap-3 border-t py-2.5 text-sm first:border-t-0" style="border-color: var(--cs-line);">
                                <span style="color: var(--cs-ink-soft);">{{ c.nombre }}</span>
                                <span class="cs-amount text-xs" :style="{ color: colorPorcentaje(c.porcentaje) }">
                                    {{ c.porcentaje >= 0 ? '↑' : '↓' }} {{ Math.abs(c.porcentaje) }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>