<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
    <AuthLayout title="Подтверждение email" description="Пожалуйста, подтвердите свой email, кликнув на ссылку, которую мы только что отправили вам на почту.">
        <Head title="Подтверждение email" />

        <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-green-600">
            Новая ссылка для подтверждения была отправлена на email, который вы указали при регистрации.
        </div>

        <form @submit.prevent="submit" class="space-y-6 text-center">
            <Button :disabled="form.processing" variant="secondary">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                Отправить письмо для подтверждения снова
            </Button>

            <TextLink :href="route('logout')" method="post" as="button" class="mx-auto block text-sm"> Выйти </TextLink>
        </form>
    </AuthLayout>
</template>
