<script setup>
import { computed } from 'vue';
import { Check, Palette } from 'lucide-vue-next';
import Dropdown from '@/Components/Dropdown.vue';
import { useTheme } from '@/Composables/useTheme';

defineProps({
    align: {
        type: String,
        default: 'right',
    },
    compact: {
        type: Boolean,
        default: false,
    },
});

const { theme, themes, setTheme } = useTheme();

const actual = computed(
    () => themes.find((preset) => preset.id === theme.value) ?? themes[0],
);
</script>

<template>
    <Dropdown :align="align" width="72" content-classes="p-1.5">
        <template #trigger>
            <button
                type="button"
                class="cs-theme-trigger"
                :class="{ 'is-compact': compact }"
                :title="`Tema: ${actual.name}`"
                :aria-label="`Cambiar tema (actual: ${actual.name})`"
            >
                <Palette class="cs-theme-trigger-icon" />
                <span v-if="!compact" class="cs-theme-trigger-label">
                    {{ actual.name }}
                </span>
            </button>
        </template>

        <template #content>
            <p class="cs-theme-title">Tema</p>

            <button
                v-for="preset in themes"
                :key="preset.id"
                type="button"
                class="cs-theme-option"
                :class="{ 'is-active': preset.id === theme }"
                :aria-pressed="preset.id === theme"
                @click="setTheme(preset.id)"
            >
                <span class="cs-theme-swatch" aria-hidden="true">
                    <span
                        v-for="color in preset.swatch"
                        :key="color"
                        :style="{ backgroundColor: color }"
                    />
                </span>

                <span class="cs-theme-copy">
                    <span class="cs-theme-name">{{ preset.name }}</span>
                    <span class="cs-theme-hint">{{ preset.hint }}</span>
                </span>

                <Check
                    v-if="preset.id === theme"
                    class="cs-theme-check"
                    aria-hidden="true"
                />
            </button>
        </template>
    </Dropdown>
</template>
