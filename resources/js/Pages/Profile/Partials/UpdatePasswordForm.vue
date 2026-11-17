<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header class="mb-4">
            <h2 class="cs-section-title">Cambiar contraseña</h2>
            <p class="mt-1 text-sm" style="color: var(--cs-muted);">
                Usá una contraseña larga y única para mantener tu cuenta segura.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="space-y-4">
            <div>
                <label class="cs-label" for="profile-current-password">Contraseña actual</label>
                <input
                    id="profile-current-password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    autocomplete="current-password"
                    class="cs-input"
                />
                <p v-if="form.errors.current_password" class="cs-error">
                    {{ form.errors.current_password }}
                </p>
            </div>

            <div>
                <label class="cs-label" for="profile-new-password">Nueva contraseña</label>
                <input
                    id="profile-new-password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                    class="cs-input"
                />
                <p v-if="form.errors.password" class="cs-error">{{ form.errors.password }}</p>
            </div>

            <div>
                <label class="cs-label" for="profile-password-confirmation">Confirmar nueva contraseña</label>
                <input
                    id="profile-password-confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="cs-input"
                />
                <p v-if="form.errors.password_confirmation" class="cs-error">
                    {{ form.errors.password_confirmation }}
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