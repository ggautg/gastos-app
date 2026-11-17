<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message">
        <p class="cs-error" style="color: var(--cs-terra);">
            {{ message }}
        </p>
    </div>
</template>
