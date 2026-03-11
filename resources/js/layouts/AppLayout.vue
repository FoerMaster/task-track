<script setup lang="ts">
import { toast, Toaster } from '@/components/ui/toast';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const flash = computed(() => usePage().props.flash);

watch(
    () => flash.value,
    (newVal) => {
        if (newVal.message) {
            toast({
                title: 'Успешно',
                description: newVal.message,
                variant: 'success',
            });
        }

        if (newVal.error) {
            toast({
                title: 'Ошибка',
                description: newVal.error,
                variant: 'destructive',
            });
        }
    },
    { deep: true },
);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
    <Toaster />
</template>
