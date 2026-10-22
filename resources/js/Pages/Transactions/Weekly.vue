<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import MonthYearSelector from '@/Components/MonthYearSelector.vue';
import WeeklyBlockCard from '@/Components/WeeklyBlockCard.vue';

const props = defineProps({
    month: Number,
    year: Number,
    bloques: Array,
    bloquesMesAnterior: Array,
});

const selectedMonth = ref(props.month);
const selectedYear = ref(props.year);

function goToMonth() {
    router.get(route('transactions.weekly'), {
        month: selectedMonth.value,
        year: selectedYear.value,
    }, { preserveState: true });
}
</script>

<template>
    <Head title="Comparativa semanal" />

    <AuthenticatedLayout>
        <template #header>
            <div class="space-y-1.5">
                <p class="cs-eyebrow">Comparativa</p>
                <h2 class="cs-page-title">Semanal</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 space-y-6">

                <MonthYearSelector
                    v-model:month="selectedMonth"
                    v-model:year="selectedYear"
                    @change="goToMonth"
                />

                <div class="space-y-3">
                    <WeeklyBlockCard
                        v-for="(bloque, i) in bloques"
                        :key="i"
                        :bloque="bloque"
                        :bloque-mes-anterior="bloquesMesAnterior[i] ?? null"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>