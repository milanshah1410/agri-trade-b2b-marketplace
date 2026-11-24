<script setup>
import { Head } from '@inertiajs/vue3';
import PortalLayout from '../../Layouts/PortalLayout.vue';
</script>

<template>
    <Head title="My account" />

    <PortalLayout title="Customer dashboard">
        <div class="rounded-lg border border-dashed border-gray-300 bg-white p-6 text-gray-600">
            Orders, payments, invoices and shipment tracking will appear here as those modules are built.
        </div>
    </PortalLayout>
</template>
