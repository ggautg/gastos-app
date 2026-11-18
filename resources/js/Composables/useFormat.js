/**
 * Formato único de dinero y fechas para toda la app.
 * Centralizarlo evita que cada componente use un separador distinto.
 */
const formateador = new Intl.NumberFormat('es-PY');

/** Número con separador de miles paraguayo: 150000 → 150.000 */
export function formatNumero(valor) {
    return formateador.format(Number(valor) || 0);
}

/** Monto en guaraníes: 150000 → ₲ 150.000 */
export function formatGs(valor) {
    return `₲ ${formatNumero(valor)}`;
}

/** Número compacto para ejes de gráficos: 1500000 → 1,5 M */
export function formatCompacto(valor) {
    return new Intl.NumberFormat('es-PY', { notation: 'compact' }).format(Number(valor) || 0);
}

/** Fecha ISO (2026-11-17) al formato que se usa en las listas: 17/11/2026 */
export function formatFecha(fecha) {
    const [anio, mes, dia] = String(fecha).split('T')[0].split('-');
    return `${dia}/${mes}/${anio}`;
}
