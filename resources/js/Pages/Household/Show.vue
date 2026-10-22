<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    household: Object,
    members: Array,
    role: String,
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
</script>

<template>
    <Head title="Cuenta compartida" />

    <AuthenticatedLayout>
        <template #header>
            <div class="space-y-1.5">
                <p class="cs-eyebrow">Hogar</p>
                <h2 class="cs-page-title">Cuenta compartida</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Info del household actual -->
                <div class="cs-card p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="cs-section-title">{{ household.name }}</h3>
                            <p class="mt-1 text-sm" style="color: var(--cs-muted);">
                                Tu rol acá:
                                <span class="font-semibold" style="color: var(--cs-ink);">{{ role === 'owner' ? 'Dueño' : 'Invitado (solo lectura)' }}</span>
                            </p>
                        </div>
                        <span class="cs-badge" :style="role === 'owner'
                            ? { background: 'color-mix(in srgb, var(--cs-teal) 12%, transparent)', color: 'var(--cs-teal)' }
                            : { background: 'color-mix(in srgb, var(--cs-ink) 7%, transparent)', color: 'var(--cs-muted)' }">
                            {{ role === 'owner' ? 'Owner' : 'Guest' }}
                        </span>
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

                <!-- Unirse a otro household -->
                <div class="cs-card p-5">
                    <h3 class="cs-section-title mb-1">¿Te invitaron?</h3>
                    <p class="mb-3 text-sm leading-relaxed" style="color: var(--cs-muted);">
                        Ingresá el código que te compartieron. Vas a dejar de ver tus datos actuales y vas a pasar a ver los del household al que te unís, como invitado de solo lectura.
                    </p>
                    <form @submit.prevent="unirse" class="flex gap-2">
                        <input
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
            </div>
        </div>
    </AuthenticatedLayout>
</template>