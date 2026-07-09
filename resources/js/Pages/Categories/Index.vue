<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    categories: Array,
});

const showForm = ref(false);
const editingId = ref(null);

const form = useForm({
    name: '',
    type: 'gasto',
    color: '#0F5257',
    budget: '',
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
    showForm.value = true;
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

function remove(category) {
    if (confirm(`¿Borrar la categoría "${category.name}"?`)) {
        form.delete(route('categories.destroy', category.id), {
            onSuccess: () => mostrarToast('Categoría eliminada', 'error'),
        });
    }
}

const toast = ref(null);

function mostrarToast(mensaje, tipo = 'success') {
    toast.value = { mensaje, tipo };

    setTimeout(() => {
        toast.value = null;
    }, 3000);
}
</script>

<template>

    <Head title="Categorías" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-slate-800">Categorías</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <p class="text-sm text-slate-500">
                        Organizá tus gastos y ganancias por categoría.
                    </p>
                    <button @click="openCreate"
                        class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-medium text-white hover:bg-teal-800 transition">
                        + Nueva categoría
                    </button>
                </div>

                <!-- Lista -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 divide-y divide-slate-100">
                    <div v-for="category in categories" :key="category.id"
                        class="flex items-center justify-between px-4 py-3">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full" :style="{ backgroundColor: category.color }"></span>
                            <span class="font-medium text-slate-800">{{ category.name }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full" :class="category.type === 'gasto'
                                ? 'bg-red-50 text-red-700'
                                : 'bg-emerald-50 text-emerald-700'">
                                {{ category.type === 'gasto' ? 'Gasto' : 'Ganancia' }}
                            </span>
                        </div>
                        <div class="flex gap-3 text-sm">
                            <button @click="openEdit(category)" class="text-slate-500 hover:text-slate-800">
                                Editar
                            </button>
                            <button @click="remove(category)" class="text-red-500 hover:text-red-700">
                                Borrar
                            </button>
                        </div>
                    </div>

                    <p v-if="categories.length === 0" class="px-4 py-6 text-center text-sm text-slate-400">
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
                :class="toast.tipo === 'success' ? 'bg-emerald-600' : 'bg-red-600'">
                {{ toast.mensaje }}
            </div>
        </Transition>

        <!-- Modal simple -->
        <div v-if="showForm" class="fixed inset-0 bg-black/30 flex items-center justify-center px-4 z-50"
            @click.self="showForm = false">
            <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">
                    {{ editingId ? 'Editar categoría' : 'Nueva categoría' }}
                </h3>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm text-slate-600 mb-1">Nombre</label>
                        <input v-model="form.name" type="text"
                            class="w-full rounded-lg border-slate-300 focus:border-teal-600 focus:ring-teal-600"
                            placeholder="Ej: Comida" />
                        <p v-if="form.errors.name" class="text-xs text-red-600 mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-600 mb-1">Tipo</label>
                        <select v-model="form.type"
                            class="w-full rounded-lg border-slate-300 focus:border-teal-600 focus:ring-teal-600">
                            <option value="gasto">Gasto</option>
                            <option value="ganancia">Ganancia</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-600 mb-1">Color</label>
                        <input v-model="form.color" type="color" class="w-14 h-9 rounded border-slate-300" />
                    </div>
                    <div v-if="form.type === 'gasto'">
                        <label class="block text-sm text-slate-600 mb-1">
                            Presupuesto mensual (₲) <span class="text-slate-400">— opcional</span>
                        </label>
                        <input v-model="form.budget" type="number" min="0"
                            class="w-full rounded-lg border-slate-300 focus:border-teal-600 focus:ring-teal-600"
                            placeholder="Ej: 500000" />
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