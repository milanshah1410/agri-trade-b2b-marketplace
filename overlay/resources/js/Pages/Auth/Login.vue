<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '../../Layouts/AuthLayout.vue';
import TextField from '../../Components/TextField.vue';

const form = useForm({
    login: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Sign in" />

    <AuthLayout title="Sign in" subtitle="Use your email or mobile number">
        <form class="space-y-5" @submit.prevent="submit">
            <TextField
                id="login"
                v-model="form.login"
                label="Email or mobile number"
                autocomplete="username"
                :error="form.errors.login"
            />
            <TextField
                id="password"
                v-model="form.password"
                type="password"
                label="Password"
                autocomplete="current-password"
                :error="form.errors.password"
            />

            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input v-model="form.remember" type="checkbox" class="rounded border-gray-300 text-brand-600" />
                Remember me
            </label>

            <button
                type="submit"
                class="w-full rounded-md bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-60"
                :disabled="form.processing"
            >
                Sign in
            </button>

            <p class="text-center text-sm text-gray-600">
                New to AGRI TRADE?
                <Link href="/register" class="font-medium text-brand-700 hover:underline">Register your business</Link>
            </p>
        </form>
    </AuthLayout>
</template>
