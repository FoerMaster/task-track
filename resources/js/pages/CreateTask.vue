<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Edit, Send } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { computed, ref } from 'vue';
import { tasks } from '@/lib/moked';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover'
import { ScrollArea } from '@/components/ui/scroll-area';
import TaskSideSelector from '@/components/TaskSideSelector.vue';
import TaskSideMultiSelector from '@/components/TaskSideMultiSelector.vue';
import TaskSideDateSelector from '@/components/TaskSideDateSelector.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Новая задача',
        href: '/dashboard',
    },
];

defineProps<{
    user: any;
}>();

const selectedProject = ref(0);
const page = usePage<SharedData>();
const editorMode = ref(true);

const taskModel = ref({
    name: '',
    description: '',
    assigned_users: [],
    executor: [],
    dead_line: "",
    status: undefined,
    type_type: undefined,
    project: undefined,
});

function submit() {
    router.post(route('tasks.store'),{
        name: taskModel.value.name,
        description:  taskModel.value.description,
        deadline:  new Date(taskModel.value.dead_line),
        status:  taskModel.value.status,
        task_type:  taskModel.value.type_type,
        project_id: taskModel.value.project
    })
}
</script>

<template>
    <Head title="Задача" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full xl:flex-row flex-col gap-5 p-5 ">
            <div class="flex flex-col gap-3 ms-auto w-full xl:w-1/2">
                <Input v-model="taskModel.name" placeholder="Заголовок задачи" />
                <Textarea class="min-h-[300px]" autosize v-model="taskModel.description" placeholder="Подробное описание задачи" />
                <span class="mt-3 text-sm opacity-50">Прикреплённые файлы</span>
                <hr />
            </div>
            <div class="flex flex-col gap-2 me-auto rounded border p-4 h-fit xl:w-64 w-full">
                <TaskSideSelector label="Проект*" :items="page.props.auth.projects" v-model="taskModel.project" />
                <TaskSideSelector label="Статус*" :items="page.props.auth.statuses" v-model="taskModel.status" />
                <TaskSideSelector label="Тип*" :items="page.props.auth.task_types" v-model="taskModel.type_type" />
                <TaskSideMultiSelector label="Ответственный" :items="page.props.auth.users_list" show="full_name" v-model="taskModel.assigned_users" />
                <TaskSideMultiSelector label="Исполнитель" :items="page.props.auth.users_list" show="full_name" v-model="taskModel.executor" />
                <TaskSideDateSelector label="Дедлайн" v-model="taskModel.dead_line" />
                <Button @click="submit" class="mt-5">
                    Сохранить и создать
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
