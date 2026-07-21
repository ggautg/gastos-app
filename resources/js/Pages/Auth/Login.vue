<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Iniciar sesión" />

    <GuestLayout>
        <h1 class="cs-form-title">Bienvenido de nuevo</h1>
        <p class="cs-form-subtitle">Ingresá para ver tus finanzas</p>

        <div v-if="status" class="cs-status">{{ status }}</div>

        <form @submit.prevent="submit" class="cs-form">
            <div class="cs-field">
                <label class="cs-label">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    required
                    autofocus
                    autocomplete="username"
                    class="cs-input"
                />
                <p v-if="form.errors.email" class="cs-error">{{ form.errors.email }}</p>
            </div>

            <div class="cs-field">
                <label class="cs-label">Contraseña</label>
                <input
                    v-model="form.password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="cs-input"
                />
                <p v-if="form.errors.password" class="cs-error">{{ form.errors.password }}</p>
            </div>

            <div class="cs-row-between">
                <label class="cs-checkbox-row">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    Recordarme
                </label>

                <Link v-if="canResetPassword" :href="route('password.request')" class="cs-link">
                    ¿Olvidaste tu contraseña?
                </Link>
            </div>

            <button type="submit" :disabled="form.processing" class="cs-submit">
                Iniciar sesión
            </button>

            <p class="cs-switch">
                ¿No tenés cuenta?
                <Link :href="route('register')" class="cs-link cs-link-strong">Registrate</Link>
            </p>
        </form>
    </GuestLayout>
</template>