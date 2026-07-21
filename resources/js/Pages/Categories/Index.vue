<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import EmojiPicker from 'vue3-emoji-picker';
import 'vue3-emoji-picker/css';

const props = defineProps({
    categories: Array,
    role: String,
});

const showForm = ref(false);
const editingId = ref(null);

const form = useForm({
    name: '',
    type: 'gasto',
    color: '#0F5257',
    budget: '',
    icon: '',
});

function openCreate() {
    editingId.value = null;
    form.reset();
    form.color = '#0F5257';
    showForm.value = true;
}

function openEdit(category) {
    editingId.value = category.id;
    form.name = category.name;
    form.type = category.type;
    form.color = category.color;
    form.budget = category.budget ?? '';
    form.icon = category.icon ?? '';
    showForm.value = true;
}

const showEmojiPicker = ref(false);

const categoriaAEliminar = ref(null);

function seleccionarEmoji(emoji) {
    form.icon = emoji.i;
    showEmojiPicker.value = false;
}

function submit() {
    if (editingId.value) {
        form.put(route('categories.update', editingId.value), {
            onSuccess: () => (showForm.value = false,
                mostrarToast('Categoría actualizada', 'success')
            ),
        });
    } else {
        form.post(route('categories.store'), {
            onSuccess: () => (showForm.value = false,
                mostrarToast('Categoría creada', 'success')
            ),
        });
    }
}

function pedirConfirmacionBorrado(category) {
    categoriaAEliminar.value = category;
}

function confirmarBorrado() {
    form.delete(route('categories.destroy', categoriaAEliminar.value.id), {
        onSuccess: () => {
            categoriaAEliminar.value = null;
            mostrarToast('Categoría eliminada', 'error');
        },
    });
}

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

const toast = ref(null);

function mostrarToast(mensaje, tipo = 'success') {
    toast.value = { mensaje, tipo };

    setTimeout(() => {
        toast.value = null;
    }, 3000);
}

function manejarEscape(e) {
    if (e.key !== 'Escape') return;

    if (categoriaAEliminar.value) {
        categoriaAEliminar.value = null;
        return;
    }

    if (showEmojiPicker.value) {
        showEmojiPicker.value = false;
        return;
    }

    if (showForm.value) {
        showForm.value = false;
    }
}
onMounted(() => {
    window.addEventListener('keydown', manejarEscape);
});

onUnmounted(() => {
    window.removeEventListener('keydown', manejarEscape);
});
</script>

