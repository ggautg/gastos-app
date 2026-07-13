<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-900 px-4">
        <Link href="/" class="flex items-center gap-2 mb-8">
            <div class="w-9 h-9 rounded-lg bg-teal-700 flex items-center justify-center text-white font-bold text-lg">
                ₲
            </div>
            <span class="font-semibold text-xl text-slate-800 dark:text-slate-100">CheSogue</span>
        </Link>

        <div class="w-full sm:max-w-md bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 rounded-xl px-6 py-8">
            <slot />
        </div>
    </div>
</template>