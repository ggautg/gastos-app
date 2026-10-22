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
            <div class="space-y-1.5">
                <p class="cs-eyebrow">Organización</p>
                <h2 class="cs-page-title">Categorías</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm cs-muted">
                        Organizá tus gastos y ganancias.
                    </p>
                    <button v-if="role === 'owner'" @click="openCreate" class="cs-btn cs-btn-primary">
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
                class="cs-toast"
                :class="toast.tipo === 'success' ? '' : 'cs-toast-error'">
                {{ toast.mensaje }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>