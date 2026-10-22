<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { ArrowRight, ShieldCheck, Sparkles } from 'lucide-vue-next';

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
    { label: 'Presupuesto por categoría', detalle: 'Alertas cuando te acercás al tope' },
    { label: 'Cuenta compartida', detalle: 'Invitá a quien quieras, con roles' },
];
</script>

<template>
    <Head title="CheSogue — Tus finanzas en guaraníes" />

    <div class="cs-landing">
        <div class="cs-landing-aura" aria-hidden="true"></div>

        <header class="cs-landing-header">
            <div class="cs-landing-header-inner">
                <div class="cs-landing-logo">
                    <span class="cs-brand-mark">₲</span>
                    <span class="cs-brand-word">CheSogue</span>
                </div>

                <nav class="flex items-center gap-2">
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="route('dashboard')"
                        class="cs-btn cs-btn-primary cs-btn-sm"
                    >
                        Ir a mi panel
                        <ArrowRight class="h-3.5 w-3.5" />
                    </Link>
                    <template v-else>
                        <Link :href="route('login')" class="cs-btn cs-btn-ghost cs-btn-sm">
                            Iniciar sesión
                        </Link>
                        <Link :href="route('register')" class="cs-btn cs-btn-primary cs-btn-sm">
                            Crear cuenta
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main class="cs-landing-hero">
            <svg class="cs-landing-nandu" viewBox="0 0 400 400" aria-hidden="true">
                <defs>
                    <path id="cs-petal" d="M200,150 C210,120 230,110 240,130 C248,145 238,165 200,180 C162,165 152,145 160,130 C170,110 190,120 200,150 Z" />
                </defs>
                <g class="cs-landing-rings">
                    <circle cx="200" cy="200" r="60" fill="none" stroke="var(--cs-teal)" stroke-width="1" stroke-dasharray="2 7" opacity="0.45" />
                    <circle cx="200" cy="200" r="112" fill="none" stroke="var(--cs-gold)" stroke-width="1" stroke-dasharray="2 7" opacity="0.35" />
                    <circle cx="200" cy="200" r="164" fill="none" stroke="var(--cs-teal)" stroke-width="1" stroke-dasharray="2 7" opacity="0.22" />
                </g>
                <g class="cs-landing-petals">
                    <use v-for="n in 16" :key="n" href="#cs-petal" :transform="`rotate(${n * 22.5} 200 200)`" fill="none" :stroke="n % 2 === 0 ? 'var(--cs-teal)' : 'var(--cs-gold)'" stroke-width="1" />
                </g>
                <circle cx="200" cy="200" r="5" fill="var(--cs-gold)" />
            </svg>

            <div class="cs-landing-content">
                <p class="cs-eyebrow">Finanzas personales en guaraníes</p>

                <h1 class="cs-landing-headline">
                    Tus gastos y ganancias,<br />
                    <em>tejidos</em> en guaraníes.
                </h1>

                <p class="cs-landing-subhead">
                    Registrá cada movimiento del mes, seguí tu presupuesto por categoría
                    y compartí tus cuentas con quien quieras — todo en un solo lugar.
                </p>

                <div class="cs-landing-ticker">
                    <div>
                        <p class="cs-landing-ticker-label">Balance de julio</p>
                        <p class="cs-landing-ticker-amount">₲ {{ formatGs(montoAnimado) }}</p>
                    </div>
                    <span class="cs-hero-chip">
                        <Sparkles class="h-3 w-3" />
                        Al día
                    </span>
                </div>

                <div class="cs-landing-cta">
                    <Link :href="route('register')" class="cs-btn cs-btn-primary cs-btn-lg">
                        Empezar gratis
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                    <Link :href="route('login')" class="cs-btn cs-btn-ghost cs-btn-lg">
                        Ya tengo cuenta
                    </Link>
                </div>

                <p class="cs-landing-note">
                    <ShieldCheck class="h-3.5 w-3.5" />
                    Tus datos quedan en tu cuenta. Sin publicidad, sin letra chica.
                </p>
            </div>
        </main>

        <section class="cs-landing-receipt-section">
            <div class="cs-landing-receipt">
                <p class="cs-landing-receipt-title">Qué incluye</p>
                <ul class="cs-landing-receipt-list">
                    <li v-for="item in items" :key="item.label" class="cs-landing-receipt-item">
                        <span class="cs-landing-receipt-label">{{ item.label }}</span>
                        <span class="cs-landing-receipt-dots"></span>
                        <span class="cs-landing-receipt-detalle">{{ item.detalle }}</span>
                    </li>
                </ul>
                <div class="cs-landing-receipt-foot">
                    <span>CheSogue</span>
                    <span>Hecho para Paraguay</span>
                </div>
            </div>
        </section>

        <footer class="cs-landing-footer">
            <span>CheSogue</span>
            <span class="cs-landing-footer-dot" aria-hidden="true"></span>
            <span>Finanzas claras, decisiones simples.</span>
        </footer>
    </div>
