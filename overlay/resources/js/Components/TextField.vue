<script setup>
defineProps({
    label: { type: String, required: true },
    id: { type: String, required: true },
    type: { type: String, default: 'text' },
    autocomplete: { type: String, default: undefined },
    error: { type: String, default: undefined },
    hint: { type: String, default: undefined },
});

const model = defineModel({ type: String, default: '' });
</script>

<template>
    <div>
        <label :for="id" class="block text-sm font-medium text-gray-700">{{ label }}</label>
        <input
            :id="id"
            v-model="model"
            :type="type"
            :autocomplete="autocomplete"
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600"
            :class="{ 'border-red-500': error }"
        />
        <p v-if="hint && !error" class="mt-1 text-xs text-gray-500">{{ hint }}</p>
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>
