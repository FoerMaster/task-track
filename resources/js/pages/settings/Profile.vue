<script setup lang="ts">
import { TransitionRoot } from '@headlessui/vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import ImageSelector from '@/components/ImageSelector.vue';
import { ref } from 'vue';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
    timezones: string[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Настройки профиля',
        href: '/settings/profile',
    },
];

const loading = ref(false);
const avatarFile = ref<File>();
const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const form = useForm({
    full_name: user.full_name,
    timezone: user.timezone,
});

const submit = async () => {

    form.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            if (avatarFile.value) {
                router.post(route('profile.update'), {
                    _method: 'patch',
                    avatar: avatarFile.value,
                    ...form.data()
                });
            }
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Настройки профиля" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Информация" description="Обновите свою информацию о себе" />
                <div class="grid gap-2">
                    <Label>Аватар</Label>
                    <ImageSelector
                        v-model="avatarFile"
                        :current-image="user.avatar"
                        class="mt-1"
                        :error="('avatar' in page.props.errors) && page.props.errors.avatar"
                    />
                </div>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="fullName">ФИО</Label>
                        <Input
                            id="fullName"
                            class="mt-1 block w-full"
                            v-model="form.full_name"
                            required
                            autocomplete="fullName"
                            placeholder="Фамилия Имя Отчество"
                        />
                        <InputError class="mt-2" :message="form.errors.full_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Имя пользователя</Label>
                        <Input id="name" class="mt-1 block w-full" v-model="user.name" disabled autocomplete="name" placeholder="Имя пользователя" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Адрес электронной почты</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="user.email"
                            disabled
                            autocomplete="email"
                            placeholder="example@mail.ru"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="timezone">Часовой пояс</Label>
                        <Select id="timezone" class="mt-1 block w-full" v-model="form.timezone" required autocomplete="timezone">
                            <SelectTrigger>
                                <SelectValue placeholder="Выберите часовой пояс" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectItem v-for="timezone in timezones" :key="timezone" :value="timezone">

                                        {{ timezone }}

                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <InputError class="mt-2" :message="form.errors.timezone" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:!decoration-current dark:decoration-neutral-500"
                            >
                                Нажмите здесь, чтобы повторно отправить электронное письмо с подтверждением.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            На ваш электронный адрес была отправлена новая ссылка для подтверждения.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="loading || form.processing">Сохранить</Button>

                        <TransitionRoot
                            :show="form.recentlySuccessful"
                            enter="transition ease-in-out"
                            enter-from="opacity-0"
                            leave="transition ease-in-out"
                            leave-to="opacity-0"
                        >
                            <p class="text-sm text-neutral-600">Сохранено.</p>
                        </TransitionRoot>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
