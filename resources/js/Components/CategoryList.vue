<script setup>
defineProps({
    categories: Array,
    role: String,
});

defineEmits(['editar', 'eliminar']);
</script>

<template>
    <div
        class="rounded-xl shadow-sm divide-y"
        style="background: var(--cs-paper-card); border: 1px solid color-mix(in srgb, var(--cs-ink) 12%, transparent);"
    >
        <div v-for="category in categories" :key="category.id" class="flex items-center justify-between px-4 py-3">
            <div class="flex items-center gap-3">
                <span class="w-3 h-3 rounded-full" :style="{ backgroundColor: category.color }"></span>
                <span v-if="category.icon" class="text-lg">{{ category.icon }}</span>
                <span class="font-medium" style="color: var(--cs-ink);">{{ category.name }}</span>
                <span
                    class="text-xs px-2 py-0.5 rounded-full font-medium"
                    :style="category.type === 'gasto'
                        ? { background: '#D85A3020', color: '#D85A30' }
                        : { background: '#1D9E7520', color: '#1D9E75' }"
                >
                    {{ category.type === 'gasto' ? 'Gasto' : 'Ganancia' }}
                </span>
                <span
                    v-if="category.budget"
                    class="text-xs"
                    style="font-family: 'JetBrains Mono', monospace; color: color-mix(in srgb, var(--cs-ink) 50%, transparent);"
                >
                    tope ₲{{ new Intl.NumberFormat('es-PY').format(category.budget) }}
                </span>
            </div>
            <div v-if="role === 'owner'" class="flex gap-3 text-sm">
                <button @click="$emit('editar', category)" style="color: color-mix(in srgb, var(--cs-ink) 55%, transparent);">
                    Editar
                </button>
                <button @click="$emit('eliminar', category)" class="text-red-500 hover:text-red-700">
                    Borrar
                </button>
            </div>
        </div>

        <p v-if="categories.length === 0" class="px-4 py-6 text-center text-sm" style="color: color-mix(in srgb, var(--cs-ink) 40%, transparent);">
            Todavía no creaste ninguna categoría.
        </p>
    </div>
</template>