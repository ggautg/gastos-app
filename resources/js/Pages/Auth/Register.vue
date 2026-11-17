<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Crear cuenta" />

    <GuestLayout>
        <h1 class="cs-form-title">Creá tu cuenta</h1>
        <p class="cs-form-subtitle">Empezá a registrar tus finanzas en un minuto</p>

        <form @submit.prevent="submit" class="cs-form">
            <div class="cs-field">
                <label class="cs-label" for="register-name">Nombre</label>
                <input
                    id="register-name"
                    v-model="form.name"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    class="cs-input"
                />
                <p v-if="form.errors.name" class="cs-error">{{ form.errors.name }}</p>
            </div>

            <div class="cs-field">
                <label class="cs-label" for="register-email">Email</label>
                <input
                    id="register-email"
                    v-model="form.email"
                    type="email"
                    required
                    autocomplete="username"
                    class="cs-input"
                />
                <p v-if="form.errors.email" class="cs-error">{{ form.errors.email }}</p>
            </div>

            <div class="cs-field">
                <label class="cs-label" for="register-password">Contraseña</label>
                <input
                    id="register-password"
                    v-model="form.password"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="cs-input"
                />
                <p v-if="form.errors.password" class="cs-error">{{ form.errors.password }}</p>
            </div>

            <div class="cs-field">
                <label class="cs-label" for="register-password-confirmation">Confirmar contraseña</label>
                <input
                    id="register-password-confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="cs-input"
                />
                <p v-if="form.errors.password_confirmation" class="cs-error">
                    {{ form.errors.password_confirmation }}
                </p>
            </div>

            <button type="submit" :disabled="form.processing" class="cs-submit">
                Crear cuenta
            </button>

            <p class="cs-switch">
                ¿Ya tenés cuenta?
                <Link :href="route('login')" class="cs-link cs-link-strong">Iniciar sesión</Link>
            </p>
        </form>
    </GuestLayout>
</template>