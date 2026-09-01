<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

import FiltersBar from '@/Components/FiltersBar.vue';
import SummaryCards from '@/Components/SummaryCards.vue';
import CategoryChart from '@/Components/CategoryChart.vue';
import BudgetProgress from '@/Components/BudgetProgress.vue';
import CategoryTotals from '@/Components/CategoryTotals.vue';
import TransactionList from '@/Components/TransactionList.vue';
import TransactionModal from '@/Components/TransactionModal.vue';
import DeleteModal from '@/Components/DeleteModal.vue';

const props = defineProps({
    transactions: Array,
    categories: Array,
    filters: Object,
    summary: Object,
    gastosPorCategoria: Array,
    presupuestos: Array,
    comparativa: Object,
    role: String,
});

// --- Filtros ---
const selectedMonth = ref(props.filters.month);
const selectedYear = ref(props.filters.year);
const busqueda = ref('');
const categoriaFiltro = ref('');
const ordenarPor = ref('date');
const ordenAscendente = ref(false);
const porPagina = ref(50);
const paginaActual = ref(1);

function goToMonth() {
    router.get(route('transactions.index'), {
        month: selectedMonth.value,
        year: selectedYear.value,
    }, { preserveState: true });
}

const transaccionesFiltradas = computed(() => {
    let resultado = props.transactions;

    if (categoriaFiltro.value) {
        resultado = resultado.filter(t => t.category_id === categoriaFiltro.value);
    }

    if (busqueda.value.trim()) {
        const termino = busqueda.value.toLowerCase();
        resultado = resultado.filter(t =>
            (t.description ?? '').toLowerCase().includes(termino) ||
            t.category.name.toLowerCase().includes(termino)
        );
    }

    resultado = [...resultado].sort((a, b) => {
        let valorA, valorB;
        if (ordenarPor.value === 'category') {
            valorA = a.category.name;
            valorB = b.category.name;
        } else {
            valorA = a[ordenarPor.value];
            valorB = b[ordenarPor.value];
        }
        if (valorA < valorB) return ordenAscendente.value ? -1 : 1;
        if (valorA > valorB) return ordenAscendente.value ? 1 : -1;
        return 0;
    });

    return resultado;
});

const transaccionesPaginadas = computed(() => {
    const inicio = (paginaActual.value - 1) * porPagina.value;
    return transaccionesFiltradas.value.slice(inicio, inicio + porPagina.value);
});

const hayFiltroActivo = computed(() => !!busqueda.value || !!categoriaFiltro.value);

watch([busqueda, categoriaFiltro, ordenarPor, ordenAscendente, porPagina], () => {
    paginaActual.value = 1;
});

function toggleCategoriaFiltro(id) {
    categoriaFiltro.value = categoriaFiltro.value === id ? '' : id;
}

// --- Modal de crear/editar ---
const showForm = ref(false);
const editingTransaction = ref(null);

function openCreate() {
    editingTransaction.value = null;
    showForm.value = true;
}

function openEdit(t) {
    editingTransaction.value = t;
    showForm.value = true;
}

function duplicar(t) {
    editingTransaction.value = { ...t, id: null }; // copia los datos, pero sin id → el modal lo trata como "nuevo"
    showForm.value = true;
}

function onSaved(mensaje) {
    showForm.value = false;
    mostrarToast(mensaje);
}

// --- Modal de borrado ---
const transaccionAEliminar = ref(null);
const eliminando = ref(false);

function pedirConfirmacionBorrado(t) {
    transaccionAEliminar.value = t;
}

function confirmarBorrado() {
    eliminando.value = true;
    router.delete(route('transactions.destroy', transaccionAEliminar.value.id), {
        onSuccess: () => {
            transaccionAEliminar.value = null;
            mostrarToast('Movimiento eliminado', 'error');
        },
        onFinish: () => (eliminando.value = false),
    });
}

// --- Toast ---
const toast = ref(null);

