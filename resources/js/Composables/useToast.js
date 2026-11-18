import { ref } from 'vue';

const toast = ref(null);
let temporizador;

/**
 * Toast global. Se comparte entre páginas para no repetir el mismo bloque
 * de Transition en cada una. Si llega un mensaje nuevo, se reinicia el tiempo.
 */
export function useToast() {
    function mostrarToast(mensaje, tipo = 'success') {
        clearTimeout(temporizador);
        toast.value = { mensaje, tipo };
        temporizador = setTimeout(() => (toast.value = null), 3000);
    }

    return { toast, mostrarToast };
}
