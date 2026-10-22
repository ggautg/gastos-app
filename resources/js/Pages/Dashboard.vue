<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowUpRight,
    BarChart3,
    ClipboardList,
    Tags,
    Users,
    Wallet,
} from 'lucide-vue-next';

defineProps({
    household: Object,
    resumen: Object,
});

const accesos = [
    {
        route: 'transactions.index',
        title: 'Ver movimientos',
        text: 'Cargá y revisá tus gastos y ganancias.',
        icon: ClipboardList,
    },
    {
        route: 'summary.yearly',
        title: 'Resumen anual',
        text: 'Mirá cómo te fue en todo el año.',
        icon: BarChart3,
    },
    {
        route: 'categories.index',
        title: 'Categorías',
        text: 'Organizá y ponele presupuesto a tus gastos.',
        icon: Tags,
    },
    {
        route: 'household.show',
        title: 'Cuenta compartida',
        text: 'Invitá a alguien a ver tus finanzas.',
        icon: Users,
    },
];

function formatGs(amount) {
    return '₲ ' + new Intl.NumberFormat('es-PY').format(amount);
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div class="space-y-1.5">
                    <p class="cs-eyebrow">Panorama</p>
                    <h2 class="cs-page-title">Dashboard</h2>
                </div>
                <p class="text-sm cs-muted">{{ household.name }}</p>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl space-y-5 px-4 sm:px-6 lg:px-8">
                <section class="cs-hero cs-reveal">
                    <div class="flex items-start justify-between gap-6">
                        <div class="min-w-0">
                            <p class="cs-hero-label">Balance de este mes</p>
                            <p class="cs-hero-amount mt-2">{{ formatGs(resumen.balance) }}</p>
                            <div class="cs-hero-meta mt-4">
                                <span class="cs-hero-chip">{{ household.name }}</span>
                                <span>{{ resumen.movimientos }} movimientos registrados</span>
                            </div>
                        </div>

                        <div
                            class="hidden h-14 w-14 shrink-0 items-center justify-center rounded-full border sm:flex"
                            style="border-color: rgba(227, 198, 133, 0.28); background: rgba(8, 30, 26, 0.3);"
                        >
                            <Wallet class="h-6 w-6" style="color: var(--cs-gold-light);" />
                        </div>
                    </div>
                </section>

                <section class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="cs-kpi cs-reveal cs-reveal-1">
                        <p class="cs-kpi-label">Ganancias del mes</p>
                        <p class="cs-kpi-value" style="color: var(--cs-emerald);">
                            {{ formatGs(resumen.ganancias) }}
                        </p>
                    </div>
                    <div class="cs-kpi cs-reveal cs-reveal-2">
                        <p class="cs-kpi-label">Gastos del mes</p>
                        <p class="cs-kpi-value" style="color: var(--cs-terra);">
                            {{ formatGs(resumen.gastos) }}
                        </p>
                    </div>
                    <div class="cs-kpi cs-reveal cs-reveal-3">
                        <p class="cs-kpi-label">Movimientos</p>
                        <p class="cs-kpi-value">{{ resumen.movimientos }}</p>
                    </div>
                </section>

                <section class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <Link
                        v-for="(acceso, i) in accesos"
                        :key="acceso.route"
                        :href="route(acceso.route)"
                        class="cs-tile cs-reveal"
                        :style="{ animationDelay: `${i * 50 + 60}ms` }"
                    >
                        <span
                            class="mb-1.5 inline-flex h-8 w-8 items-center justify-center rounded-lg"
                            style="background: color-mix(in srgb, var(--cs-teal) 10%, transparent); color: var(--cs-teal);"
                        >
                            <component :is="acceso.icon" class="h-4 w-4" />
                        </span>
                        <p class="cs-tile-title">{{ acceso.title }}</p>
                        <p class="cs-tile-text">{{ acceso.text }}</p>
                        <ArrowUpRight class="cs-tile-arrow h-4 w-4" />
                    </Link>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
