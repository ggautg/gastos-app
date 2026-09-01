import { ref } from 'vue';

const toast = ref(null);

export function useToast() {
    function mostrarToast(mensaje, tipo = 'success') {
        toast.value = { mensaje, tipo };
        setTimeout(() => (toast.value = null), 3000);
    }

    return { toast, mostrarToast };
}