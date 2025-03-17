<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Task } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Search } from 'lucide-vue-next'
import { Button } from '@/components/ui/button';
import TaskRow from '@/components/TaskRow.vue';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Задачи',
        href: '/dashboard',
    },
];

const props =  defineProps<{
    user: any
    tasks: Task[]
}>()

const taskList = computed(()=>props.tasks)
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
    </AppLayout>
</template>
