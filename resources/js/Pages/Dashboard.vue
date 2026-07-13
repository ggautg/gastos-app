<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    household: Object,
    resumen: Object,
});

function formatGs(amount) {
    return '₲ ' + new Intl.NumberFormat('es-PY').format(amount);
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Dashboard</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-6">

                <div class="bg-teal-700 rounded-xl p-6 text-white">
                    <p class="text-teal-100 text-sm mb-1">Balance de este mes</p>
                    <p class="text-3xl font-bold">{{ formatGs(resumen.balance) }}</p>
                    <p class="text-teal-100 text-sm mt-2">{{ household.name }}</p>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Ganancias del mes</p>
                        <p class="text-lg font-semibold text-emerald-700 dark:text-emerald-400">
                            {{ formatGs(resumen.ganancias) }}
                        </p>
                    </div>
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Gastos del mes</p>
                        <p class="text-lg font-semibold text-red-700 dark:text-red-400">
                            {{ formatGs(resumen.gastos) }}
                        </p>
                    </div>
                    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Movimientos</p>
                        <p class="text-lg font-semibold text-slate-700 dark:text-slate-200">
                            {{ resumen.movimientos }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <Link
                        :href="route('transactions.index')"
                        class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 hover:border-teal-600 dark:hover:border-teal-500 transition"
                    >
                        <p class="font-medium text-slate-800 dark:text-slate-100">📋 Ver movimientos</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Cargá y revisá tus gastos y ganancias
                        </p>
                    </Link>
                    <Link
                        :href="route('summary.yearly')"
                        class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 hover:border-teal-600 dark:hover:border-teal-500 transition"
                    >
                        <p class="font-medium text-slate-800 dark:text-slate-100">📊 Resumen anual</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Mirá cómo te fue en todo el año
                        </p>
                    </Link>
                    <Link
                        :href="route('categories.index')"
                        class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 hover:border-teal-600 dark:hover:border-teal-500 transition"
                    >
                        <p class="font-medium text-slate-800 dark:text-slate-100">🏷️ Categorías</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Organizá y ponele presupuesto a tus gastos
                        </p>
                    </Link>
                    <Link
                        :href="route('household.show')"
                        class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 hover:border-teal-600 dark:hover:border-teal-500 transition"
                    >
                        <p class="font-medium text-slate-800 dark:text-slate-100">👥 Cuenta compartida</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Invitá a alguien a ver tus finanzas
                        </p>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>