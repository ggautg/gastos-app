<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Pie } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

const props = defineProps({
    transactions: Array,
    categories: Array,
    filters: Object,
    summary: Object,
    gastosPorCategoria: Array,
    presupuestos: Array,
    comparativa: Object,
});

const meses = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
];

const selectedMonth = ref(props.filters.month);
const selectedYear = ref(props.filters.year);

function goToMonth() {
    router.get(route('transactions.index'), {
        month: selectedMonth.value,
        year: selectedYear.value,
    }, { preserveState: true });
}

const chartData = computed(() => ({
    labels: props.gastosPorCategoria.map(g => g.name),
    datasets: [
        {
            data: props.gastosPorCategoria.map(g => g.total),
            backgroundColor: props.gastosPorCategoria.map(g => g.color),
            borderWidth: 0,
        },
    ],
}));

const chartOptions = {
    responsive: true,
    plugins: {
        legend: {
            position: 'bottom',
            labels: { boxWidth: 12, padding: 16 },
        },
        tooltip: {
            callbacks: {
                label: (ctx) => `${ctx.label}: ₲ ${new Intl.NumberFormat('es-PY').format(ctx.raw)}`,
            },
        },
    },
};

const diferencias = computed(() => {
    const gastoAnt = props.comparativa.gastos_anterior;
    const gananciaAnt = props.comparativa.ganancias_anterior;

    return {
        gasto: {
            valor: props.summary.gastos - gastoAnt,
            porcentaje: gastoAnt > 0
                ? Math.round(((props.summary.gastos - gastoAnt) / gastoAnt) * 100)
                : null,
        },
        ganancia: {
            valor: props.summary.ganancias - gananciaAnt,
            porcentaje: gananciaAnt > 0
                ? Math.round(((props.summary.ganancias - gananciaAnt) / gananciaAnt) * 100)
                : null,
        },
    };
});

function formatGs(amount) {
    return '₲ ' + new Intl.NumberFormat('es-PY').format(amount);
}

const showForm = ref(false);
const editingId = ref(null);
const toast = ref(null);

function mostrarToast(mensaje, tipo = 'success') {
    toast.value = { mensaje, tipo };

    setTimeout(() => {
        toast.value = null;
    }, 3000);
}

function colorBarra(porcentaje) {
    if (porcentaje >= 100) return 'bg-red-500';
    if (porcentaje >= 80) return 'bg-amber-500';
    return 'bg-emerald-500';
}

const form = useForm({
    category_id: '',
    type: 'gasto',
    amount: '',
    currency: 'PYG',
    description: '',
    date: new Date().toISOString().slice(0, 10),
});

const filteredCategories = computed(() =>
    props.categories.filter(c => c.type === form.type)
);

const busqueda = ref('');
const ordenarPor = ref('date');
const ordenAscendente = ref(false);

const transaccionesFiltradas = computed(() => {
    let resultado = props.transactions;

    if (busqueda.value.trim()) {
        const termino = busqueda.value.toLowerCase();
        resultado = resultado.filter(t =>
            (t.description ?? '').toLowerCase().includes(termino) ||
            t.category.name.toLowerCase().includes(termino)
        );
    }

    resultado = [...resultado].sort((a, b) => {
        let valorA, valorB;

        if (ordenarPor.value === 'amount') {
            valorA = a.amount;
            valorB = b.amount;
        } else if (ordenarPor.value === 'category') {
            valorA = a.category.name;
            valorB = b.category.name;
        } else {
            valorA = a.date;
            valorB = b.date;
        }

        if (valorA < valorB) return ordenAscendente.value ? -1 : 1;
        if (valorA > valorB) return ordenAscendente.value ? 1 : -1;
        return 0;
    });

    return resultado;
});



function openCreate() {
    editingId.value = null;
    form.reset();
    form.currency = 'PYG';
    form.date = new Date().toISOString().slice(0, 10);
    showForm.value = true;
}

function openEdit(t) {
    editingId.value = t.id;
    form.category_id = t.category_id;
    form.type = t.type;
    form.amount = t.amount;
    form.currency = t.currency;
    form.description = t.description;
    form.date = t.date;
    showForm.value = true;
}

function duplicar(t) {
    editingId.value = null;
    form.category_id = t.category_id;
    form.type = t.type;
    form.amount = t.amount;
    form.currency = t.currency;
    form.description = t.description;
    form.date = new Date().toISOString().slice(0, 10);
    showForm.value = true;
}

function submit() {
    if (editingId.value) {
        form.put(route('transactions.update', editingId.value), {
            onSuccess: () => {
                showForm.value = false;
                mostrarToast('Movimiento actualizado');
            },
        });
    } else {
        form.post(route('transactions.store'), {
            onSuccess: () => {
                showForm.value = false;
                mostrarToast('Movimiento creado');
            },
        });
    }
}

function remove(t) {
    if (confirm('¿Borrar este movimiento?')) {
        form.delete(route('transactions.destroy', t.id), {
            onSuccess: () => mostrarToast('Movimiento eliminado', 'error'),
        });
    }
}
</script>

