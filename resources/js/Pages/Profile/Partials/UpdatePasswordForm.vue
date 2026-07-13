<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header class="mb-4">
            <h2 class="text-lg font-medium text-slate-800 dark:text-slate-100">Cambiar contraseña</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Usá una contraseña larga y única para mantener tu cuenta segura.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="space-y-4">
            <div>
                <label class="block text-sm text-slate-600 dark:text-slate-300 mb-1">Contraseña actual</label>
                <input
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    autocomplete="current-password"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-teal-600 focus:ring-teal-600"
                />
                <p v-if="form.errors.current_password" class="text-xs text-red-600 mt-1">
                    {{ form.errors.current_password }}
                </p>
            </div>

            <div>
                <label class="block text-sm text-slate-600 dark:text-slate-300 mb-1">Nueva contraseña</label>
                <input
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-teal-600 focus:ring-teal-600"
                />
                <p v-if="form.errors.password" class="text-xs text-red-600 mt-1">{{ form.errors.password }}</p>
            </div>

            <div>
                <label class="block text-sm text-slate-600 dark:text-slate-300 mb-1">Confirmar nueva contraseña</label>
                <input
                    v-model="form.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-teal-600 focus:ring-teal-600"
                />
                <p v-if="form.errors.password_confirmation" class="text-xs text-red-600 mt-1">
                    {{ form.errors.password_confirmation }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-4 py-2 rounded-lg bg-teal-700 text-white text-sm font-medium hover:bg-teal-800 disabled:opacity-50"
                >
                    Guardar
                </button>
                <p v-if="form.recentlySuccessful" class="text-sm text-emerald-600 dark:text-emerald-400">
                    Guardado ✓
                </p>
            </div>
        </form>
    </section>
</template>