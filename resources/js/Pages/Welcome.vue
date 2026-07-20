<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const montoAnimado = ref(0);
const montoObjetivo = 1480000;

function formatGs(amount) {
    return new Intl.NumberFormat('es-PY').format(Math.round(amount));
}

onMounted(() => {
    const prefiereMenosMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (prefiereMenosMovimiento) {
        montoAnimado.value = montoObjetivo;
        return;
    }

    const duracion = 1600;
    const inicio = performance.now();

    function paso(ahora) {
        const progreso = Math.min((ahora - inicio) / duracion, 1);
        const facilitado = 1 - Math.pow(1 - progreso, 3);
        montoAnimado.value = montoObjetivo * facilitado;
        if (progreso < 1) requestAnimationFrame(paso);
    }

    requestAnimationFrame(paso);
});

const items = [
    { label: 'Movimientos ilimitados', detalle: 'Gastos y ganancias, sin límite mensual' },
    { label: 'Multi-moneda', detalle: 'Guaraníes y dólares, con cotización automática' },
    { label: 'Presupuesto por categoría', detalle: 'Alertas cuando te acercás al tope' },
    { label: 'Cuenta compartida', detalle: 'Invitá a quien quieras, con roles' },
];
</script>

<template>
    <Head title="CheSogue — Tus finanzas en guaraníes" />

    <div class="cs-page">
        <header class="cs-header">
            <div class="cs-header-inner">
                <div class="cs-logo">
                    <div class="cs-logo-mark">₲</div>
                    <span class="cs-logo-word">CheSogue</span>
                </div>

                <nav class="cs-nav">
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="route('dashboard')"
                        class="cs-btn cs-btn-solid"
                    >
                        Ir a mi panel
                    </Link>
                    <template v-else>
                        <Link :href="route('login')" class="cs-btn cs-btn-ghost">
                            Iniciar sesión
                        </Link>
                        <Link :href="route('register')" class="cs-btn cs-btn-solid">
                            Crear cuenta
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main class="cs-hero">
            <svg class="cs-nandu" viewBox="0 0 400 400" aria-hidden="true">
                <defs>
                    <path id="cs-petal" d="M200,150 C210,120 230,110 240,130 C248,145 238,165 200,180 C162,165 152,145 160,130 C170,110 190,120 200,150 Z" />
                </defs>
                <g class="cs-nandu-ring">
                    <circle cx="200" cy="200" r="60" fill="none" stroke="var(--cs-teal)" stroke-width="1" stroke-dasharray="2 6" opacity="0.5" />
                    <circle cx="200" cy="200" r="110" fill="none" stroke="var(--cs-gold)" stroke-width="1" stroke-dasharray="2 6" opacity="0.4" />
                    <circle cx="200" cy="200" r="160" fill="none" stroke="var(--cs-teal)" stroke-width="1" stroke-dasharray="2 6" opacity="0.3" />
                </g>
                <g class="cs-nandu-petals">
                    <use v-for="n in 16" :key="n" href="#cs-petal" :transform="`rotate(${n * 22.5} 200 200)`" fill="none" :stroke="n % 2 === 0 ? 'var(--cs-teal)' : 'var(--cs-gold)'" stroke-width="1.2" />
                </g>
                <circle cx="200" cy="200" r="6" fill="var(--cs-gold)" />
            </svg>

            <div class="cs-hero-content">
                <p class="cs-eyebrow">Finanzas personales · Paraguay</p>
                <h1 class="cs-headline">
                    Tus gastos y ganancias,<br />
                    tejidos en guaraníes.
                </h1>
                <p class="cs-subhead">
                    Registrá cada movimiento del mes, seguí tu presupuesto por categoría
                    y compartí tus cuentas con quien quieras — todo en un solo lugar.
                </p>

                <div class="cs-ticker">
                    <span class="cs-ticker-label">Balance de julio</span>
                    <span class="cs-ticker-amount">₲ {{ formatGs(montoAnimado) }}</span>
                </div>

                <div class="cs-cta-row">
                    <Link :href="route('register')" class="cs-btn cs-btn-solid cs-btn-lg">
                        Empezar gratis
                    </Link>
                    <Link :href="route('login')" class="cs-btn cs-btn-ghost cs-btn-lg">
                        Ya tengo cuenta
                    </Link>
                </div>
            </div>
        </main>

        <section class="cs-receipt-section">
            <div class="cs-receipt">
                <p class="cs-receipt-title">Qué incluye</p>
                <ul class="cs-receipt-list">
                    <li v-for="item in items" :key="item.label" class="cs-receipt-item">
                        <span class="cs-receipt-label">{{ item.label }}</span>
                        <span class="cs-receipt-dots"></span>
                        <span class="cs-receipt-detalle">{{ item.detalle }}</span>
                    </li>
                </ul>
            </div>
        </section>

        <footer class="cs-footer">
            CheSogue · Hecho en Paraguay
        </footer>
    </div>
</template>

<style scoped>
.cs-page {
    --cs-teal: #0F5257;
    --cs-teal-dark: #0A3A3E;
    --cs-ink: #12231F;
    --cs-paper: #EFF5F1;
    --cs-paper-card: #FFFFFF;
    --cs-gold: #C68A2E;
    --cs-gold-light: #E8C773;

    min-height: 100vh;
    background: var(--cs-paper);
    color: var(--cs-ink);
    display: flex;
    flex-direction: column;
}

:global(.dark) .cs-page {
    --cs-paper: #0E1B18;
    --cs-paper-card: #14231F;
    --cs-ink: #E8F0EC;
}