<template>

    <Head title="Categorías" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-slate-800 dark:text-gray-200">Categorías</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="font-cs-display text-xl font-semibold" style="color: var(--cs-ink);">
                            Categorías
                        </h2>
                        <p class="text-sm mt-1" style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">
                            Organizá tus gastos y ganancias.
                        </p>
                    </div>
                    <button v-if="role === 'owner'" @click="openCreate"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-white transition"
                        style="background: var(--cs-teal);" onmouseover="this.style.background='var(--cs-teal-dark)'"
                        onmouseout="this.style.background='var(--cs-teal)'">
                        + Nueva categoría
                    </button>
                </div>

                <!-- Lista -->
                <div class="rounded-xl shadow-sm divide-y"
                    style="background: var(--cs-paper-card); border: 1px solid color-mix(in srgb, var(--cs-ink) 12%, transparent);">
                    <div v-for="category in categories" :key="category.id"
                        class="flex items-center justify-between px-4 py-3">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full" :style="{ backgroundColor: category.color }"></span>
                            <span v-if="category.icon" class="text-lg">{{ category.icon }}</span>
                            <span class="font-medium" style="color: var(--cs-ink);">{{ category.name }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium" :style="category.type === 'gasto'
                                ? { background: '#D85A3020', color: '#D85A30' }
                                : { background: '#1D9E7520', color: '#1D9E75' }">
                                {{ category.type === 'gasto' ? 'Gasto' : 'Ganancia' }}
                            </span>
                            <span v-if="category.budget" class="text-xs"
                                style="font-family: 'JetBrains Mono', monospace; color: color-mix(in srgb, var(--cs-ink) 50%, transparent);">
                                tope ₲{{ new Intl.NumberFormat('es-PY').format(category.budget) }}
                            </span>
                        </div>
                        <div class="flex gap-3 text-sm">
                            <div v-if="role === 'owner'" class="flex gap-3 text-sm">
                                <button @click="openEdit(category)"
                                    class="text-slate-500 hover:text-slate-800 dark:text-gray-200 dark:hover:text-gray-400">
                                    Editar
                                </button>
                                <button @click="pedirConfirmacionBorrado(category)"
                                    class="text-red-500 hover:text-red-700">
                                    Borrar
                                </button>
                            </div>
                        </div>
                    </div>

                    <p v-if="categories.length === 0"
                        class="px-4 py-6 text-center text-sm text-slate-400 dark:text-gray-400">
                        Todavía no creaste ninguna categoría.
                    </p>
                </div>
            </div>
        </div>

        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="toast"
                class="fixed bottom-6 right-6 px-4 py-3 rounded-lg shadow-lg text-white text-sm font-medium z-50"
               :style="toast.tipo === 'success' ? { background: 'var(--cs-teal)' } : { background: '#D85A30' }">
                {{ toast.mensaje }}
            </div>
        </Transition>

        <!-- Modal simple -->
        <div v-if="showForm" class="fixed inset-0 bg-black/30 flex items-center justify-center px-4 z-50"
            @click.self="showForm = false">
            <div class="rounded-xl shadow-lg p-6 w-full max-w-sm" style="background: var(--cs-paper-card);">
                <h3 class="font-cs-display text-lg font-semibold mb-4" style="color: var(--cs-ink);">
                    {{ editingId ? 'Editar categoría' : 'Nueva categoría' }}
                </h3>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="cs-label">Nombre</label>
                        <input v-model="form.name" type="text" class="cs-input w-full" placeholder="Ej: Comida" />
                        <p v-if="form.errors.name" class="text-xs text-red-600 mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-600 dark:text-gray-400 mb-1">Tipo</label>
                        <select v-model="form.type"
                            class="w-full rounded-lg border-slate-300 focus:border-teal-600 focus:ring-teal-600 dark:bg-slate-700 dark:border-slate-600 dark:text-gray-200">
                            <option value="gasto">Gasto</option>
                            <option value="ganancia">Ganancia</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-600 dark:text-slate-300 mb-1">
                            Ícono <span class="text-slate-400">— opcional</span>
                        </label>

                        <div class="relative">
                            <button type="button" @click="showEmojiPicker = !showEmojiPicker"
                                class="w-full flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700">
                                <span v-if="form.icon" class="text-lg">{{ form.icon }}</span>
                                <span class="text-slate-500 dark:text-slate-400">
                                    {{ form.icon ? 'Cambiar emoji' : 'Elegir emoji' }}
                                </span>
                            </button>

                            <div v-if="showEmojiPicker" class="absolute z-50 mt-1">
                                <EmojiPicker :native="true" @select="seleccionarEmoji" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-600 dark:text-gray-400 mb-1">Color</label>
                        <input v-model="form.color" type="color"
                            class="w-14 h-9 rounded border-slate-300 dark:border-slate-600" />
                    </div>

                    <div v-if="form.type === 'gasto'">
                        <label class="block text-sm text-slate-600 dark:text-gray-400 mb-1">
                            Presupuesto mensual (₲) <span class="text-slate-400">— opcional</span>
                        </label>
                        <input v-model="budgetDisplay" type="text" inputmode="numeric" placeholder="Ej: 500.000"
                            class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-teal-600 focus:ring-teal-600" />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showForm = false" class="px-4 py-2 text-sm"
                            style="color: color-mix(in srgb, var(--cs-ink) 65%, transparent);">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="form.processing" class="cs-submit"
                            style="width: auto; padding: 0.55rem 1.1rem;">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="categoriaAEliminar" class="fixed inset-0 bg-black/30 flex items-center justify-center px-4 z-50"
            @click.self="categoriaAEliminar = null">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg p-6 w-full max-w-sm">
                <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">
                    ¿Eliminar categoría?
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">
                    Vas a borrar <span class="font-medium">"{{ categoriaAEliminar.name }}"</span>.
                    Esta acción no se puede deshacer.
                </p>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="categoriaAEliminar = null"
                        class="px-4 py-2 text-sm text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100">
                        Cancelar
                    </button>
                    <button type="button" @click="confirmarBorrado" :disabled="form.processing"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>