<script setup>
import { Link } from '@inertiajs/vue3';
import ThemeSelector from '@/Components/ThemeSelector.vue';
</script>

<template>
    <div class="cs-guest">
        <div class="cs-guest-aura" aria-hidden="true"></div>

        <div class="cs-guest-theme">
            <ThemeSelector />
        </div>

        <svg class="cs-guest-nandu" viewBox="0 0 400 400" aria-hidden="true">
            <defs>
                <path id="cs-petal-g" d="M200,150 C210,120 230,110 240,130 C248,145 238,165 200,180 C162,165 152,145 160,130 C170,110 190,120 200,150 Z" />
            </defs>
            <g>
                <use v-for="n in 16" :key="n" href="#cs-petal-g" :transform="`rotate(${n * 22.5} 200 200)`" fill="none" :stroke="n % 2 === 0 ? 'var(--cs-teal)' : 'var(--cs-gold)'" stroke-width="1" />
            </g>
        </svg>

        <Link href="/" class="cs-guest-logo">
            <span class="cs-brand-mark">₲</span>
            <span class="cs-brand-word">CheSogue</span>
        </Link>

        <div class="cs-guest-card">
            <slot />
        </div>

        <p class="cs-guest-foot">Finanzas claras, decisiones simples.</p>
    </div>
</template>

<style scoped>
.cs-guest {
    position: relative;
    display: flex;
    min-height: 100vh;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 1.5rem;
    background-color: var(--cs-paper);
    color: var(--cs-ink);
}

.cs-guest-aura {
    position: absolute;
    inset: 0;
    pointer-events: none;
    background:
        radial-gradient(60% 55% at 50% -10%, color-mix(in srgb, var(--cs-teal) 22%, transparent), transparent 70%),
        radial-gradient(45% 40% at 100% 100%, color-mix(in srgb, var(--cs-gold) 18%, transparent), transparent 65%);
}

.cs-guest-nandu {
    position: absolute;
    top: 50%;
    left: 50%;
    width: min(680px, 92vw);
    height: min(680px, 92vw);
    transform: translate(-50%, -50%);
    opacity: 0.09;
    pointer-events: none;
}

.dark .cs-guest-nandu {
    opacity: 0.14;
}

.cs-guest-theme {
    position: absolute;
    top: 1.25rem;
    right: 1.25rem;
    z-index: 2;
}

.cs-guest-logo {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 0.7rem;
    margin-bottom: 2rem;
    transition: opacity 0.2s var(--cs-ease);
}

.cs-guest-logo:hover {
    opacity: 0.85;
}

.cs-guest-card {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 27rem;
    padding: 2.15rem 1.9rem;
    border: 1px solid var(--cs-line);
    border-radius: var(--cs-radius-xl);
    background: var(--cs-paper-card);
    box-shadow: var(--cs-shadow-lg);
}

.cs-guest-card::before {
    content: '';
    position: absolute;
    inset: 0 0 auto;
    height: 1px;
    border-radius: inherit;
    background: linear-gradient(
        90deg,
        transparent,
        color-mix(in srgb, var(--cs-gold) 60%, transparent),
        transparent
    );
}

.cs-guest-foot {
    position: relative;
    z-index: 1;
    margin-top: 1.75rem;
    font-size: 0.72rem;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--cs-faint);
}
</style>
