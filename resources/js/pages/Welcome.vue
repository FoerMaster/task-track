<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Abris" />

    <div class="flex min-h-screen items-center justify-center bg-white px-6 py-12 text-neutral-900 dark:bg-neutral-950 dark:text-neutral-100">
        <div class="w-full max-w-md space-y-6 rounded-2xl border border-neutral-200 p-8 shadow-sm dark:border-neutral-800">
            <div class="space-y-2">
                <p class="text-sm uppercase tracking-wide text-neutral-500">Abris</p>
                <h1 class="text-2xl font-semibold">Task management for teams</h1>
                <p class="text-sm text-neutral-600 dark:text-neutral-400">Sign in to your workspace or create an account to get started.</p>
            </div>

            <div class="flex gap-3">
                <Link
                    :href="route('login')"
                    class="inline-flex flex-1 items-center justify-center rounded-lg bg-neutral-900 px-4 py-2 text-sm font-medium text-white hover:bg-neutral-800 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-100"
                >
                    Log in
                </Link>
                <Link
                    :href="route('register')"
                    class="inline-flex flex-1 items-center justify-center rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-900 hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-100 dark:hover:bg-neutral-900"
                >
                    Register
                </Link>
            </div>
        </div>
    </div>
</template>
