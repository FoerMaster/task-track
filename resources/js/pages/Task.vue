<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Project, type SharedData, Task, User } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Edit, Send } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { computed, ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import TaskSideSelector from '@/components/TaskSideSelector.vue';
import TaskSideDateSelector from '@/components/TaskSideDateSelector.vue';
import { getProjectById, getUserById } from '@/lib/utils';
import { toast } from '@/components/ui/toast';
import { formatDate } from '@/lib/datetime';

const props = defineProps<{
    task: Task;
}>();

const currentProject = computed<Project>(() => getProjectById(props.task.project_id) as Project);
const creatorUser = computed<User>(() => getUserById(props.task.create_from) as User);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: currentProject.value.name,
        href: `/projects/${currentProject.value.id}`,
    },
    {
        title: 'Задачи',
        href: `/projects/${currentProject.value.id}`,
    },
    {
        title: props.task.name,
        href: `/tasks/${props.task.id}`,
    },
];

const page = usePage<SharedData>();
const editorMode = ref(false);

const errors = ref({});

const taskModel = ref(props.task);
function submit() {
    router.patch(route('tasks.update',taskModel.value.id), {
        name: taskModel.value.name,
        description: taskModel.value.description,
        deadline: new Date(taskModel.value.deadline),
        status: taskModel.value.status,
        task_type: taskModel.value.task_type,
        project_id: taskModel.value.project_id,
    },{
        onSuccess: ()=> {
            editorMode.value = false;
        },
        onError: (message) => {
            errors.value = message;
            toast({
                title: 'Ошибка',
                description: "Проверьте все поля",
                variant: 'destructive',
            });
        }
    });
}

watch(() => [taskModel.value.task_type, taskModel.value.status, taskModel.value.project_id, taskModel.value.deadline], () => {
    router.patch(route('tasks.update', taskModel.value.id), {
        task_type: taskModel.value.task_type,
        status: taskModel.value.status,
        project_id: taskModel.value.project_id,
        deadline: new Date(taskModel.value.deadline),
    });
});
</script>

<template>
    <Head :title="props.task.name" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full flex-col gap-5 p-5 xl:flex-row">
            <div class="ms-auto flex flex-col gap-3 xl:w-1/2">
                <div class="flex flex-row items-center gap-4 text-sm">
                    <Button @click="editorMode = !editorMode" class="-me-2 h-10 w-10 !text-xs" variant="outline"><Edit /></Button>
                    <span>{{ currentProject.code_name }}-{{ task.id }}</span>
                    <span>Создал {{ creatorUser.full_name }} - {{ formatDate(task.created_at) }}</span>
                    <span v-if="task.updated_from">Обновил guest 4 месяца назад</span>
                </div>
                <Input v-if="editorMode" v-model="taskModel.name" placeholder="Заголовок задачи" />
                <h1 v-else class="space-x-2 text-xl font-semibold">{{ taskModel.name }}</h1>
                <Textarea v-if="editorMode" class="min-h-[300px]" autosize v-model="taskModel.description" placeholder="Подробное описание задачи" />
                <div v-else class="content">{{ taskModel.description }}</div>
                <span class="mt-3 text-sm opacity-50">Прикреплённые файлы</span>
                <hr />
                <span class="-т mt-3 text-sm opacity-50">Комментарии и история</span>
                <p class="py-4 text-center text-sm opacity-50">Нет комментариев</p>
                <div class="flex flex-row gap-1">
                    <Input placeholder="Введите сообщение" />
                    <Button class="-me-2 h-10 w-10 !text-xs">
                        <Send />
                    </Button>
                </div>
            </div>
            <div class="me-auto flex h-fit w-full flex-col gap-2 rounded border p-4 xl:w-64 shadow-md">
                <TaskSideSelector label="Проект*" :items="page.props.auth.projects" v-model="taskModel.project_id" />
                <TaskSideSelector label="Статус*" :items="page.props.auth.statuses" v-model="taskModel.status" />
                <TaskSideSelector label="Тип*" :items="page.props.auth.task_types" v-model="taskModel.task_type" />
                <!--                <TaskSideMultiSelector label="Ответственный" :items="page.props.auth.users_list" show="full_name" v-model="taskModel.assigned_users" />-->
                <!--                <TaskSideMultiSelector label="Исполнитель" :items="page.props.auth.users_list" show="full_name" v-model="taskModel.executor" />-->
                <TaskSideDateSelector label="Дедлайн" v-model="taskModel.deadline" />
                <Button v-if="editorMode" @click="submit" class="mt-5"> Сохранить </Button>
            </div>
        </div>
    </AppLayout>
</template>