.cs-header-inner {
    max-width: 68rem;
    margin: 0 auto;
    padding: 1.5rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cs-logo {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.cs-logo-mark {
    width: 2.1rem;
    height: 2.1rem;
    border-radius: 0.6rem;
    background: var(--cs-teal);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-family: 'JetBrains Mono', monospace;
}

.cs-logo-word {
    font-family: 'Fraunces', serif;
    font-weight: 600;
    font-size: 1.15rem;
}

.cs-nav {
    display: flex;
    gap: 0.6rem;
}

.cs-btn {
    padding: 0.55rem 1.1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.15s ease;
    display: inline-block;
}

.cs-btn-solid {
    background: var(--cs-teal);
    color: white;
}

.cs-btn-solid:hover {
    background: var(--cs-teal-dark);
}

.cs-btn-ghost {
    color: var(--cs-ink);
    border: 1px solid color-mix(in srgb, var(--cs-ink) 20%, transparent);
}

.cs-btn-ghost:hover {
    background: color-mix(in srgb, var(--cs-ink) 6%, transparent);
}

.cs-btn-lg {
    padding: 0.8rem 1.6rem;
    font-size: 0.95rem;
}

.cs-hero {
    position: relative;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem 1.5rem 4rem;
    overflow: hidden;
}

.cs-nandu {
    position: absolute;
    width: min(700px, 90vw);
    height: min(700px, 90vw);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    pointer-events: none;
}

.cs-nandu-petals {
    opacity: 0;
    animation: cs-draw-in 1.4s ease-out 0.2s forwards;
}

@keyframes cs-draw-in {
    from { opacity: 0; transform: scale(0.85); }
    to { opacity: 0.35; transform: scale(1); }
}

@media (prefers-reduced-motion: reduce) {
    .cs-nandu-petals {
        animation: none;
        opacity: 0.35;
    }
}

.cs-hero-content {
    position: relative;
    z-index: 1;
    max-width: 34rem;
    text-align: center;
}

.cs-eyebrow {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.75rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--cs-teal);
    margin-bottom: 1rem;
}

:global(.dark) .cs-eyebrow {
    color: var(--cs-gold-light);
}

.cs-headline {
    font-family: 'Fraunces', serif;
    font-weight: 600;
    font-size: clamp(2.2rem, 5vw, 3.2rem);
    line-height: 1.1;
    margin-bottom: 1.25rem;
}

.cs-subhead {
    font-size: 1.05rem;
    line-height: 1.6;
    color: color-mix(in srgb, var(--cs-ink) 70%, transparent);
    margin-bottom: 2rem;
}

.cs-ticker {
    background: var(--cs-paper-card);
    border: 1px solid color-mix(in srgb, var(--cs-teal) 20%, transparent);
    border-radius: 0.75rem;
    padding: 1rem 1.5rem;
    display: inline-flex;
    flex-direction: column;
    gap: 0.2rem;
    margin-bottom: 2rem;
}

.cs-ticker-label {
    font-size: 0.75rem;
    color: color-mix(in srgb, var(--cs-ink) 55%, transparent);
}

.cs-ticker-amount {
    font-family: 'JetBrains Mono', monospace;
    font-size: 1.75rem;
    font-weight: 600;
    color: var(--cs-teal);
}

:global(.dark) .cs-ticker-amount {
    color: var(--cs-gold-light);
}

.cs-cta-row {
    display: flex;
    gap: 0.75rem;
    justify-content: center;
}

.cs-receipt-section {
    padding: 0 1.5rem 4rem;
    display: flex;
    justify-content: center;
}

.cs-receipt {
    background: var(--cs-paper-card);
    max-width: 26rem;
    width: 100%;
    padding: 1.75rem 1.75rem 1.5rem;
    position: relative;
    border-radius: 0.25rem;
    box-shadow: 0 1px 3px color-mix(in srgb, var(--cs-ink) 8%, transparent);
}

.cs-receipt::before,
.cs-receipt::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    height: 14px;
    background-image: radial-gradient(circle, var(--cs-paper) 60%, transparent 61%);
    background-size: 20px 20px;
    background-position: -4px center;
}

.cs-receipt::before {
    top: -7px;
}

.cs-receipt::after {
    bottom: -7px;
}

.cs-receipt-title {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.75rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: color-mix(in srgb, var(--cs-ink) 55%, transparent);
    margin-bottom: 1rem;
    text-align: center;
}

.cs-receipt-list {
    display: flex;
    flex-direction: column;
    gap: 0.9rem;
}

.cs-receipt-item {
    display: flex;
    align-items: baseline;
    gap: 0.4rem;
}

.cs-receipt-label {
    font-weight: 500;
    font-size: 0.9rem;
    white-space: nowrap;
}

.cs-receipt-dots {
    flex: 1;
    border-bottom: 1px dotted color-mix(in srgb, var(--cs-ink) 30%, transparent);
    height: 0.6em;
}

.cs-receipt-detalle {
    font-size: 0.8rem;
    color: color-mix(in srgb, var(--cs-ink) 55%, transparent);
    white-space: nowrap;
}

.cs-footer {
    text-align: center;
    padding: 1.5rem;
    font-size: 0.85rem;
    color: color-mix(in srgb, var(--cs-ink) 45%, transparent);
}

@media (max-width: 640px) {
    .cs-receipt-item {
        flex-wrap: wrap;
    }
    .cs-receipt-dots {
        display: none;
    }
    .cs-receipt-detalle {
        white-space: normal;
        width: 100%;
        padding-left: 0;
    }
}
</style>