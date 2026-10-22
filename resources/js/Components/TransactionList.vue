<script setup>
import { computed } from 'vue';
import { Copy, Pencil, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    transacciones: Array,
    totalFiltradas: Number,
    hayFiltroActivo: Boolean,
    role: String,
});

const paginaActual = defineModel('paginaActual');
const porPagina = defineModel('porPagina');

const emit = defineEmits(['duplicar', 'editar', 'eliminar']);

const totalPaginas = computed(() => Math.ceil(props.totalFiltradas / porPagina.value));

function formatGs(amount) {
    return '₲ ' + new Intl.NumberFormat('es-PY').format(amount);
}

function formatFecha(fecha) {
    const [year, month, day] = fecha.split('T')[0].split('-');
    return `${day}/${month}/${year}`;
}
</script>

<template>
    <div>
        <div class="cs-list">
            <div v-for="t in transacciones" :key="t.id" class="cs-list-row">
                <div class="flex min-w-0 items-center gap-3">
                    <span v-if="t.category.icon" class="shrink-0 text-lg">{{ t.category.icon }}</span>
                    <div class="min-w-0">
                        <p class="cs-list-title truncate">
                            {{ t.description || t.category.name }}
                        </p>
                        <p class="cs-list-meta">
                            <span
                                class="cs-badge"
                                :style="{ backgroundColor: t.category.color + '18', color: t.category.color }"
                            >
                                {{ t.category.name }}
                            </span>
                            <span aria-hidden="true">·</span>
                            <span>{{ formatFecha(t.date) }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2">
                    <span
                        class="cs-amount text-sm"
                        :style="{ color: t.type === 'gasto' ? 'var(--cs-terra)' : 'var(--cs-emerald)' }"
                    >
                        {{ t.type === 'gasto' ? '−' : '+' }}{{ formatGs(t.amount_gs) }}
                    </span>
                    <div v-if="role === 'owner'" class="flex gap-0.5">
                        <button type="button" @click="emit('duplicar', t)" title="Duplicar" class="cs-icon-btn">
                            <Copy class="h-4 w-4" />
                        </button>
                        <button type="button" @click="emit('editar', t)" title="Editar" class="cs-icon-btn">
                            <Pencil class="h-4 w-4" />
                        </button>
                        <button type="button" @click="emit('eliminar', t)" title="Eliminar" class="cs-icon-btn cs-icon-btn-danger">
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>

            <p v-if="transacciones.length === 0" class="cs-empty">
                {{ hayFiltroActivo ? 'No se encontraron movimientos con ese criterio.' : 'No hay movimientos cargados en este mes.' }}
            </p>
        </div>

        <div v-if="totalPaginas > 1" class="mt-4 flex flex-wrap items-center justify-between gap-2 text-sm">
            <div class="flex items-center gap-2">
                <p style="color: var(--cs-faint);">
                    Página {{ paginaActual }} de {{ totalPaginas }} · {{ totalFiltradas }} movimientos
                </p>
                <select v-model="porPagina" class="cs-input w-auto py-1 text-xs">
                    <option :value="10">10 por página</option>
                    <option :value="25">25 por página</option>
                    <option :value="50">50 por página</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="button" @click="paginaActual--" :disabled="paginaActual === 1" class="cs-btn cs-btn-ghost cs-btn-sm disabled:opacity-40">
                    ← Anterior
                </button>
                <button type="button" @click="paginaActual++" :disabled="paginaActual === totalPaginas" class="cs-btn cs-btn-ghost cs-btn-sm disabled:opacity-40">
                    Siguiente →
                </button>
            </div>
        </div>
    </div>
</template>
