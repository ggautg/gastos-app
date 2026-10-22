<script setup>
import { ref } from 'vue';
import EmojiPicker from 'vue3-emoji-picker';
import 'vue3-emoji-picker/css';

defineProps({
    modelValue: String,
});

const emit = defineEmits(['update:modelValue']);

const showPicker = defineModel('showPicker', { default: false });

function seleccionar(emoji) {
    emit('update:modelValue', emoji.i);
    showPicker.value = false;
}
</script>

<template>
    <div class="relative">
        <button
            type="button"
            @click="showPicker = !showPicker"
            class="cs-input flex w-full cursor-pointer items-center gap-2 text-left text-sm"
        >
            <span v-if="modelValue" class="text-lg">{{ modelValue }}</span>
            <span style="color: var(--cs-muted);">
                {{ modelValue ? 'Cambiar emoji' : 'Elegir emoji' }}
            </span>
        </button>

        <div v-if="showPicker" class="cs-panel absolute z-50 mt-1 overflow-hidden">
            <EmojiPicker :native="true" @select="seleccionar" />
        </div>
    </div>
</template>