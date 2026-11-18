import { computed } from 'vue';
import { theme, THEMES } from '@/Composables/useTheme';

// Canvas no entiende var()/color-mix(): resolvemos el color calculado del tema.
function resuelto(variable, fallback) {
    if (typeof document === 'undefined') return fallback;

    const elemento = document.createElement('span');
    elemento.style.position = 'absolute';
    elemento.style.visibility = 'hidden';
    elemento.style.color = `var(${variable})`;
    document.body.appendChild(elemento);
    const color = getComputedStyle(elemento).color || fallback;
    elemento.remove();

    return color;
}

// Los colores de categoría son hex elegidos por el usuario: los aclara u oscurece
// lo justo para que no se pierdan contra el fondo del tema.
export function colorLegible(color, oscuro) {
    const coincidencia = /^#?([0-9a-f]{3}|[0-9a-f]{6})$/i.exec((color || '').trim());
    if (!coincidencia) return null;

    let hex = coincidencia[1];
    if (hex.length === 3) hex = hex.split('').map((c) => c + c).join('');

    let [r, g, b] = [0, 2, 4].map((i) => parseInt(hex.slice(i, i + 2), 16));
    const luminancia = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

    if (oscuro ? luminancia >= 0.3 : luminancia <= 0.72) return `#${hex}`;

    const objetivo = oscuro ? 255 : 0;
    const mezcla = oscuro ? 0.4 : 0.45;
    const ajustar = (valor) => Math.round(valor + (objetivo - valor) * mezcla);
    [r, g, b] = [ajustar(r), ajustar(g), ajustar(b)];

    return '#' + [r, g, b].map((v) => v.toString(16).padStart(2, '0')).join('');
}

export function useChartColors() {
    return computed(() => {
        theme.value; // fuerza recalcular cuando cambia el tema
        const oscuro = THEMES.find((preset) => preset.id === theme.value)?.dark ?? false;

        return {
            dark: oscuro,
            muted: resuelto('--cs-muted-strong', '#40544d'),
            grid: resuelto('--cs-line', '#d5ddd6'),
            paper: resuelto('--cs-paper-card', '#ffffff'),
            emerald: resuelto('--cs-emerald', '#0b6f50'),
            terra: resuelto('--cs-terra', '#c0522d'),
        };
    });
}
