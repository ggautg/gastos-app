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
    return porcentaje >= 0 ? '#D85A30' : '#1D9E75';
}

const masSubio = props.comparaciones.find(c => c.porcentaje > 0);
const masBajo = props.comparaciones.find(c => c.porcentaje < 0);
</script>

<template>
    <Head title="Resumen" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-cs-display text-xl font-semibold" style="color: var(--cs-ink);">
                Resumen del mes
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 space-y-6">

                <MonthYearSelector
                    v-model:month="selectedMonth"
                    v-model:year="selectedYear"
                    @change="goToMonth"
                />

                <div
                    v-if="totalAnterior === 0"
                    class="rounded-xl border p-6 text-center"
                    style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);"
                >
                    <p class="text-sm" style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">
                        Todavía no tenés suficiente historial para comparar este mes con el anterior.
                        Volvé a mirar acá el próximo mes. 📊
                    </p>
                </div>

                <template v-else>
                    <div
                        class="rounded-xl border p-6"
                        style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);"
                    >
                        <p class="font-cs-display text-lg" style="color: var(--cs-ink);">
                            Gastaste
                            <span style="font-family: 'JetBrains Mono', monospace;" :style="{ color: colorPorcentaje(totalActual - totalAnterior) }">
                                {{ formatGs(totalActual) }}
                            </span>
                            este mes,
                            <span v-if="totalActual > totalAnterior">
                                un poco más que los {{ formatGs(totalAnterior) }} del mes pasado.
                            </span>
                            <span v-else-if="totalActual < totalAnterior">
                                menos que los {{ formatGs(totalAnterior) }} del mes pasado. 🎉
                            </span>
                            <span v-else>
                                exactamente igual que el mes pasado.
                            </span>
                        </p>
                    </div>

                    <div v-if="comparaciones.length === 0" class="rounded-xl border p-6 text-center" style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);">
                        <p class="text-sm" style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">
                            No hay categorías con gasto en ambos meses para comparar en detalle.
                        </p>
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-if="masSubio"
                            class="rounded-xl border p-5"
                            style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);"
                        >
                            <p class="text-xs mb-1" style="color: color-mix(in srgb, var(--cs-ink) 50%, transparent);">Lo que más subió</p>
                            <p style="color: var(--cs-ink);">
                                {{ fraseCategoria(masSubio) }}
                                <span style="color: color-mix(in srgb, var(--cs-ink) 50%, transparent);">
                                    ({{ formatGs(masSubio.anterior) }} → {{ formatGs(masSubio.actual) }})
                                </span>
                            </p>
                        </div>

                        <div
                            v-if="masBajo"
                            class="rounded-xl border p-5"
                            style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);"
                        >
                            <p class="text-xs mb-1" style="color: color-mix(in srgb, var(--cs-ink) 50%, transparent);">Lo que más bajó</p>
                            <p style="color: var(--cs-ink);">
                                {{ fraseCategoria(masBajo) }}
                                <span style="color: color-mix(in srgb, var(--cs-ink) 50%, transparent);">
                                    ({{ formatGs(masBajo.anterior) }} → {{ formatGs(masBajo.actual) }})
                                </span>
                            </p>
                        </div>

                        <div
                            class="rounded-xl border p-5"
                            style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);"
                        >
                            <p class="text-sm font-medium mb-3" style="color: color-mix(in srgb, var(--cs-ink) 70%, transparent);">
                                Detalle por categoría
                            </p>
                            <div v-for="c in comparaciones" :key="c.nombre" class="flex justify-between text-sm py-1.5">
                                <span style="color: var(--cs-ink);">{{ c.nombre }}</span>
                                <span :style="{ color: colorPorcentaje(c.porcentaje) }">
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