<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

defineProps({
    title: { type: String, required: true },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

const logout = () => router.post('/logout');
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
                <Link href="/" class="text-lg font-bold text-brand-700">AGRI TRADE</Link>
                <div class="flex items-center gap-4 text-sm">
                    <span v-if="user" class="text-gray-700">{{ user.name }}</span>
                    <button
                        type="button"
                        class="rounded-md border border-gray-300 px-3 py-1.5 font-medium text-gray-700 hover:bg-gray-100"
                        @click="logout"
                    >
                        Sign out
                    </button>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
            <h1 class="text-2xl font-semibold text-gray-900">{{ title }}</h1>
            <div class="mt-6">
                <slot />
            </div>
        </main>
    </div>
</template>
