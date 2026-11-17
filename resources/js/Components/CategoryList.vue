<script setup>
defineProps({
    categories: Array,
    role: String,
});

defineEmits(['editar', 'eliminar']);
</script>

<template>
    <div class="cs-list">
        <div
            v-for="category in categories"
            :key="category.id"
            class="cs-list-row"
        >
            <div class="flex min-w-0 items-center gap-3">
                <span
                    class="h-2.5 w-2.5 shrink-0 rounded-full"
                    :style="{ backgroundColor: category.color, boxShadow: `0 0 0 4px ${category.color}20` }"
                ></span>
                <span v-if="category.icon" class="text-lg">{{ category.icon }}</span>
                <span class="cs-list-title truncate">{{ category.name }}</span>
                <span
                    class="cs-badge"
                    :style="category.type === 'gasto'
                        ? { background: 'color-mix(in srgb, var(--cs-terra) 13%, transparent)', color: 'color-mix(in srgb, var(--cs-terra) 72%, var(--cs-ink))' }
                        : { background: 'color-mix(in srgb, var(--cs-emerald) 13%, transparent)', color: 'color-mix(in srgb, var(--cs-emerald) 72%, var(--cs-ink))' }"
                >
                    {{ category.type === 'gasto' ? 'Gasto' : 'Ganancia' }}
                </span>
                <span v-if="category.budget" class="cs-amount hidden text-xs sm:inline" style="color: var(--cs-faint); font-weight: 500;">
                    tope ₲{{ new Intl.NumberFormat('es-PY').format(category.budget) }}
                </span>
            </div>

            <div v-if="role === 'owner'" class="flex shrink-0 items-center gap-1.5">
                <button type="button" @click="$emit('editar', category)" class="cs-text-btn px-2 py-1">
                    Editar
                </button>
                <span v-if="category.transactions_count > 0" class="text-xs cs-faint" title="Tiene movimientos: no se puede borrar">
                    En uso
                </span>
                <button v-else type="button" @click="$emit('eliminar', category)" class="cs-text-btn cs-text-btn-danger px-2 py-1">
                    Borrar
                </button>
            </div>
        </div>

        <p v-if="categories.length === 0" class="cs-empty">
            Todavía no creaste ninguna categoría.
        </p>
    </div>
</template>
