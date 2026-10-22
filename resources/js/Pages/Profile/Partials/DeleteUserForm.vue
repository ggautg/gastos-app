<script setup>
import { nextTick, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useBackdropClose } from '@/Composables/useBackdropClose';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
};

const { onPointerDown, onClick: onBackdropClick } = useBackdropClose(closeModal);
</script>

<template>
    <section>
        <header class="mb-4">
            <h2 class="cs-section-title" style="color: var(--cs-terra);">Eliminar cuenta</h2>
            <p class="mt-1 text-sm leading-relaxed" style="color: var(--cs-muted);">
                Una vez que borres tu cuenta, todos tus datos se pierden permanentemente. Descargá cualquier información que quieras conservar antes de continuar.
            </p>
        </header>

        <button
            type="button"
            @click="confirmUserDeletion"
            class="cs-btn cs-btn-danger"
        >
            Eliminar cuenta
        </button>

        <div
            v-if="confirmingUserDeletion"
            class="cs-overlay fixed inset-0 z-50 flex items-center justify-center px-4"
            @pointerdown="onPointerDown" @click="onBackdropClick"
        >
            <div class="cs-panel w-full max-w-md p-6">
                <h3 class="cs-panel-title mb-2">
                    ¿Seguro que querés eliminar tu cuenta?
                </h3>
                <p class="mb-4 text-sm leading-relaxed" style="color: var(--cs-muted);">
                    Esta acción no se puede deshacer. Ingresá tu contraseña para confirmar.
                </p>

                <form @submit.prevent="deleteUser">
                    <input
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        placeholder="Contraseña"
                        class="cs-input"
                    />
                    <p v-if="form.errors.password" class="cs-error">{{ form.errors.password }}</p>

                    <div class="mt-5 flex justify-end gap-2">
                        <button
                            type="button"
                            @click="closeModal"
                            class="cs-btn cs-btn-ghost"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="cs-btn cs-btn-danger"
                        >
                            Eliminar cuenta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</template>