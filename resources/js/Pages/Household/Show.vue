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
            <h2 class="text-xl font-semibold text-slate-800 dark:text-gray-200">Cuenta compartida</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Info del household actual -->
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="font-semibold text-slate-800 dark:text-gray-200 mb-1">{{ household.name }}</h3>
                    <p class="text-sm text-slate-500 dark:text-gray-400 mb-4">
                        Tu rol acá: <span class="font-medium">{{ role === 'owner' ? 'Dueño' : 'Invitado (solo lectura)' }}</span>
                    </p>

                    <div v-if="role === 'owner'">
                        <p class="text-sm text-slate-600 dark:text-gray-400 mb-2">
                            Compartí este código para que alguien se sume a ver tus finanzas:
                        </p>
                        <div class="flex items-center gap-2">
                            <code class="flex-1 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-lg font-mono tracking-widest text-center">
                                {{ household.invite_code }}
                            </code>
                            <button
                                @click="copiarCodigo"
                                class="px-3 py-2 rounded-lg bg-teal-700 text-white text-sm hover:bg-teal-800"
                            >
                                {{ copiado ? '¡Copiado!' : 'Copiar' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Miembros -->
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="font-semibold text-slate-800 dark:text-gray-200 mb-3">Miembros</h3>
                    <ul class="divide-y divide-slate-100 dark:divide-slate-700">
                        <li v-for="m in members" :key="m.id" class="py-2 flex justify-between items-center text-sm">
                            <span class="text-slate-700 dark:text-gray-200">{{ m.name }} <span class="text-slate-400 dark:text-gray-400">({{ m.email }})</span></span>
                            <span
                                class="text-xs px-2 py-0.5 rounded-full"
                                :class="m.pivot?.role === 'owner' ? 'bg-teal-50 text-teal-700 dark:bg-teal-900 dark:text-teal-500' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-gray-400'"
                            >
                                {{ m.pivot?.role === 'owner' ? 'Dueño' : 'Invitado' }}
                            </span>
                        </li>
                    </ul>
                </div>

                <!-- Unirse a otro household -->
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="font-semibold text-slate-800 dark:text-gray-200 mb-2">¿Te invitaron?</h3>
                    <p class="text-sm text-slate-500 dark:text-gray-400 mb-3">
                        Ingresá el código que te compartieron. Vas a dejar de ver tus datos actuales y vas a pasar a ver los del household al que te unís, como invitado de solo lectura.
                    </p>
                    <form @submit.prevent="unirse" class="flex gap-2">
                        <input
                            v-model="joinForm.invite_code"
                            type="text"
                            placeholder="Ej: X7K2P9QM"
                            class="flex-1 rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-gray-200 uppercase focus:border-teal-600 focus:ring-teal-600"
                        />
                        <button
                            type="submit"
                            :disabled="joinForm.processing"
                            class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm hover:bg-slate-900 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-slate-700 dark:hover:bg-slate-600"
                        >
                            Unirme
                        </button>
                    </form>
                    <p v-if="joinForm.errors.invite_code" class="text-xs text-red-600 mt-2">
                        {{ joinForm.errors.invite_code }}
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>