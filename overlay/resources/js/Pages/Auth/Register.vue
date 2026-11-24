<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '../../Layouts/AuthLayout.vue';
import TextField from '../../Components/TextField.vue';

const form = useForm({
    name: '',
    company_name: '',
    email: '',
    mobile: '',
    gstin: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <AuthLayout title="Register your business" subtitle="Create a customer account to buy in bulk">
        <form class="space-y-5" @submit.prevent="submit">
            <TextField id="name" v-model="form.name" label="Your name" autocomplete="name" :error="form.errors.name" />
            <TextField
                id="company_name"
                v-model="form.company_name"
                label="Company name"
                autocomplete="organization"
                :error="form.errors.company_name"
            />
            <TextField id="email" v-model="form.email" type="email" label="Email" autocomplete="email" :error="form.errors.email" />
            <TextField
                id="mobile"
                v-model="form.mobile"
                type="tel"
                label="Mobile number"
                autocomplete="tel"
                hint="10-digit Indian mobile number"
                :error="form.errors.mobile"
            />
            <TextField
                id="gstin"
                v-model="form.gstin"
                label="GSTIN (optional)"
                hint="15 characters, if your business is GST registered"
                :error="form.errors.gstin"
            />
            <TextField
                id="password"
                v-model="form.password"
                type="password"
                label="Password"
                autocomplete="new-password"
                hint="At least 10 characters with upper and lower case letters and a number"
                :error="form.errors.password"
            />
            <TextField
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                label="Confirm password"
                autocomplete="new-password"
            />

            <button
                type="submit"
                class="w-full rounded-md bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-60"
                :disabled="form.processing"
            >
                Create account
            </button>

            <p class="text-center text-sm text-gray-600">
                Already registered?
                <Link href="/login" class="font-medium text-brand-700 hover:underline">Sign in</Link>
            </p>
        </form>
    </AuthLayout>
</template>
