<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Iniciar sesión" />

    <GuestLayout>
        <h1 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-1">
            Bienvenido de nuevo
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
            Ingresá para ver tus finanzas
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
                    autocomplete="username"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-teal-600 focus:ring-teal-600"
                />
                <p v-if="form.errors.email" class="text-xs text-red-600 mt-1">{{ form.errors.email }}</p>
            </div>

            <div>
                <label class="block text-sm text-slate-600 dark:text-slate-300 mb-1">Contraseña</label>
                <input
                    v-model="form.password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-teal-600 focus:ring-teal-600"
                />
                <p v-if="form.errors.password" class="text-xs text-red-600 mt-1">{{ form.errors.password }}</p>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    Recordarme
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-teal-700 dark:text-teal-400 hover:underline"
                >
                    ¿Olvidaste tu contraseña?
                </Link>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full py-2.5 rounded-lg bg-teal-700 text-white font-medium hover:bg-teal-800 disabled:opacity-50 transition"
            >
                Iniciar sesión
            </button>

            <p class="text-center text-sm text-slate-500 dark:text-slate-400">
                ¿No tenés cuenta?
                <Link :href="route('register')" class="text-teal-700 dark:text-teal-400 hover:underline font-medium">
                    Registrate
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>