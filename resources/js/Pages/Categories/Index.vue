<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { useToast } from '@/Composables/useToast';

import CategoryList from '@/Components/CategoryList.vue';
import CategoryModal from '@/Components/CategoryModal.vue';
import DeleteModal from '@/Components/DeleteModal.vue';

defineProps({
    categories: Array,
    role: String,
});

const { toast, mostrarToast } = useToast();

const showForm = ref(false);
const editingCategory = ref(null);

function openCreate() {
    editingCategory.value = null;
    showForm.value = true;
}

function openEdit(category) {
    editingCategory.value = category;
    showForm.value = true;
}

function onSaved(mensaje) {
    showForm.value = false;
    mostrarToast(mensaje);
}

const categoriaAEliminar = ref(null);
const eliminando = ref(false);

function confirmarBorrado() {
    eliminando.value = true;
    router.delete(route('categories.destroy', categoriaAEliminar.value.id), {
        onSuccess: () => {
            categoriaAEliminar.value = null;
            mostrarToast('Categoría eliminada', 'error');
        },
        onFinish: () => (eliminando.value = false),
    });
}

function manejarEscape(e) {
    if (e.key !== 'Escape') return;

    if (categoriaAEliminar.value) {
        categoriaAEliminar.value = null;
        return;
    }

    if (showForm.value) {
        showForm.value = false;
        return;
    }
}

onMounted(() => window.addEventListener('keydown', manejarEscape));
onUnmounted(() => window.removeEventListener('keydown', manejarEscape));
</script>

<template>

    <Head title="Categorías" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-cs-display text-xl font-semibold" style="color: var(--cs-ink);">Categorías</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <p class="text-sm" style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">
                        Organizá tus gastos y ganancias.
                    </p>
                    <button v-if="role === 'owner'" @click="openCreate"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-white transition"
                        style="background: var(--cs-teal);">
                        + Nueva categoría
                    </button>
                </div>

                <CategoryList :categories="categories" :role="role" @editar="openEdit"
                    @eliminar="c => categoriaAEliminar = c" />
            </div>
        </div>

        <CategoryModal :show="showForm" :editing-category="editingCategory" @close="showForm = false"
            @saved="onSaved" />

        <DeleteModal :show="!!categoriaAEliminar" title="¿Eliminar categoría?"
            :message="categoriaAEliminar ? `Vas a borrar &quot;${categoriaAEliminar.name}&quot;. Esta acción no se puede deshacer.` : ''"
            :processing="eliminando" @cancel="categoriaAEliminar = null" @confirm="confirmarBorrado" />

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