</template>

<style scoped>
.cs-landing {
    position: relative;
    display: flex;
    min-height: 100vh;
    flex-direction: column;
    overflow: hidden;
    background: transparent;
    color: var(--cs-ink);
}

.cs-landing-aura {
    position: absolute;
    inset: 0;
    pointer-events: none;
    background:
        radial-gradient(58% 50% at 50% -6%, color-mix(in srgb, var(--cs-teal) 20%, transparent), transparent 70%),
        radial-gradient(42% 36% at 100% 100%, color-mix(in srgb, var(--cs-gold) 14%, transparent), transparent 65%);
}

.cs-landing-header {
    position: relative;
    z-index: 2;
    border-bottom: 1px solid var(--cs-line);
    background: color-mix(in srgb, var(--cs-paper) 72%, transparent);
    backdrop-filter: blur(16px) saturate(140%);
}

.cs-landing-header-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    max-width: 68rem;
    margin: 0 auto;
    padding: 0.9rem 1.5rem;
}

.cs-landing-logo {
    display: flex;
    align-items: center;
    gap: 0.65rem;
}

.cs-landing-hero {
    position: relative;
    display: flex;
    flex: 1;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: clamp(3rem, 8vw, 6rem) 1.5rem clamp(3.5rem, 9vw, 7rem);
}

.cs-landing-nandu {
    position: absolute;
    top: 50%;
    left: 50%;
    width: min(760px, 96vw);
    height: min(760px, 96vw);
    transform: translate(-50%, -50%);
    pointer-events: none;
}

.cs-landing-petals {
    opacity: 0;
    animation: cs-draw-in 1.5s var(--cs-ease) 0.2s forwards;
}

.cs-landing-rings {
    opacity: 0;
    animation: cs-draw-in 1.8s var(--cs-ease) 0.05s forwards;
}

@keyframes cs-draw-in {
    from {
        opacity: 0;
        transform: scale(0.9);
    }

    to {
        opacity: 0.35;
        transform: scale(1);
    }
}

@media (prefers-reduced-motion: reduce) {
    .cs-landing-petals,
    .cs-landing-rings {
        animation: none;
        opacity: 0.35;
    }
}

.cs-landing-content {
    position: relative;
    z-index: 1;
    max-width: 36rem;
    text-align: center;
}

.cs-landing-headline {
    margin: 1rem 0 1.3rem;
    font-family: var(--cs-font-display);
    font-variation-settings: 'opsz' 96, 'SOFT' 40, 'WONK' 1;
    font-weight: 600;
    font-size: clamp(2.2rem, 1.6rem + 3.6vw, 3.75rem);
    line-height: 1.04;
    letter-spacing: -0.038em;
}

