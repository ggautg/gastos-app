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
            <h2 class="cs-section-title">Datos personales</h2>
            <p class="mt-1 text-sm" style="color: var(--cs-muted);">
                Actualizá tu nombre y email.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="space-y-4"
        >
            <div>
                <label class="cs-label">Nombre</label>
                <input
                    v-model="form.name"
                    type="text"
                    required
                    class="cs-input"
                />
                <p v-if="form.errors.name" class="cs-error">{{ form.errors.name }}</p>
            </div>

            <div>
                <label class="cs-label">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    required
                    class="cs-input"
                />
                <p v-if="form.errors.email" class="cs-error">{{ form.errors.email }}</p>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm" style="color: var(--cs-gold);">
                    Tu email todavía no está verificado.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="cs-link underline hover:no-underline"
                    >
                        Reenviar email de verificación
                    </Link>
                </p>
                <p v-if="status === 'verification-link-sent'" class="mt-2 text-sm" style="color: var(--cs-emerald);">
                    Te mandamos un nuevo link de verificación.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="cs-btn cs-btn-primary"
                >
                    Guardar
                </button>
                <p v-if="form.recentlySuccessful" class="text-sm" style="color: var(--cs-emerald);">
                    Guardado ✓
                </p>
            </div>
        </form>
    </section>
</template>