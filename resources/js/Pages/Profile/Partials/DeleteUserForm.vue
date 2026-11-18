<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppModal from '@/Components/AppModal.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({ password: '' });

function confirmUserDeletion() {
    confirmingUserDeletion.value = true;
}

function deleteUser() {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
}

function closeModal() {
    confirmingUserDeletion.value = false;
    form.reset();
}
</script>

<template>
    <section>
        <header class="mb-4">
            <h2 class="cs-section-title" style="color: var(--cs-terra);">Eliminar cuenta</h2>
            <p class="mt-1 text-sm leading-relaxed" style="color: var(--cs-muted);">
                Una vez que borres tu cuenta, todos tus datos se pierden permanentemente. Descargá cualquier información que quieras conservar antes de continuar.
            </p>
        </header>

        <button type="button" @click="confirmUserDeletion" class="cs-btn cs-btn-danger">
            Eliminar cuenta
        </button>

        <AppModal :show="confirmingUserDeletion" size="md" title="¿Seguro que querés eliminar tu cuenta?"
            @close="closeModal">
            <p class="mb-4 text-sm leading-relaxed" style="color: var(--cs-muted);">
                Esta acción no se puede deshacer. Ingresá tu contraseña para confirmar.
            </p>

            <form @submit.prevent="deleteUser">
                <input ref="passwordInput" v-model="form.password" type="password" placeholder="Contraseña"
                    aria-label="Contraseña" class="cs-input" />
                <p v-if="form.errors.password" class="cs-error">{{ form.errors.password }}</p>

                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" @click="closeModal" class="cs-btn cs-btn-ghost">Cancelar</button>
                    <button type="submit" :disabled="form.processing" class="cs-btn cs-btn-danger">
                        Eliminar cuenta
                    </button>
                </div>
            </form>
        </AppModal>
    </section>
</template>
