<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

import FiltersBar from '@/Components/FiltersBar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import SummaryCards from '@/Components/SummaryCards.vue';
import CategoryChart from '@/Components/CategoryChart.vue';
import BudgetProgress from '@/Components/BudgetProgress.vue';
import CategoryTotals from '@/Components/CategoryTotals.vue';
import TransactionList from '@/Components/TransactionList.vue';
import TransactionModal from '@/Components/TransactionModal.vue';
import DeleteModal from '@/Components/DeleteModal.vue';
import ToastHost from '@/Components/ToastHost.vue';
import { useToast } from '@/Composables/useToast';

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
        if (ordenarPor.value === 'date') {
            const fechaA = String(a.date).slice(0, 10);
            const fechaB = String(b.date).slice(0, 10);

            if (fechaA !== fechaB) {
                return ordenAscendente.value
                    ? fechaA.localeCompare(fechaB)
                    : fechaB.localeCompare(fechaA);
            }

            // Mismo día: el último movimiento cargado queda arriba (pila).
            return ordenAscendente.value ? a.id - b.id : b.id - a.id;
        }

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
        return b.id - a.id;
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
            mostrarToast('Movimiento eliminado');
        },
        onFinish: () => (eliminando.value = false),
    });
}

// --- Toast global ---
const { mostrarToast } = useToast();

// --- Atajos de teclado ---
function manejarAtajos(e) {
    // Escape lo maneja AppModal; acá solo el atajo para cargar.
    if (e.key !== 'n' && e.key !== 'N') return;
    if (showForm.value || transaccionAEliminar.value) return;
    if (['INPUT', 'SELECT', 'TEXTAREA'].includes(e.target.tagName)) return;
    openCreate();
}

onMounted(() => window.addEventListener('keydown', manejarAtajos));
onUnmounted(() => window.removeEventListener('keydown', manejarAtajos));
</script>

<template>

    <Head title="Movimientos" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader eyebrow="Registro" title="Gastos y ganancias" />
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
                    v-model:ordenAscendente="ordenAscendente" @change-month="goToMonth" />
                <div class="flex flex-wrap items-center justify-end gap-2">
                    <button v-if="role === 'owner'" type="button" @click="openCreate" class="cs-btn cs-btn-primary">
                        + Nuevo movimiento
                    </button>
                    <a :href="route('transactions.export.excel', { month: selectedMonth, year: selectedYear })"
                        class="cs-btn cs-btn-ghost cs-btn-sm">
                        ↓ Excel
                    </a>
                    <a :href="route('transactions.export.pdf', { month: selectedMonth, year: selectedYear })"
                        class="cs-btn cs-btn-ghost cs-btn-sm">
                        ↓ PDF
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
            :message="transaccionAEliminar ? `Vas a eliminar &quot;${transaccionAEliminar.description || transaccionAEliminar.category.name}&quot;. Esta acción no se puede deshacer.` : ''"
            :processing="eliminando" @cancel="transaccionAEliminar = null" @confirm="confirmarBorrado" />

        <ToastHost />
    </AuthenticatedLayout>
</template>