<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const user = computed(() => usePage().props.auth?.user);
</script>

<template>
    <Head title="Home" />

    <div class="flex min-h-screen flex-col items-center justify-center bg-brand-50 px-4 text-center">
        <h1 class="text-4xl font-bold text-brand-800">AGRI TRADE</h1>
        <p class="mt-3 max-w-xl text-gray-700">
            All India B2B marketplace for agricultural commodities. The product catalogue arrives in the next phase.
        </p>

        <div class="mt-8 flex gap-3">
            <template v-if="user">
                <a href="/portal" class="rounded-md bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
                    Go to my account
                </a>
            </template>
            <template v-else>
                <Link href="/login" class="rounded-md bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
                    Sign in
                </Link>
                <Link href="/register" class="rounded-md border border-brand-600 px-5 py-2.5 text-sm font-semibold text-brand-700 hover:bg-white">
                    Register your business
                </Link>
            </template>
        </div>
    </div>
</template>
