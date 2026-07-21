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
            <h2 class="font-cs-display text-xl font-semibold" style="color: var(--cs-ink);">
                Dashboard
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-6">

                <div
                    class="rounded-xl p-6 text-white"
                    style="background: var(--cs-teal);"
                >
                    <p class="text-sm mb-1" style="color: color-mix(in srgb, white 75%, transparent);">
                        Balance de este mes
                    </p>
                    <p class="text-3xl font-semibold" style="font-family: 'JetBrains Mono', monospace;">
                        {{ formatGs(resumen.balance) }}
                    </p>
                    <p class="text-sm mt-2" style="color: color-mix(in srgb, white 75%, transparent);">
                        {{ household.name }}
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="rounded-xl border p-4" style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);">
                        <p class="text-xs mb-1" style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">Ganancias del mes</p>
                        <p class="text-lg font-semibold" style="font-family: 'JetBrains Mono', monospace; color: #1D9E75;">
                            {{ formatGs(resumen.ganancias) }}
                        </p>
                    </div>
                    <div class="rounded-xl border p-4" style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);">
                        <p class="text-xs mb-1" style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">Gastos del mes</p>
                        <p class="text-lg font-semibold" style="font-family: 'JetBrains Mono', monospace; color: #D85A30;">
                            {{ formatGs(resumen.gastos) }}
                        </p>
                    </div>
                    <div class="rounded-xl border p-4" style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);">
                        <p class="text-xs mb-1" style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">Movimientos</p>
                        <p class="text-lg font-semibold" style="font-family: 'JetBrains Mono', monospace; color: var(--cs-ink);">
                            {{ resumen.movimientos }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <Link
                        :href="route('transactions.index')"
                        class="rounded-xl border p-5 transition"
                        style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);"
                        onmouseover="this.style.borderColor='var(--cs-teal)'"
                        onmouseout="this.style.borderColor='color-mix(in srgb, var(--cs-ink) 12%, transparent)'"
                    >
                        <p class="font-medium" style="color: var(--cs-ink);">📋 Ver movimientos</p>
                        <p class="text-sm mt-1" style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">
                            Cargá y revisá tus gastos y ganancias
                        </p>
                    </Link>
                    <Link
                        :href="route('summary.yearly')"
                        class="rounded-xl border p-5 transition"
                        style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);"
                        onmouseover="this.style.borderColor='var(--cs-teal)'"
                        onmouseout="this.style.borderColor='color-mix(in srgb, var(--cs-ink) 12%, transparent)'"
                    >
                        <p class="font-medium" style="color: var(--cs-ink);">📊 Resumen anual</p>
                        <p class="text-sm mt-1" style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">
                            Mirá cómo te fue en todo el año
                        </p>
                    </Link>
                    <Link
                        :href="route('categories.index')"
                        class="rounded-xl border p-5 transition"
                        style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);"
                        onmouseover="this.style.borderColor='var(--cs-teal)'"
                        onmouseout="this.style.borderColor='color-mix(in srgb, var(--cs-ink) 12%, transparent)'"
                    >
                        <p class="font-medium" style="color: var(--cs-ink);">🏷️ Categorías</p>
                        <p class="text-sm mt-1" style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">
                            Organizá y ponele presupuesto a tus gastos
                        </p>
                    </Link>
                    <Link
                        :href="route('household.show')"
                        class="rounded-xl border p-5 transition"
                        style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 12%, transparent);"
                        onmouseover="this.style.borderColor='var(--cs-teal)'"
                        onmouseout="this.style.borderColor='color-mix(in srgb, var(--cs-ink) 12%, transparent)'"
                    >
                        <p class="font-medium" style="color: var(--cs-ink);">👥 Cuenta compartida</p>
                        <p class="text-sm mt-1" style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">
                            Invitá a alguien a ver tus finanzas
                        </p>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>