<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="CheSogue — Tus finanzas en guaraníes" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-900 flex flex-col">
        <header class="max-w-5xl mx-auto w-full px-6 py-6 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-teal-700 flex items-center justify-center text-white font-bold">
                    ₲
                </div>
                <span class="font-semibold text-slate-800 dark:text-slate-100">CheSogue</span>
            </div>

            <div class="flex gap-3">
                <Link
                    v-if="$page.props.auth?.user"
                    :href="route('dashboard')"
                    class="px-4 py-2 rounded-lg bg-teal-700 text-white text-sm font-medium hover:bg-teal-800"
                >
                    Ir a mi panel
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
                    >
                        Iniciar sesión
                    </Link>
                    <Link
                        :href="route('register')"
                        class="px-4 py-2 rounded-lg bg-teal-700 text-white text-sm font-medium hover:bg-teal-800"
                    >
                        Crear cuenta
                    </Link>
                </template>
            </div>
        </header>

        <main class="flex-1 flex items-center">
            <div class="max-w-3xl mx-auto px-6 text-center">
                <h1 class="text-4xl sm:text-5xl font-bold text-slate-800 dark:text-slate-100 mb-4">
                    Tus gastos y ganancias,<br /> en guaraníes.
                </h1>
                <p class="text-lg text-slate-500 dark:text-slate-400 mb-8 max-w-xl mx-auto">
                    Registrá tus movimientos mensuales, seguí tu presupuesto por categoría
                    y compartí tus finanzas con quien quieras — todo en una sola app.
                </p>

                <div class="flex justify-center gap-4">
                    <Link
                        :href="route('register')"
                        class="px-6 py-3 rounded-lg bg-teal-700 text-white font-medium hover:bg-teal-800 transition"
                    >
                        Empezar gratis
                    </Link>
                    <Link
                        :href="route('login')"
                        class="px-6 py-3 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                    >
                        Ya tengo cuenta
                    </Link>
                </div>
            </div>
        </main>

        <footer class="text-center py-6 text-sm text-slate-400 dark:text-slate-500">
            CheSogue · Hecho en Paraguay 🇵🇾
        </footer>
    </div>
</template>