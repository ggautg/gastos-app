<script setup>
import { computed, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteModal from '@/Components/DeleteModal.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    household: Object,
    members: Array,
    role: String,
    households: Array,
    stats: Object,
});

const copiado = ref(false);

function copiarCodigo() {
    navigator.clipboard.writeText(props.household.invite_code);
    copiado.value = true;
    setTimeout(() => (copiado.value = false), 2000);
}

const joinForm = useForm({
    invite_code: '',
});

function unirse() {
    joinForm.post(route('household.join'), {
        onSuccess: () => joinForm.reset(),
    });
}

function usarHogar(id) {
    router.post(route('household.switch'), { household_id: id }, { preserveScroll: true });
}

const renombrando = ref(false);
const renameForm = useForm({ name: '' });

function empezarRenombrar() {
    renameForm.name = props.household.name;
    renameForm.clearErrors();
    renombrando.value = true;
}

function renombrar() {
    renameForm.patch(route('household.update'), {
        onSuccess: () => (renombrando.value = false),
    });
}

const confirmandoEliminacion = ref(false);
const eliminando = ref(false);

function eliminarHogar() {
    eliminando.value = true;
    router.delete(route('household.destroy'), { confirm: 1 }, {
        onFinish: () => {
            eliminando.value = false;
            confirmandoEliminacion.value = false;
        },
    });
}

const mensajeEliminacion = computed(() =>
    `Se van a eliminar ${props.stats.categories} categorías y ${props.stats.transactions} movimientos de “${props.household.name}”. No se puede deshacer.`
);

const hogarASalir = ref(null);
const saliendo = ref(false);

function pedirSalida(hogar) {
    hogarASalir.value = hogar;
}

function salir() {
    saliendo.value = true;
    router.delete(route('household.leave'), { household_id: hogarASalir.value.id }, {
        onSuccess: () => (hogarASalir.value = null),
        onFinish: () => (saliendo.value = false),
    });
}

function noPuedeSalir(hogar) {
    if (props.households.length === 1) return 'Es tu único hogar';
    if (hogar.role === 'owner' && hogar.miembros === 1) return 'Sos el único integrante: eliminá el hogar en vez de salir';
    return null;
}

const mensajeSalida = computed(() =>
    `Vas a dejar de ver los datos de “${hogarASalir.value?.name ?? ''}”. Para volver, necesitás un código de invitación.`
);
</script>

