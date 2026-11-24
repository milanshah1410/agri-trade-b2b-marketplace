<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: undefined },
});
</script>

<template>
    <div class="flex min-h-screen flex-col justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <Link href="/" class="block text-center text-2xl font-bold text-brand-700">AGRI TRADE</Link>
            <h1 class="mt-4 text-center text-xl font-semibold text-gray-900">{{ title }}</h1>
            <p v-if="subtitle" class="mt-1 text-center text-sm text-gray-600">{{ subtitle }}</p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="rounded-lg bg-white px-6 py-8 shadow">
                <slot />
            </div>
        </div>
    </div>
</template>
