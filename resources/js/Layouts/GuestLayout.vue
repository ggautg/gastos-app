<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="cs-guest">
        <svg class="cs-guest-nandu" viewBox="0 0 400 400" aria-hidden="true">
            <defs>
                <path id="cs-petal-g" d="M200,150 C210,120 230,110 240,130 C248,145 238,165 200,180 C162,165 152,145 160,130 C170,110 190,120 200,150 Z" />
            </defs>
            <g>
                <use v-for="n in 16" :key="n" href="#cs-petal-g" :transform="`rotate(${n * 22.5} 200 200)`" fill="none" :stroke="n % 2 === 0 ? 'var(--cs-teal)' : 'var(--cs-gold)'" stroke-width="1.2" />
            </g>
        </svg>

        <Link href="/" class="cs-guest-logo">
            <div class="cs-guest-mark">₲</div>
            <span class="cs-guest-word">CheSogue</span>
        </Link>

        <div class="cs-guest-card">
            <slot />
        </div>
    </div>
</template>

<style scoped>
.cs-guest {
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
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
}

:global(.dark) .cs-guest {
    --cs-paper: #0E1B18;
    --cs-paper-card: #14231F;
    --cs-ink: #E8F0EC;
}

.cs-guest-nandu {
    position: absolute;
    width: min(600px, 85vw);
    height: min(600px, 85vw);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0.12;
    pointer-events: none;
}

.cs-guest-logo {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin-bottom: 2rem;
}

.cs-guest-mark {
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 0.6rem;
    background: var(--cs-teal);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-family: 'JetBrains Mono', monospace;
    font-size: 1.1rem;
}

.cs-guest-word {
    font-family: 'Fraunces', serif;
    font-weight: 600;
    font-size: 1.3rem;
    color: var(--cs-ink);
}

.cs-guest-card {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 26rem;
    background: var(--cs-paper-card);
    border-radius: 0.9rem;
    padding: 2rem 1.75rem;
    box-shadow: 0 1px 3px color-mix(in srgb, var(--cs-ink) 8%, transparent), 0 8px 24px color-mix(in srgb, var(--cs-ink) 6%, transparent);
}
</style>