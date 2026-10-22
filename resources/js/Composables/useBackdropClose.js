import { ref } from 'vue';

/**
 * Cierra un modal al hacer click en el fondo, pero solo si el gesto
 * empezó y terminó en el fondo. Así no se cierra al arrastrar el mouse
 * (por ejemplo, al seleccionar texto dentro del panel y soltar afuera).
 */
export function useBackdropClose(onClose) {
    const empezoEnFondo = ref(false);

    function onPointerDown(event) {
        empezoEnFondo.value = event.target === event.currentTarget;
    }

    function onClick(event) {
        if (!empezoEnFondo.value) {
            return;
        }

        empezoEnFondo.value = false;

        if (event.target === event.currentTarget) {
            onClose();
        }
    }

    return { onPointerDown, onClick };
}
