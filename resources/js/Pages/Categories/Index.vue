<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { useToast } from '@/Composables/useToast';

import CategoryList from '@/Components/CategoryList.vue';
import PageHeader from '@/Components/PageHeader.vue';
import CategoryModal from '@/Components/CategoryModal.vue';
import DeleteModal from '@/Components/DeleteModal.vue';
import ToastHost from '@/Components/ToastHost.vue';

defineProps({
    categories: Array,
    role: String,
});

const { mostrarToast } = useToast();

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
            mostrarToast('Categoría eliminada');
        },
        onError: (errors) => mostrarToast(errors.category ?? 'No se pudo eliminar la categoría', 'error'),
        onFinish: () => (eliminando.value = false),
    });
}

</script>

<template>

    <Head title="Categorías" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader eyebrow="Organización" title="Categorías" />
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
            :message="categoriaAEliminar ? `Vas a eliminar &quot;${categoriaAEliminar.name}&quot;. Esta acción no se puede deshacer.` : ''"
            :processing="eliminando" @cancel="categoriaAEliminar = null" @confirm="confirmarBorrado" />

        <ToastHost />
    </AuthenticatedLayout>
</template>