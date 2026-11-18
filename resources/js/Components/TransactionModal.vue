<script setup>
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppModal from './AppModal.vue';
import { formatNumero } from '@/Composables/useFormat';

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
    description: '',
    date: hoyLocal(),
});

watch(() => props.show, (abierto) => {
    if (!abierto) return;

    form.clearErrors();

    if (props.editingTransaction) {
        const t = props.editingTransaction;
        form.category_id = t.category_id;
        form.type = t.type;
        form.amount = t.amount;
        form.description = t.description;
        form.date = t.id ? t.date.split('T')[0] : hoyLocal();
    } else {
        form.category_id = '';
        form.type = 'gasto';
        form.amount = '';
        form.description = '';
        form.date = hoyLocal();
    }
}, { flush: 'post' });

const filteredCategories = computed(() =>
    props.categories.filter(c => c.type === form.type)
);

// El input muestra miles, pero el formulario guarda solo dígitos.
const amountDisplay = computed({
    get() {
        if (form.amount === '' || form.amount === null) return '';
        return formatNumero(form.amount);
    },
    set(valor) {
        const limpio = valor.replace(/\D/g, '');
        form.amount = limpio ? parseInt(limpio) : '';
    },
});

function submit() {
    if (props.editingTransaction && props.editingTransaction.id) {
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
    <AppModal :show="show" :title="editingTransaction ? 'Editar movimiento' : 'Nuevo movimiento'" @close="emit('close')">
        <form @submit.prevent="submit" class="space-y-4">
            <div class="cs-segment">
                <button type="button" @click="form.type = 'gasto'; form.category_id = ''"
                    class="cs-segment-btn" :class="{ 'is-active': form.type === 'gasto' }"
                    style="--seg-accent: var(--cs-terra);">
                    Gasto
                </button>
                <button type="button" @click="form.type = 'ganancia'; form.category_id = ''"
                    class="cs-segment-btn" :class="{ 'is-active': form.type === 'ganancia' }"
                    style="--seg-accent: var(--cs-emerald);">
                    Ganancia
                </button>
            </div>

            <div>
                <label class="cs-label" for="transaction-category">Categoría</label>
                <select id="transaction-category" v-model="form.category_id" class="cs-input w-full">
                    <option value="" disabled>Elegí una categoría</option>
                    <option v-for="c in filteredCategories" :key="c.id" :value="c.id">
                        {{ c.icon ? c.icon + ' ' : '' }}{{ c.name }}
                    </option>
                </select>
                <p v-if="form.errors.category_id" class="cs-error">{{ form.errors.category_id }}</p>
            </div>

            <div>
                <label class="cs-label" for="transaction-amount">Monto (₲)</label>
                <input id="transaction-amount" v-model="amountDisplay" type="text" inputmode="numeric"
                    class="cs-input w-full" placeholder="Ej: 150.000" />
                <p v-if="form.errors.amount" class="cs-error">{{ form.errors.amount }}</p>
            </div>

            <div>
                <label class="cs-label" for="transaction-description">Descripción (opcional)</label>
                <input id="transaction-description" v-model="form.description" type="text" class="cs-input w-full"
                    placeholder="Ej: Supermercado" />
            </div>

            <div>
                <label class="cs-label" for="transaction-date">Fecha</label>
                <input id="transaction-date" v-model="form.date" type="date" class="cs-input w-full" />
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="emit('close')" class="cs-btn cs-btn-ghost">Cancelar</button>
                <button type="submit" :disabled="form.processing" class="cs-submit"
                    style="width: auto; padding: 0.62rem 1.15rem;">
                    Guardar
                </button>
            </div>
        </form>
    </AppModal>
</template>
