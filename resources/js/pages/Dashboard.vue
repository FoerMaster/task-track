<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Task } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import TaskRow from '@/components/TaskRow.vue';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Задачи',
        href: '/dashboard',
    },
];

const props = defineProps<{
    user: any
    tasks: Task[],
    totalItems: number,
    prePage: number,
    currentPage: number
}>()

const taskList = computed(() => props.tasks);

const totalPages = computed(() => Math.ceil(props.totalItems / props.prePage));

const goToPage = (page: number) => {
    if (page < 1 || page > totalPages.value) return;
    router.visit(`/dashboard?page=${page}`);
};

const pagesToShow = computed(() => {
    const pages = [];
    const siblingCount = 1; // Количество страниц по бокам от текущей
    const leftSiblingIndex = Math.max(props.currentPage - siblingCount, 1);
    const rightSiblingIndex = Math.min(props.currentPage + siblingCount, totalPages.value);

    for (let i = leftSiblingIndex; i <= rightSiblingIndex; i++) {
        pages.push(i);
    }

    return pages;
});
</script>

<template>
    <Head title="Задачи" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
                <div class="flex flex-col divide-y">
                    <TaskRow v-for="task in taskList" :key="task.id" :task="task" />
                </div>
            </div>
        </div>

        <!-- Пагинатор -->
        <div class="flex justify-center items-center gap-2 mt-4">
            <!-- Кнопка "Первая" -->
            <Button
                @click="goToPage(1)"
                :disabled="currentPage === 1"
                variant="outline"
            >
                Первая
            </Button>

            <!-- Кнопка "Назад" -->
            <Button
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
                variant="outline"
            >
                Назад
            </Button>

            <!-- Кнопки с номерами страниц -->
            <template v-for="page in pagesToShow" :key="page">
                <Button
                    @click="goToPage(page)"
                    :variant="page === currentPage ? 'primary' : 'outline'"
                >
                    {{ page }}
                </Button>
            </template>

            <!-- Кнопка "Вперед" -->
            <Button
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === totalPages"
                variant="outline"
            >
                Вперед
            </Button>

            <!-- Кнопка "Последняя" -->
            <Button
                @click="goToPage(totalPages)"
                :disabled="currentPage === totalPages"
                variant="outline"
            >
                Последняя
            </Button>
        </div>
    </AppLayout>
</template>
