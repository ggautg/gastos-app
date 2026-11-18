<script setup>
/** Único lugar donde se pinta el toast global. */
import { useToast } from '@/Composables/useToast';

const { toast } = useToast();
</script>

<template>
    <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 translate-y-2"
        enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="toast" class="cs-toast" :class="toast.tipo === 'success' ? '' : 'cs-toast-error'">
            {{ toast.mensaje }}
        </div>
    </Transition>
</template>
