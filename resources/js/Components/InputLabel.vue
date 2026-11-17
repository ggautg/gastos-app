<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="cs-label" style="color: var(--cs-ink-soft);">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