<template>

    <Head title="Movimientos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-slate-800">Gastos y Ganancias</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Filtro de mes -->
                <div class="flex items-center gap-3">
                    <select v-model="selectedMonth" @change="goToMonth" class="rounded-lg border-slate-300 text-sm">
                        <option v-for="(m, i) in meses" :key="i" :value="i + 1">{{ m }}</option>
                    </select>
                    <select v-model="selectedYear" @change="goToMonth" class="rounded-lg border-slate-300 text-sm">
                        <option v-for="y in [2024, 2025, 2026, 2027]" :key="y" :value="y">{{ y }}</option>
                    </select>
                    <input v-model="busqueda" type="text" placeholder="Buscar por descripción o categoría..."
                        class="flex-1 rounded-lg border-slate-300 text-sm focus:border-teal-600 focus:ring-teal-600" />
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-slate-500">Ordenar por:</span>
                        <select v-model="ordenarPor" class="rounded-lg border-slate-300 text-sm">
                            <option value="date">Fecha</option>
                            <option value="amount">Monto</option>
                            <option value="category">Categoría</option>
                        </select>
                        <button @click="ordenAscendente = !ordenAscendente"
                            class="px-2 py-1 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50"
                            :title="ordenAscendente ? 'Ascendente' : 'Descendente'">
                            {{ ordenAscendente ? '↑' : '↓' }}
                        </button>
                    </div>
                </div>

                <!-- Resumen -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <p class="text-xs text-slate-500 mb-1">Ganancias</p>
                        <p class="text-lg font-semibold text-emerald-700">{{ formatGs(summary.ganancias) }}</p>
                        <p v-if="comparativa.ganancias_anterior > 0" class="text-xs mt-1"
                            :class="diferencias.ganancia.valor >= 0 ? 'text-emerald-600' : 'text-red-500'">
                            {{ diferencias.ganancia.valor >= 0 ? '↑' : '↓' }}
                            {{ diferencias.ganancia.porcentaje !== null ? Math.abs(diferencias.ganancia.porcentaje) +
                                '%' : ''
                            }}
                            vs. mes anterior
                        </p>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <p class="text-xs text-slate-500 mb-1">Gastos</p>
                        <p class="text-lg font-semibold text-red-700">{{ formatGs(summary.gastos) }}</p>
                        <p v-if="comparativa.gastos_anterior > 0" class="text-xs mt-1"
                            :class="diferencias.gasto.valor <= 0 ? 'text-emerald-600' : 'text-red-500'">
                            {{ diferencias.gasto.valor >= 0 ? '↑' : '↓' }}
                            {{ diferencias.gasto.porcentaje !== null ? Math.abs(diferencias.gasto.porcentaje) + '%' : ''
                            }}
                            vs. mes anterior
                        </p>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-4">
                        <p class="text-xs text-slate-500 mb-1">Balance</p>
                        <p class="text-lg font-semibold"
                            :class="summary.balance >= 0 ? 'text-teal-700' : 'text-red-700'">
                            {{ formatGs(summary.balance) }}
                        </p>
                    </div>
                </div>

                <div v-if="gastosPorCategoria.length > 0" class="bg-white rounded-xl border border-slate-200 p-4">
                    <p class="text-sm font-medium text-slate-600 mb-3">Gastos por categoría</p>
                    <div class="max-w-xs mx-auto">
                        <Pie :data="chartData" :options="chartOptions" />
                    </div>
                </div>
                <p v-else class="text-sm text-slate-400 text-center py-2">
                    No hay gastos este mes para graficar.
                </p>

                <div v-if="presupuestos.length > 0" class="bg-white rounded-xl border border-slate-200 p-4 space-y-4">
                    <p class="text-sm font-medium text-slate-600">Presupuestos del mes</p>

                    <div v-for="p in presupuestos" :key="p.name">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-700 font-medium">{{ p.name }}</span>
                            <span class="text-slate-500">
                                {{ formatGs(p.gastado) }} / {{ formatGs(p.budget) }}
                            </span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all" :class="colorBarra(p.porcentaje)"
                                :style="{ width: Math.min(p.porcentaje, 100) + '%' }"></div>
                        </div>
                        <p v-if="p.porcentaje >= 100" class="text-xs text-red-600 mt-1">
                            Te pasaste ₲{{ new Intl.NumberFormat('es-PY').format(p.gastado - p.budget) }}
                        </p>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button @click="openCreate"
                        class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-medium text-white hover:bg-teal-800 transition">
                        + Nuevo movimiento
                    </button>
                </div>

                <!-- Listado -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 divide-y divide-slate-100">
                    <div v-for="t in transaccionesFiltradas" :key="t.id"
                        class="flex items-center justify-between px-4 py-3">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full"
                                :style="{ backgroundColor: t.category.color }"></span>
                            <div>
                                <p class="font-medium text-slate-800">
                                    {{ t.description || t.category.name }}
                                </p>
                                <p class="text-xs text-slate-400">
                                    {{ t.category.name }} · {{ t.date }}
                                    <span v-if="t.currency === 'USD'">· US$ {{ t.amount }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="font-semibold"
                                :class="t.type === 'gasto' ? 'text-red-700' : 'text-emerald-700'">
                                {{ t.type === 'gasto' ? '-' : '+' }}{{ formatGs(t.amount_gs) }}
                            </span>
                            <div class="flex gap-2 text-sm">
                                <button @click="duplicar(t)" class="text-slate-400 hover:text-teal-700"
                                    title="Duplicar">⎘</button>
                                <button @click="openEdit(t)" class="text-slate-400 hover:text-slate-700">✎</button>
                                <button @click="remove(t)" class="text-red-400 hover:text-red-700">✕</button>
                            </div>
                        </div>
                    </div>

                    <p v-if="transaccionesFiltradas.length === 0" class="px-4 py-6 text-center text-sm text-slate-400">
                        {{ busqueda ? 'No se encontraron movimientos con ese criterio.' :
                            'No hay movimientos cargados en este mes.' }}
                    </p>
                </div>
            </div>
        </div>

        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="toast"
                class="fixed bottom-6 right-6 px-4 py-3 rounded-lg shadow-lg text-white text-sm font-medium z-50"
                :class="toast.tipo === 'success' ? 'bg-emerald-600' : 'bg-red-600'">
                {{ toast.mensaje }}
            </div>
        </Transition>
        <!-- Modal -->
        <div v-if="showForm" class="fixed inset-0 bg-black/30 flex items-center justify-center px-4 z-50"
            @click.self="showForm = false">
            <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">
                    {{ editingId ? 'Editar movimiento' : 'Nuevo movimiento' }}
                </h3>

                <form @submit.prevent="submit" class="space-y-4">
                    <div class="flex gap-2">
                        <button type="button" @click="form.type = 'gasto'; form.category_id = ''"
                            class="flex-1 py-2 rounded-lg text-sm font-medium border" :class="form.type === 'gasto'
                                ? 'bg-red-50 border-red-300 text-red-700'
                                : 'border-slate-200 text-slate-500'">
                            Gasto
                        </button>
                        <button type="button" @click="form.type = 'ganancia'; form.category_id = ''"
                            class="flex-1 py-2 rounded-lg text-sm font-medium border" :class="form.type === 'ganancia'
                                ? 'bg-emerald-50 border-emerald-300 text-emerald-700'
                                : 'border-slate-200 text-slate-500'">
                            Ganancia
                        </button>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-600 mb-1">Categoría</label>
                        <select v-model="form.category_id"
                            class="w-full rounded-lg border-slate-300 focus:border-teal-600 focus:ring-teal-600">
                            <option value="" disabled>Elegí una categoría</option>
                            <option v-for="c in filteredCategories" :key="c.id" :value="c.id">
                                {{ c.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.category_id" class="text-xs text-red-600 mt-1">
                            {{ form.errors.category_id }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-600 mb-1">Monto (₲)</label>
                        <input v-model="form.amount" type="number" min="1"
                            class="w-full rounded-lg border-slate-300 focus:border-teal-600 focus:ring-teal-600"
                            placeholder="Ej: 150000" />
                        <p v-if="form.errors.amount" class="text-xs text-red-600 mt-1">{{ form.errors.amount }}</p>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-600 mb-1">Monto</label>
                        <div class="flex gap-2">
                            <select v-model="form.currency"
                                class="rounded-lg border-slate-300 text-sm focus:border-teal-600 focus:ring-teal-600">
                                <option value="PYG">₲</option>
                                <option value="USD">US$</option>
                            </select>
                            <input v-model="form.amount" type="number" :min="form.currency === 'USD' ? 0.01 : 1"
                                :step="form.currency === 'USD' ? 0.01 : 1"
                                class="flex-1 rounded-lg border-slate-300 focus:border-teal-600 focus:ring-teal-600"
                                :placeholder="form.currency === 'USD' ? 'Ej: 49.99' : 'Ej: 150000'" />
                        </div>
                        <p v-if="form.errors.amount" class="text-xs text-red-600 mt-1">{{ form.errors.amount }}</p>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-600 mb-1">Descripción (opcional)</label>
                        <input v-model="form.description" type="text"
                            class="w-full rounded-lg border-slate-300 focus:border-teal-600 focus:ring-teal-600"
                            placeholder="Ej: Supermercado" />
                    </div>

                    <div>
                        <label class="block text-sm text-slate-600 mb-1">Fecha</label>
                        <input v-model="form.date" type="date"
                            class="w-full rounded-lg border-slate-300 focus:border-teal-600 focus:ring-teal-600" />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showForm = false"
                            class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="form.processing"
                            class="px-4 py-2 text-sm font-medium text-white bg-teal-700 rounded-lg hover:bg-teal-800 disabled:opacity-50">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>