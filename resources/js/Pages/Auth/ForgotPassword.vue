<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Recuperar contraseña" />

    <GuestLayout>
        <h1 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-1">
            ¿Olvidaste tu contraseña?
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
            Ingresá tu email y te mandamos un link para crear una nueva.
        </p>

        <div v-if="status" class="mb-4 text-sm font-medium text-emerald-600 dark:text-emerald-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block text-sm text-slate-600 dark:text-slate-300 mb-1">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    required
                    autofocus
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-teal-600 focus:ring-teal-600"
                />
                <p v-if="form.errors.email" class="text-xs text-red-600 mt-1">{{ form.errors.email }}</p>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full py-2.5 rounded-lg bg-teal-700 text-white font-medium hover:bg-teal-800 disabled:opacity-50 transition"
            >
                Enviar link de recuperación
            </button>
        </form>
    </GuestLayout>
</template>