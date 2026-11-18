import { computed } from 'vue';

/**
 * Años disponibles en los selectores: siete años alrededor del actual,
 * más el año seleccionado (para no ocultar datos históricos).
 */
export function useYears(selectedYear) {
    return computed(() => {
        const actual = new Date().getFullYear();
        const base = Array.from({ length: 7 }, (_, i) => actual - 3 + i);
        return [...new Set([...base, Number(selectedYear.value)])]
            .filter(Number.isFinite)
            .sort((a, b) => a - b);
    });
}