function mostrarToast(mensaje, tipo = 'success') {
    toast.value = { mensaje, tipo };
    setTimeout(() => (toast.value = null), 3000);
}

// --- Atajos de teclado ---
function manejarAtajos(e) {
    const enCampoDeTexto = ['INPUT', 'SELECT', 'TEXTAREA'].includes(e.target.tagName);

    if (e.key === 'Escape') {
        if (transaccionAEliminar.value) {
            transaccionAEliminar.value = null;
            return;
        }
        if (showForm.value) {
            showForm.value = false;
            return;
        }
    }

    if (enCampoDeTexto) return;

    if (e.key === 'n' || e.key === 'N') openCreate();
}

onMounted(() => window.addEventListener('keydown', manejarAtajos));
onUnmounted(() => window.removeEventListener('keydown', manejarAtajos));
</script>

<template>

    <Head title="Movimientos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-cs-display text-xl font-semibold" style="color: var(--cs-ink);">
                Gastos y Ganancias
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-6">

                <SummaryCards :summary="summary" :comparativa="comparativa" />

                <CategoryChart :gastos-por-categoria="gastosPorCategoria" />
                
                <BudgetProgress :presupuestos="presupuestos" />

                <CategoryTotals :gastos-por-categoria="gastosPorCategoria" :categoria-filtro="categoriaFiltro"
                    @toggle="toggleCategoriaFiltro" />

                <FiltersBar v-model:month="selectedMonth" v-model:year="selectedYear" v-model:busqueda="busqueda"
                    v-model:categoriaFiltro="categoriaFiltro" v-model:ordenarPor="ordenarPor"
                    v-model:ordenAscendente="ordenAscendente" :categories="categories" @change-month="goToMonth" />
                <div class="flex justify-end gap-2">
                    <button v-if="role === 'owner'" type="button" @click="openCreate" class="cs-submit"
                        style="width: auto; padding: 0.6rem 1.1rem;">
                        + Nuevo movimiento
                    </button>
                    <a :href="route('transactions.export.excel', { month: selectedMonth, year: selectedYear })"
                        class="px-3 py-2 rounded-lg border text-sm"
                        style="border-color: color-mix(in srgb, var(--cs-ink) 18%, transparent); color: color-mix(in srgb, var(--cs-ink) 70%, transparent);">
                        ⬇ Excel
                    </a>
                    <a :href="route('transactions.export.pdf', { month: selectedMonth, year: selectedYear })"
                        class="px-3 py-2 rounded-lg border text-sm"
                        style="border-color: color-mix(in srgb, var(--cs-ink) 18%, transparent); color: color-mix(in srgb, var(--cs-ink) 70%, transparent);">
                        ⬇ PDF
                    </a>
                </div>

                <TransactionList :transacciones="transaccionesPaginadas"
                    :total-filtradas="transaccionesFiltradas.length" :hay-filtro-activo="hayFiltroActivo" :role="role"
                    v-model:pagina-actual="paginaActual" v-model:por-pagina="porPagina" @duplicar="duplicar"
                    @editar="openEdit" @eliminar="pedirConfirmacionBorrado" />
            </div>
        </div>

        <TransactionModal :show="showForm" :editing-transaction="editingTransaction" :categories="categories"
            @close="showForm = false" @saved="onSaved" />

        <DeleteModal :show="!!transaccionAEliminar" title="¿Eliminar movimiento?"
            :message="transaccionAEliminar ? `Vas a borrar &quot;${transaccionAEliminar.description || transaccionAEliminar.category.name}&quot;. Esta acción no se puede deshacer.` : ''"
            :processing="eliminando" @cancel="transaccionAEliminar = null" @confirm="confirmarBorrado" />

        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="toast"
                class="fixed bottom-6 right-6 px-4 py-3 rounded-lg shadow-lg text-white text-sm font-medium z-50"
                :style="{ background: toast.tipo === 'success' ? 'var(--cs-teal)' : '#D85A30' }">
                {{ toast.mensaje }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>