<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { useDarkMode } from '@/Composables/useDarkMode';

const showingNavigationDropdown = ref(false);
const { isDark, toggle } = useDarkMode();

const links = [
    { route: 'dashboard', label: 'Dashboard' },
    { route: 'categories.index', label: 'Categorías' },
    { route: 'transactions.index', label: 'Movimientos' },
    { route: 'insights.index', label: 'Resumen' },
    { route: 'transactions.weekly', label: 'Semanal' },
    { route: 'summary.yearly', label: 'Resumen anual' },
    { route: 'household.show', label: 'Cuenta compartida' },
];
</script>

<template>
    <div class="min-h-screen" style="background: var(--cs-paper);">
        <nav
            class="sticky top-0 z-40 border-b backdrop-blur"
            style="background: color-mix(in srgb, var(--cs-paper-card) 92%, transparent); border-color: color-mix(in srgb, var(--cs-ink) 10%, transparent);"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <div class="flex items-center min-w-0">
                        <Link :href="route('dashboard')" class="shrink-0 flex items-center">
                            <ApplicationLogo />
                        </Link>

                        <div class="hidden lg:flex items-center gap-1 ml-8">
                            <Link
                                v-for="link in links"
                                :key="link.route"
                                :href="route(link.route)"
                                class="px-3 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap"
                                :style="route().current(link.route)
                                    ? { background: 'color-mix(in srgb, var(--cs-teal) 12%, transparent)', color: 'var(--cs-teal)' }
                                    : { color: 'color-mix(in srgb, var(--cs-ink) 60%, transparent)' }"
                            >
                                {{ link.label }}
                            </Link>
                        </div>
                    </div>

                    <div class="hidden lg:flex items-center gap-2 shrink-0">
                        <button
                            @click="toggle"
                            class="p-2 rounded-lg transition"
                            style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);"
                            title="Cambiar tema"
                        >
                            {{ isDark ? '☀️' : '🌙' }}
                        </button>

                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    type="button"
                                    class="flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium transition"
                                    style="color: color-mix(in srgb, var(--cs-ink) 70%, transparent);"
                                >
                                    {{ $page.props.auth.user.name }}
                                    <svg class="w-4 h-4 opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')">Perfil</DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Cerrar sesión
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>

                    <div class="-me-2 flex items-center lg:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center rounded-lg p-2 transition"
                            style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div
                v-show="showingNavigationDropdown"
                class="lg:hidden border-t"
                style="border-color: color-mix(in srgb, var(--cs-ink) 10%, transparent); background: var(--cs-paper-card);"
            >
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <Link
                        v-for="link in links"
                        :key="link.route"
                        :href="route(link.route)"
                        class="block px-3 py-2 rounded-lg text-sm font-medium"
                        :style="route().current(link.route)
                            ? { background: 'color-mix(in srgb, var(--cs-teal) 12%, transparent)', color: 'var(--cs-teal)' }
                            : { color: 'color-mix(in srgb, var(--cs-ink) 65%, transparent)' }"
                    >
                        {{ link.label }}
                    </Link>
                </div>

                <div class="border-t pt-4 pb-3" style="border-color: color-mix(in srgb, var(--cs-ink) 10%, transparent);">
                    <div class="px-4 flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="text-sm font-medium truncate" style="color: var(--cs-ink);">
                                {{ $page.props.auth.user.name }}
                            </p>
                            <p class="text-xs truncate" style="color: color-mix(in srgb, var(--cs-ink) 50%, transparent);">
                                {{ $page.props.auth.user.email }}
                            </p>
                        </div>
                        <button
                            @click="toggle"
                            class="p-2 rounded-lg shrink-0"
                            style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);"
                        >
                            {{ isDark ? '☀️' : '🌙' }}
                        </button>
                    </div>

                    <div class="mt-3 space-y-1 px-2">
                        <Link
                            :href="route('profile.edit')"
                            class="block px-3 py-2 rounded-lg text-sm"
                            style="color: color-mix(in srgb, var(--cs-ink) 65%, transparent);"
                        >
                            Perfil
                        </Link>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="block w-full text-left px-3 py-2 rounded-lg text-sm"
                            style="color: #D85A30;"
                        >
                            Cerrar sesión
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <header v-if="$slots.header" class="border-b" style="background: var(--cs-paper-card); border-color: color-mix(in srgb, var(--cs-ink) 8%, transparent);">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>