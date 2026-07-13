<script setup>
import { nextTick, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
};
</script>

<template>
    <section>
        <header class="mb-4">
            <h2 class="text-lg font-medium text-red-700 dark:text-red-400">Eliminar cuenta</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Una vez que borres tu cuenta, todos tus datos se pierden permanentemente. Descargá cualquier información que quieras conservar antes de continuar.
            </p>
        </header>

        <button
            @click="confirmUserDeletion"
            class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700"
        >
            Eliminar cuenta
        </button>

        <div
            v-if="confirmingUserDeletion"
            class="fixed inset-0 bg-black/30 flex items-center justify-center px-4 z-50"
            @click.self="closeModal"
        >
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">
                    ¿Seguro que querés eliminar tu cuenta?
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                    Esta acción no se puede deshacer. Ingresá tu contraseña para confirmar.
                </p>

                <form @submit.prevent="deleteUser">
                    <input
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        placeholder="Contraseña"
                        class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-red-600 focus:ring-red-600"
                    />
                    <p v-if="form.errors.password" class="text-xs text-red-600 mt-1">{{ form.errors.password }}</p>

                    <div class="flex justify-end gap-2 mt-4">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 text-sm text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50"
                        >
                            Eliminar cuenta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</template>