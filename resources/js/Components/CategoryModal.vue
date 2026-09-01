<script setup>
import { computed, watch, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import EmojiPickerButton from './EmojiPickerButton.vue';

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
    if (!abierto) {
        return;
    }

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

const budgetDisplay = computed({
    get() {
        if (!form.budget) return '';
        return new Intl.NumberFormat('es-PY').format(form.budget);
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
    <div
        v-if="show"
        class="fixed inset-0 bg-black/30 flex items-center justify-center px-4 z-50"
        @click.self="!showEmojiPicker && emit('close')"
    >
        <div class="rounded-xl shadow-lg p-6 w-full max-w-sm" style="background: var(--cs-paper-card);">
            <h3 class="font-cs-display text-lg font-semibold mb-4" style="color: var(--cs-ink);">
                {{ editingCategory ? 'Editar categoría' : 'Nueva categoría' }}
            </h3>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="cs-label">Nombre</label>
                    <input v-model="form.name" type="text" class="cs-input w-full" placeholder="Ej: Comida" />
                    <p v-if="form.errors.name" class="cs-error">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="cs-label">Tipo</label>
                    <select v-model="form.type" class="cs-input w-full">
                        <option value="gasto">Gasto</option>
                        <option value="ganancia">Ganancia</option>
                    </select>
                </div>

                <div>
                    <label class="cs-label">Ícono <span style="opacity: 0.6;">— opcional</span></label>
                    <EmojiPickerButton v-model="form.icon" v-model:show-picker="showEmojiPicker" />
                </div>

                <div>
                    <label class="cs-label">Color</label>
                    <input v-model="form.color" type="color" class="w-14 h-9 rounded border-slate-300 dark:border-slate-600" />
                </div>

                <div v-if="form.type === 'gasto'">
                    <label class="cs-label">Presupuesto mensual (₲) <span style="opacity: 0.6;">— opcional</span></label>
                    <input v-model="budgetDisplay" type="text" inputmode="numeric" placeholder="Ej: 500.000" class="cs-input w-full" />
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="emit('close')" class="px-4 py-2 text-sm" style="color: color-mix(in srgb, var(--cs-ink) 65%, transparent);">
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