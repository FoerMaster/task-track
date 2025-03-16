<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Task } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Search } from 'lucide-vue-next'
import { Button } from '@/components/ui/button';
import TaskRow from '@/components/TaskRow.vue';
import { computed } from 'vue';
import { tasks } from '@/lib/moked';

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
            <div class="flex flex-row gap-2">
                <div class="w-fit">
                <Select id="timezone" autocomplete="timezone">
                    <SelectTrigger>
                        <SelectValue placeholder="Все проекты" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem value="timezone">TODO</SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
                </div>
                <input placeholder="Поиск" class="flex h-10 w-full rounded-md border transition-colors focus:duration-100 duration-500 focus:border-primary border-input bg-background px-3 py-2 text-base file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" />
                <Button variant="outline" size="icon" class="h-10 w-11">
                    <Search class="w-6 h-6" />
                </Button>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
                <div class="flex flex-col divide-y">
                    <TaskRow v-for="task in taskList" :key="task.id" :task="task" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
