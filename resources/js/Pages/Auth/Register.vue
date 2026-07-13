<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Crear cuenta" />

    <GuestLayout>
        <h1 class="text-xl font-semibold text-slate-800 dark:text-slate-100 mb-1">
            Creá tu cuenta
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
            Empezá a registrar tus finanzas en un minuto
        </p>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block text-sm text-slate-600 dark:text-slate-300 mb-1">Nombre</label>
                <input
                    v-model="form.name"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-teal-600 focus:ring-teal-600"
                />
                <p v-if="form.errors.name" class="text-xs text-red-600 mt-1">{{ form.errors.name }}</p>
            </div>

            <div>
                <label class="block text-sm text-slate-600 dark:text-slate-300 mb-1">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    required
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
                    autocomplete="new-password"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-teal-600 focus:ring-teal-600"
                />
                <p v-if="form.errors.password" class="text-xs text-red-600 mt-1">{{ form.errors.password }}</p>
            </div>

            <div>
                <label class="block text-sm text-slate-600 dark:text-slate-300 mb-1">Confirmar contraseña</label>
                <input
                    v-model="form.password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-teal-600 focus:ring-teal-600"
                />
                <p v-if="form.errors.password_confirmation" class="text-xs text-red-600 mt-1">
                    {{ form.errors.password_confirmation }}
                </p>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full py-2.5 rounded-lg bg-teal-700 text-white font-medium hover:bg-teal-800 disabled:opacity-50 transition"
            >
                Crear cuenta
            </button>

            <p class="text-center text-sm text-slate-500 dark:text-slate-400">
                ¿Ya tenés cuenta?
                <Link :href="route('login')" class="text-teal-700 dark:text-teal-400 hover:underline font-medium">
                    Iniciar sesión
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>