.cs-landing-headline em {
    font-style: italic;
    font-variation-settings: 'opsz' 96, 'SOFT' 60, 'WONK' 1;
    background: linear-gradient(115deg, var(--cs-teal-light), var(--cs-gold) 72%);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.dark .cs-landing-headline em {
    background: linear-gradient(115deg, var(--cs-teal-light), var(--cs-gold-light) 72%);
    -webkit-background-clip: text;
    background-clip: text;
}

.cs-landing-subhead {
    max-width: 32rem;
    margin: 0 auto 2rem;
    font-size: 1.02rem;
    line-height: 1.65;
    color: var(--cs-muted);
    text-wrap: pretty;
}

.cs-landing-ticker {
    display: inline-flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding: 0.9rem 1.35rem;
    border: 1px solid color-mix(in srgb, var(--cs-teal) 22%, transparent);
    border-radius: var(--cs-radius-lg);
    background: color-mix(in srgb, var(--cs-paper-card) 82%, transparent);
    text-align: left;
    box-shadow: var(--cs-shadow-sm);
    backdrop-filter: blur(8px);
}

.cs-landing-ticker-label {
    font-size: 0.68rem;
    font-weight: 600;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--cs-faint);
}

.cs-landing-ticker-amount {
    margin-top: 0.15rem;
    font-family: var(--cs-font-mono);
    font-variant-numeric: tabular-nums;
    font-size: 1.65rem;
    font-weight: 600;
    letter-spacing: -0.04em;
    color: var(--cs-teal);
}

.dark .cs-landing-ticker-amount {
    color: var(--cs-gold-light);
}

.cs-landing-cta {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.75rem;
}

.cs-landing-note {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    margin-top: 1.4rem;
    font-size: 0.74rem;
    color: var(--cs-faint);
}

.cs-landing-receipt-section {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: center;
    padding: 0 1.5rem clamp(3rem, 6vw, 4.5rem);
}

.cs-landing-receipt {
    position: relative;
    width: 100%;
    max-width: 28rem;
    padding: 1.9rem 1.9rem 1.4rem;
    border: 1px solid var(--cs-line);
    border-radius: 0.3rem;
    background: var(--cs-paper-card);
    box-shadow: var(--cs-shadow-md);
}

.cs-landing-receipt::before,
.cs-landing-receipt::after {
    content: '';
    position: absolute;
    right: 0;
    left: 0;
    height: 14px;
    background-image: radial-gradient(circle, var(--cs-paper) 62%, transparent 63%);
    background-size: 20px 20px;
    background-position: -4px center;
}

.cs-landing-receipt::before {
    top: -7px;
}

.cs-landing-receipt::after {
    bottom: -7px;
}

.cs-landing-receipt-title {
    margin-bottom: 1.1rem;
    text-align: center;
    font-family: var(--cs-font-mono);
    font-size: 0.68rem;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--cs-gold);
}

.cs-landing-receipt-list {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}

.cs-landing-receipt-item {
    display: flex;
    align-items: baseline;
    gap: 0.4rem;
}

.cs-landing-receipt-label {
    white-space: nowrap;
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--cs-ink-soft);
}

.cs-landing-receipt-dots {
    flex: 1;
    height: 0.6em;
    border-bottom: 1px dotted color-mix(in srgb, var(--cs-ink) 28%, transparent);
}

.cs-landing-receipt-detalle {
    white-space: nowrap;
    font-size: 0.76rem;
    color: var(--cs-faint);
}

.cs-landing-receipt-foot {
    display: flex;
    justify-content: space-between;
    margin-top: 1.35rem;
    padding-top: 0.9rem;
    border-top: 1px dashed var(--cs-line-strong);
    font-family: var(--cs-font-mono);
    font-size: 0.62rem;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--cs-faint);
}

.cs-landing-footer {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.7rem;
    padding: 1.5rem;
    border-top: 1px solid var(--cs-line);
    font-size: 0.72rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--cs-faint);
}

.cs-landing-footer-dot {
    width: 4px;
    height: 4px;
    border-radius: 999px;
    background: var(--cs-gold);
}

@media (max-width: 640px) {
    .cs-landing-receipt-item {
        flex-wrap: wrap;
    }

    .cs-landing-receipt-dots {
        display: none;
    }

    .cs-landing-receipt-detalle {
        width: 100%;
        white-space: normal;
    }

    .cs-landing-ticker {
        width: 100%;
        justify-content: space-between;
        gap: 1rem;
    }
}
</style>