<template>
    <Head title="Mi hogar" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader eyebrow="Hogar" title="Mi hogar" />
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Info del hogar actual -->
                <div class="cs-card p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="cs-section-title">{{ household.name }}</h3>
                            <p class="mt-1 text-sm" style="color: var(--cs-muted);">
                                Tu rol acá:
                                <span class="font-semibold" style="color: var(--cs-ink);">{{ role === 'owner' ? 'Dueño' : 'Invitado (solo lectura)' }}</span>
                            </p>
                        </div>
                    </div>

                    <div v-if="role === 'owner'" class="mt-4">
                        <p class="mb-2 text-sm" style="color: var(--cs-muted);">
                            Compartí este código para que alguien se sume a ver tus finanzas:
                        </p>
                        <div class="flex items-center gap-2">
                            <code
                                class="cs-amount flex-1 rounded-xl border px-3 py-2.5 text-center text-lg tracking-[0.22em]"
                                style="border-color: var(--cs-line-strong); background: color-mix(in srgb, var(--cs-ink) 3%, transparent); color: var(--cs-ink);"
                            >
                                {{ household.invite_code }}
                            </code>
                            <button
                                @click="copiarCodigo"
                                class="cs-btn cs-btn-primary"
                            >
                                {{ copiado ? '¡Copiado!' : 'Copiar' }}
                            </button>
                        </div>
                    </div>

                    <div v-if="role === 'owner'" class="mt-4 border-t pt-4" style="border-color: var(--cs-line);">
                        <div v-if="!renombrando" class="flex flex-wrap gap-2">
                            <button type="button" class="cs-btn cs-btn-ghost cs-btn-sm" @click="empezarRenombrar">
                                Renombrar
                            </button>
                            <button
                                type="button"
                                class="cs-btn cs-btn-ghost cs-btn-sm"
                                style="color: var(--cs-terra);"
                                @click="confirmandoEliminacion = true"
                            >
                                Eliminar hogar
                            </button>
                        </div>
                        <form v-else @submit.prevent="renombrar" class="flex gap-2">
                            <input
                                id="household-name"
                                v-model="renameForm.name"
                                type="text"
                                maxlength="80"
                                aria-label="Nombre del hogar"
                                class="cs-input flex-1"
                            />
                            <button type="submit" class="cs-btn cs-btn-primary" :disabled="renameForm.processing">
                                Guardar
                            </button>
                            <button type="button" class="cs-btn cs-btn-ghost" @click="renombrando = false">
                                Cancelar
                            </button>
                        </form>
                        <p v-if="renameForm.errors.name" class="cs-error mt-2">{{ renameForm.errors.name }}</p>
                    </div>
                </div>

                <!-- Cambiar de hogar -->
                <div class="cs-card p-5">
                    <h3 class="cs-section-title mb-1">Tus hogares</h3>
                    <p class="mb-3 text-sm" style="color: var(--cs-muted);">
                        Elegí cuál querés ver. Podés volver a otro cuando quieras.
                    </p>
                    <ul class="cs-list">
                        <li v-for="h in households" :key="h.id" class="cs-list-row">
                            <span class="min-w-0 truncate text-sm" style="color: var(--cs-ink-soft);">
                                {{ h.name }}
                                <span class="text-xs" style="color: var(--cs-faint);">
                                    ({{ h.role === 'owner' ? 'Dueño' : 'Invitado' }})
                                </span>
                            </span>
                            <div class="flex shrink-0 items-center gap-2">
                                <span v-if="h.actual" class="cs-badge">Actual</span>
                                <button v-else type="button" class="cs-btn cs-btn-ghost cs-btn-sm" @click="usarHogar(h.id)">
                                    Usar este
                                </button>
                                <button
                                    type="button"
                                    class="cs-btn cs-btn-ghost cs-btn-sm"
                                    :disabled="!!noPuedeSalir(h)"
                                    :title="noPuedeSalir(h) || null"
                                    @click="pedirSalida(h)"
                                >
                                    Salir
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Miembros -->
                <div class="cs-card p-5">
                    <h3 class="cs-section-title mb-3">Miembros</h3>
                    <ul class="cs-list">
                        <li v-for="m in members" :key="m.id" class="cs-list-row">
                            <span class="min-w-0 truncate text-sm" style="color: var(--cs-ink-soft);">
                                {{ m.name }}
                                <span class="text-xs" style="color: var(--cs-faint);">({{ m.email }})</span>
                            </span>
                            <span
                                class="cs-badge"
                                :style="m.pivot?.role === 'owner'
                                    ? { background: 'color-mix(in srgb, var(--cs-teal) 12%, transparent)', color: 'var(--cs-teal)' }
                                    : { background: 'color-mix(in srgb, var(--cs-ink) 7%, transparent)', color: 'var(--cs-muted)' }"
                            >
                                {{ m.pivot?.role === 'owner' ? 'Dueño' : 'Invitado' }}
                            </span>
                        </li>
                    </ul>
                </div>

                <!-- Unirse a otro hogar -->
                <div class="cs-card p-5">
                    <h3 class="cs-section-title mb-1">¿Te invitaron?</h3>
                    <p class="mb-3 text-sm leading-relaxed" style="color: var(--cs-muted);">
                        Ingresá el código que te compartieron. Vas a ver los datos de ese hogar como invitado de solo lectura, y podés volver a los tuyos desde “Tus hogares”.
                    </p>
                    <form @submit.prevent="unirse" class="flex gap-2">
                        <input
                            id="invite-code"
                            aria-label="Código de invitación"
                            v-model="joinForm.invite_code"
                            type="text"
                            placeholder="Ej: X7K2P9QM"
                            class="cs-input flex-1 uppercase tracking-widest"
                        />
                        <button
                            type="submit"
                            :disabled="joinForm.processing"
                            class="cs-btn cs-btn-primary"
                        >
                            Unirme
                        </button>
                    </form>
                    <p v-if="joinForm.errors.invite_code" class="cs-error">
                        {{ joinForm.errors.invite_code }}
                    </p>
                </div>

                <DeleteModal
                    :show="confirmandoEliminacion"
                    title="¿Eliminar este hogar?"
                    :message="mensajeEliminacion"
                    :processing="eliminando"
                    @cancel="confirmandoEliminacion = false"
                    @confirm="eliminarHogar"
                />

                <DeleteModal
                    :show="!!hogarASalir"
                    title="¿Salir del hogar?"
                    :message="mensajeSalida"
                    confirm-label="Salir"
                    :processing="saliendo"
                    @cancel="hogarASalir = null"
                    @confirm="salir"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>