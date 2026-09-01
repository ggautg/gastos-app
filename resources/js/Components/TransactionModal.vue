<script setup>
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
    editingTransaction: Object,
    categories: Array,
});

const emit = defineEmits(['close', 'saved']);

function hoyLocal() {
    const d = new Date();
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

const form = useForm({
    category_id: '',
    type: 'gasto',
    amount: '',
    currency: 'PYG',
    description: '',
    date: hoyLocal(),
});

// Cada vez que se abre el modal, cargamos los datos según si es edición o creación
watch(() => props.show, (abierto) => {
    if (!abierto) return;

    form.clearErrors();

    if (props.editingTransaction) {
        const t = props.editingTransaction;
        form.category_id = t.category_id;
        form.type = t.type;
        form.amount = t.amount;
        form.currency = t.currency;
        form.description = t.description;
        form.date = t.id ? t.date.split('T')[0] : hoyLocal();
    } else {
        form.category_id = '';
        form.type = 'gasto';
        form.amount = '';
        form.currency = 'PYG';
        form.description = '';
        form.date = hoyLocal();
    }
}, { flush: 'post' });

const filteredCategories = computed(() =>
    props.categories.filter(c => c.type === form.type)
);

function formatMilesManual(numStr) {
    const [parteEntera, parteDecimal] = String(numStr).split('.');
    const enteraFormateada = parteEntera.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return parteDecimal !== undefined ? `${enteraFormateada}.${parteDecimal}` : enteraFormateada;
}

const amountDisplay = computed({
    get() {
        if (form.amount === '' || form.amount === null) return '';
        if (form.currency === 'USD') return formatMilesManual(form.amount);
        return new Intl.NumberFormat('es-PY').format(form.amount);
    },
    set(valor) {
        if (form.currency === 'USD') {
            let limpio = valor.replace(/[^\d.]/g, '');
            const partes = limpio.split('.');
            if (partes.length > 2) limpio = partes[0] + '.' + partes.slice(1).join('');
            const partesFinal = limpio.split('.');
            if (partesFinal.length === 2 && partesFinal[1].length > 2) {
                limpio = partesFinal[0] + '.' + partesFinal[1].slice(0, 2);
            }
            form.amount = limpio;
        } else {
            const limpio = valor.replace(/\D/g, '');
            form.amount = limpio ? parseInt(limpio) : '';
        }
    },
});

function submit() {
    if (props.editingTransaction) {
        form.put(route('transactions.update', props.editingTransaction.id), {
            onSuccess: () => emit('saved', 'Movimiento actualizado'),
        });
    } else {
        form.post(route('transactions.store'), {
            onSuccess: () => emit('saved', 'Movimiento creado'),
        });
    }
}
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 bg-black/30 flex items-center justify-center px-4 z-50"
        @click.self="emit('close')"
    >
        <div class="rounded-xl shadow-lg p-6 w-full max-w-sm" style="background: var(--cs-paper-card);">
            <h3 class="font-cs-display text-lg font-semibold mb-4" style="color: var(--cs-ink);">
                {{ editingTransaction ? 'Editar movimiento' : 'Nuevo movimiento' }}
            </h3>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="flex gap-2">
                    <button
                        type="button"
                        @click="form.type = 'gasto'; form.category_id = ''"
                        class="flex-1 py-2 rounded-lg text-sm font-medium border"
                        :style="form.type === 'gasto'
                            ? { background: '#D85A3015', borderColor: '#D85A30', color: '#D85A30' }
                            : { borderColor: 'color-mix(in srgb, var(--cs-ink) 18%, transparent)', color: 'color-mix(in srgb, var(--cs-ink) 55%, transparent)' }"
                    >
                        Gasto
                    </button>
                    <button
                        type="button"
                        @click="form.type = 'ganancia'; form.category_id = ''"
                        class="flex-1 py-2 rounded-lg text-sm font-medium border"
                        :style="form.type === 'ganancia'
                            ? { background: '#1D9E7515', borderColor: '#1D9E75', color: '#1D9E75' }
                            : { borderColor: 'color-mix(in srgb, var(--cs-ink) 18%, transparent)', color: 'color-mix(in srgb, var(--cs-ink) 55%, transparent)' }"
                    >
                        Ganancia
                    </button>
                </div>

                <div>
                    <label class="cs-label">Categoría</label>
                    <select v-model="form.category_id" class="cs-input w-full">
                        <option value="" disabled>Elegí una categoría</option>
                        <option v-for="c in filteredCategories" :key="c.id" :value="c.id">
                            {{ c.icon ? c.icon + ' ' : '' }}{{ c.name }}
                        </option>
                    </select>
                    <p v-if="form.errors.category_id" class="cs-error">{{ form.errors.category_id }}</p>
                </div>

                <div>
                    <label class="cs-label">Monto</label>
                    <div class="flex gap-2">
                        <select v-model="form.currency" @change="form.amount = ''" class="cs-input text-sm">
                            <option value="PYG">₲</option>
                            <option value="USD">US$</option>
                        </select>
                        <input v-model="amountDisplay" type="text" inputmode="decimal" class="cs-input flex-1"
                               :placeholder="form.currency === 'USD' ? 'Ej: 49.99' : 'Ej: 150.000'" />
                    </div>
                    <p v-if="form.errors.amount" class="cs-error">{{ form.errors.amount }}</p>
                </div>

                <div>
                    <label class="cs-label">Descripción (opcional)</label>
                    <input v-model="form.description" type="text" class="cs-input w-full" placeholder="Ej: Supermercado" />
                </div>

                <div>
                    <label class="cs-label">Fecha</label>
                    <input v-model="form.date" type="date" class="cs-input w-full" />
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="emit('close')" class="px-4 py-2 text-sm" style="color: color-mix(in srgb, var(--cs-ink) 60%, transparent);">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="form.processing" class="cs-submit" style="width: auto; padding: 0.55rem 1.1rem;">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>