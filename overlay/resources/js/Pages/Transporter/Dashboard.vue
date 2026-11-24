<script setup>
import { Head } from '@inertiajs/vue3';
import PortalLayout from '../../Layouts/PortalLayout.vue';
</script>

<template>
    <Head title="Transporter" />

    <PortalLayout title="Transporter dashboard">
        <div class="rounded-lg border border-dashed border-gray-300 bg-white p-6 text-gray-600">
            Shipments assigned to your company will appear here once the logistics module is built.
        </div>
    </PortalLayout>
</template>
