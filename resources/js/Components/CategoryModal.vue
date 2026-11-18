<script setup>
import { computed, watch, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppModal from './AppModal.vue';
import EmojiPickerButton from './EmojiPickerButton.vue';
import { formatNumero } from '@/Composables/useFormat';

const props = defineProps({
    show: Boolean,
    editingCategory: Object,
});

const emit = defineEmits(['close', 'saved']);

const showEmojiPicker = ref(false);

const form = useForm({
    name: '',
    type: 'gasto',
    color: '#0F5257',
    budget: '',
    icon: '',
});

watch(() => props.show, (abierto) => {
    if (!abierto) return;

    form.clearErrors();
    showEmojiPicker.value = false;

    if (props.editingCategory && props.editingCategory.id) {
        const c = props.editingCategory;
        form.name = c.name;
        form.type = c.type;
        form.color = c.color;
        form.budget = c.budget ?? '';
        form.icon = c.icon ?? '';
    } else {
        form.name = '';
        form.type = 'gasto';
        form.color = '#0F5257';
        form.budget = '';
        form.icon = '';
    }
}, { flush: 'post' });

// Igual que en movimientos: se muestra con miles y se guarda sin separadores.
const budgetDisplay = computed({
    get() {
        if (!form.budget) return '';
        return formatNumero(form.budget);
    },
    set(valor) {
        const limpio = valor.replace(/\D/g, '');
        form.budget = limpio ? parseInt(limpio) : '';
    },
});

function submit() {
    if (props.editingCategory) {
        form.put(route('categories.update', props.editingCategory.id), {
            onSuccess: () => emit('saved', 'Categoría actualizada'),
        });
    } else {
        form.post(route('categories.store'), {
            onSuccess: () => emit('saved', 'Categoría creada'),
        });
    }
}
</script>

<template>
    <AppModal :show="show" :title="editingCategory ? 'Editar categoría' : 'Nueva categoría'"
        :close-on-backdrop="!showEmojiPicker" @close="emit('close')">
        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="cs-label" for="category-name">Nombre</label>
                <input id="category-name" v-model="form.name" type="text" class="cs-input w-full" placeholder="Ej: Comida" />
                <p v-if="form.errors.name" class="cs-error">{{ form.errors.name }}</p>
            </div>

            <div>
                <label class="cs-label" for="category-type">Tipo</label>
                <select id="category-type" v-model="form.type" class="cs-input w-full"
                    :disabled="editingCategory?.transactions_count > 0"
                    :title="editingCategory?.transactions_count > 0 ? 'No podés cambiar el tipo: la categoría ya tiene movimientos' : null">
                    <option value="gasto">Gasto</option>
                    <option value="ganancia">Ganancia</option>
                </select>
                <p v-if="form.errors.type" class="cs-error">{{ form.errors.type }}</p>
            </div>

            <div>
                <label class="cs-label">Ícono <span style="opacity: 0.6;">— opcional</span></label>
                <EmojiPickerButton v-model="form.icon" v-model:show-picker="showEmojiPicker" />
            </div>

            <div>
                <label class="cs-label" for="category-color">Color</label>
                <input id="category-color" v-model="form.color" type="color" aria-label="Color de la categoría"
                    class="h-9 w-14 cursor-pointer rounded-lg border p-1"
                    style="border-color: var(--cs-line-strong); background: var(--cs-paper-card);" />
            </div>

            <div v-if="form.type === 'gasto'">
                <label class="cs-label" for="category-budget">Presupuesto mensual (₲) <span style="opacity: 0.6;">— opcional</span></label>
                <input id="category-budget" v-model="budgetDisplay" type="text" inputmode="numeric"
                    placeholder="Ej: 500.000" class="cs-input w-full" />
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
