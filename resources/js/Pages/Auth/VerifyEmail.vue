<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Verificar email" />

        <div class="mb-4 text-sm" style="color: var(--cs-muted-strong);">
            ¡Gracias por registrarte! Antes de empezar, verificá tu email con el link que te enviamos.
            Si no te llegó, te mandamos otro.
        </div>

        <div
            class="mb-4 text-sm font-medium"
            style="color: var(--cs-emerald);"
            v-if="verificationLinkSent"
        >
            Te enviamos un nuevo link de verificación al email que usaste al registrarte.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Reenviar verificación
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="cs-link text-sm"
                    >Cerrar sesión</Link
                >
            </div>
        </form>
    </GuestLayout>
</template>
