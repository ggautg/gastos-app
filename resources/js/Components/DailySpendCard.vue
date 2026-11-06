<script setup>
import { computed, ref } from 'vue';
import { CalendarDays, Download } from 'lucide-vue-next';

const props = defineProps({
    dias: {
        type: Object,
        default: () => ({}),
    },
    hoy: {
        type: String,
        required: true,
    },
    month: {
        type: Number,
        required: true,
    },
    year: {
        type: Number,
        required: true,
    },
});

const selectedDate = ref(props.hoy);

const pad = (value) => String(value).padStart(2, '0');
const diasEnMes = computed(() => new Date(props.year, props.month, 0).getDate());
const minDate = computed(() => `${props.year}-${pad(props.month)}-01`);
const maxDate = computed(() => `${props.year}-${pad(props.month)}-${pad(diasEnMes.value)}`);

const fechaSeleccionada = computed(() =>
    /^\d{4}-\d{2}-\d{2}$/.test(selectedDate.value) ? selectedDate.value : props.hoy,
);

const esHoy = computed(() => fechaSeleccionada.value === props.hoy);
const selectedDay = computed(() => Number(fechaSeleccionada.value.split('-')[2]));

const resumenDia = computed(
    () => props.dias?.[fechaSeleccionada.value] ?? { gastos: 0, ganancias: 0, movimientos: 0 },
);

const fechaLarga = computed(() => {
    const [year, month, day] = fechaSeleccionada.value.split('-').map(Number);
    const fecha = new Date(year, month - 1, day);
    const texto = new Intl.DateTimeFormat('es-PY', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
    }).format(fecha);

    return texto.charAt(0).toUpperCase() + texto.slice(1);
});

function irAHoy() {
    selectedDate.value = props.hoy;
}

function formatGs(amount) {
    return '₲ ' + new Intl.NumberFormat('es-PY').format(amount);
}
</script>

<template>
    <section class="cs-card p-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <p class="cs-eyebrow">Gasto del día</p>

                <p class="mt-2 text-sm cs-muted">
                    <span v-if="esHoy">Hoy gastaste</span>
                    <span v-else>{{ fechaLarga }} gastaste</span>
                </p>

                <p
                    class="cs-kpi-value mt-1 text-2xl"
                    :style="{ color: resumenDia.gastos > 0 ? 'var(--cs-terra)' : 'var(--cs-ink)' }"
                >
                    {{ formatGs(resumenDia.gastos) }}
                </p>

                <p class="mt-1 text-xs cs-faint">
                    {{ resumenDia.movimientos }}
                    {{ resumenDia.movimientos === 1 ? 'movimiento registrado' : 'movimientos registrados' }}
                    ese día
                </p>
            </div>

            <div class="flex flex-col gap-2 sm:items-end">
                <label class="flex items-center gap-2 text-xs cs-faint">
                    <CalendarDays class="h-4 w-4 shrink-0" />
                    <input
                        v-model="selectedDate"
                        type="date"
                        :min="minDate"
                        :max="maxDate"
                        class="cs-input w-full py-1.5 text-sm sm:w-auto"
                    />
                </label>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        v-if="!esHoy"
                        type="button"
                        class="cs-btn cs-btn-ghost cs-btn-sm"
                        @click="irAHoy"
                    >
                        Hoy
                    </button>

                    <a
                        :href="route('transactions.export.excel', { month, year, day: selectedDay })"
                        class="cs-btn cs-btn-ghost cs-btn-sm"
                        title="Descargar el Excel de este día"
                    >
                        <Download class="h-3.5 w-3.5" />
                        Excel
                    </a>

                    <a
                        :href="route('transactions.export.pdf', { month, year, day: selectedDay })"
                        class="cs-btn cs-btn-ghost cs-btn-sm"
                        title="Descargar el PDF de este día"
                    >
                        <Download class="h-3.5 w-3.5" />
                        PDF
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>
