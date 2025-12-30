<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { toast } from '@/components/ui/toast';
import { Toaster } from '@/components/ui/toast';
import { BreadcrumbItemType } from '@/types';

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
        console.log(newVal)
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
    { deep: true }
);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
    <Toaster />
</template>
