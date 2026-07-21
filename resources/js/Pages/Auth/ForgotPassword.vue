<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Recuperar contraseña" />

    <GuestLayout>
        <h1 class="cs-form-title">¿Olvidaste tu contraseña?</h1>
        <p class="cs-form-subtitle">Ingresá tu email y te mandamos un link para crear una nueva.</p>

        <div v-if="status" class="cs-status">{{ status }}</div>

        <form @submit.prevent="submit" class="cs-form">
            <div class="cs-field">
                <label class="cs-label">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    required
                    autofocus
                    class="cs-input"
                />
                <p v-if="form.errors.email" class="cs-error">{{ form.errors.email }}</p>
            </div>

            <button type="submit" :disabled="form.processing" class="cs-submit">
                Enviar link de recuperación
            </button>
        </form>
    </GuestLayout>
</template>