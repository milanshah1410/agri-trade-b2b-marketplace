<script setup>
import { Head } from '@inertiajs/vue3';
import PortalLayout from '../../Layouts/PortalLayout.vue';
</script>

<template>
    <Head title="Driver" />

    <PortalLayout title="Driver dashboard">
        <div class="rounded-lg border border-dashed border-gray-300 bg-white p-6 text-gray-600">
            Your assigned deliveries will appear here once the logistics module is built.
        </div>
    </PortalLayout>
</template>
