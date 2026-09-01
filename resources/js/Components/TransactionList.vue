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
        <div
            class="rounded-xl shadow-sm divide-y"
            style="background: var(--cs-paper-card); border: 1px solid color-mix(in srgb, var(--cs-ink) 12%, transparent); divide-color: color-mix(in srgb, var(--cs-ink) 8%, transparent);"
        >
            <div v-for="t in transacciones" :key="t.id" class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center gap-3 min-w-0">
                    <span v-if="t.category.icon" class="text-lg shrink-0">{{ t.category.icon }}</span>
                    <div class="min-w-0">
                        <p class="font-medium truncate" style="color: var(--cs-ink);">
                            {{ t.description || t.category.name }}
                        </p>
                        <p class="text-xs flex items-center gap-1.5 mt-0.5 flex-wrap" style="color: color-mix(in srgb, var(--cs-ink) 50%, transparent);">
                            <span class="px-1.5 py-0.5 rounded-full font-medium" :style="{ backgroundColor: t.category.color + '20', color: t.category.color }">
                                {{ t.category.name }}
                            </span>
                            · {{ formatFecha(t.date) }}
                            <span v-if="t.currency === 'USD'">· US$ {{ t.amount }}</span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <span class="font-semibold" style="font-family: 'JetBrains Mono', monospace;" :style="{ color: t.type === 'gasto' ? '#D85A30' : '#1D9E75' }">
                        {{ t.type === 'gasto' ? '-' : '+' }}{{ formatGs(t.amount_gs) }}
                    </span>
                    <div v-if="role === 'owner'" class="flex gap-1">
                        <button type="button" @click="emit('duplicar', t)" title="Duplicar" class="p-2 rounded-lg" style="color: color-mix(in srgb, var(--cs-ink) 40%, transparent);">
                            <Copy class="w-4 h-4" />
                        </button>
                        <button type="button" @click="emit('editar', t)" title="Editar" class="p-2 rounded-lg" style="color: color-mix(in srgb, var(--cs-ink) 40%, transparent);">
                            <Pencil class="w-4 h-4" />
                        </button>
                        <button type="button" @click="emit('eliminar', t)" title="Eliminar" class="p-2 rounded-lg" style="color: #D85A30;">
                            <Trash2 class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            <p v-if="transacciones.length === 0" class="px-4 py-6 text-center text-sm" style="color: color-mix(in srgb, var(--cs-ink) 40%, transparent);">
                {{ hayFiltroActivo ? 'No se encontraron movimientos con ese criterio.' : 'No hay movimientos cargados en este mes.' }}
            </p>
        </div>

        <div v-if="totalPaginas > 1" class="flex items-center justify-between text-sm flex-wrap gap-2 mt-4">
            <div class="flex items-center gap-2">
                <p style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">
                    Página {{ paginaActual }} de {{ totalPaginas }} ({{ totalFiltradas }} movimientos)
                </p>
                <select v-model="porPagina" class="cs-input text-xs py-1">
                    <option :value="10">10 por página</option>
                    <option :value="25">25 por página</option>
                    <option :value="50">50 por página</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="button" @click="paginaActual--" :disabled="paginaActual === 1" class="px-3 py-1.5 rounded-lg border disabled:opacity-40" style="border-color: color-mix(in srgb, var(--cs-ink) 18%, transparent);">
                    ← Anterior
                </button>
                <button type="button" @click="paginaActual++" :disabled="paginaActual === totalPaginas" class="px-3 py-1.5 rounded-lg border disabled:opacity-40" style="border-color: color-mix(in srgb, var(--cs-ink) 18%, transparent);">
                    Siguiente →
                </button>
            </div>
        </div>
    </div>
</template>