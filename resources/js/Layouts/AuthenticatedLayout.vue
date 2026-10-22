<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Menu, Moon, Sun, X } from 'lucide-vue-next';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
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
    <div class="cs-app min-h-screen">
        <nav class="cs-nav sticky top-0 z-40">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-[4.25rem] items-center justify-between gap-4">
                    <div class="flex min-w-0 items-center">
                        <Link :href="route('dashboard')" class="cs-brand shrink-0">
                            <ApplicationLogo />
                        </Link>

                        <div class="ml-9 hidden items-center gap-0.5 lg:flex">
                            <Link
                                v-for="link in links"
                                :key="link.route"
                                :href="route(link.route)"
                                class="cs-nav-link"
                                :class="{ 'is-active': route().current(link.route) }"
                            >
                                {{ link.label }}
                            </Link>
                        </div>
                    </div>

                    <div class="hidden shrink-0 items-center gap-1.5 lg:flex">
                        <button
                            type="button"
                            @click="toggle"
                            class="cs-icon-btn"
                            :title="isDark ? 'Modo claro' : 'Modo oscuro'"
                            :aria-label="isDark ? 'Activar modo claro' : 'Activar modo oscuro'"
                        >
                            <Sun v-if="isDark" class="h-[1.05rem] w-[1.05rem]" />
                            <Moon v-else class="h-[1.05rem] w-[1.05rem]" />
                        </button>

                        <span class="mx-1 h-5 w-px" style="background: var(--cs-line);" aria-hidden="true" />

                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button type="button" class="cs-nav-user">
                                    <span
                                        class="flex h-6 w-6 items-center justify-center rounded-full text-[0.68rem] font-semibold uppercase"
                                        style="background: color-mix(in srgb, var(--cs-teal) 14%, transparent); color: var(--cs-teal);"
                                    >
                                        {{ $page.props.auth.user.name?.charAt(0) }}
                                    </span>
                                    {{ $page.props.auth.user.name }}
                                    <svg class="h-3.5 w-3.5 opacity-40" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
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
                            type="button"
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="cs-icon-btn"
                            aria-label="Abrir menú"
                        >
                            <X v-if="showingNavigationDropdown" class="h-5 w-5" />
                            <Menu v-else class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </div>

            <div v-show="showingNavigationDropdown" class="cs-mobile-menu lg:hidden">
                <div class="space-y-0.5 px-3 pb-3 pt-3">
                    <Link
                        v-for="link in links"
                        :key="link.route"
                        :href="route(link.route)"
                        class="cs-nav-link w-full"
                        :class="{ 'is-active': route().current(link.route) }"
                    >
                        {{ link.label }}
                    </Link>
                </div>

                <div class="border-t px-4 pb-5 pt-4" style="border-color: var(--cs-line);">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold" style="color: var(--cs-ink);">
                                {{ $page.props.auth.user.name }}
                            </p>
                            <p class="truncate text-xs" style="color: var(--cs-faint);">
                                {{ $page.props.auth.user.email }}
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="toggle"
                            class="cs-icon-btn shrink-0"
                            :aria-label="isDark ? 'Activar modo claro' : 'Activar modo oscuro'"
                        >
                            <Sun v-if="isDark" class="h-4 w-4" />
                            <Moon v-else class="h-4 w-4" />
                        </button>
                    </div>

                    <div class="mt-3 space-y-0.5">
                        <Link
                            :href="route('profile.edit')"
                            class="cs-nav-link w-full"
                        >
                            Perfil
                        </Link>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="cs-nav-link w-full text-left"
                            style="color: var(--cs-terra);"
                        >
                            Cerrar sesión
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <header v-if="$slots.header" class="cs-page-header">
            <div class="mx-auto max-w-7xl px-4 py-7 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main class="pb-16">
            <slot />
        </main>
    </div>
</template>
