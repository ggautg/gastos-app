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
            class="w-full flex items-center gap-2 rounded-lg border border-slate-300 dark:border-slate-600 px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700"
        >
            <span v-if="modelValue" class="text-lg">{{ modelValue }}</span>
            <span class="text-slate-500 dark:text-slate-400">
                {{ modelValue ? 'Cambiar emoji' : 'Elegir emoji' }}
            </span>
        </button>

        <div v-if="showPicker" class="absolute z-50 mt-1">
            <EmojiPicker :native="true" @select="seleccionar" />
        </div>
    </div>
</template>