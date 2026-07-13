<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header class="mb-4">
            <h2 class="text-lg font-medium text-slate-800 dark:text-slate-100">Datos personales</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Actualizá tu nombre y email.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="space-y-4"
        >
            <div>
                <label class="block text-sm text-slate-600 dark:text-slate-300 mb-1">Nombre</label>
                <input
                    v-model="form.name"
                    type="text"
                    required
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
                    class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:border-teal-600 focus:ring-teal-600"
                />
                <p v-if="form.errors.email" class="text-xs text-red-600 mt-1">{{ form.errors.email }}</p>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm text-amber-600 dark:text-amber-400">
                    Tu email todavía no está verificado.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline hover:no-underline"
                    >
                        Reenviar email de verificación
                    </Link>
                </p>
                <p v-if="status === 'verification-link-sent'" class="mt-2 text-sm text-emerald-600 dark:text-emerald-400">
                    Te mandamos un nuevo link de verificación.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-4 py-2 rounded-lg bg-teal-700 text-white text-sm font-medium hover:bg-teal-800 disabled:opacity-50"
                >
                    Guardar
                </button>
                <p v-if="form.recentlySuccessful" class="text-sm text-emerald-600 dark:text-emerald-400">
                    Guardado ✓
                </p>
            </div>
        </form>
    </section>
</template>