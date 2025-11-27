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
import TaskSideMultiSelector from '@/components/TaskSideMultiSelector.vue';
import UserInfo from '@/components/UserInfo.vue';
import { marked } from 'marked';
import { Checkbox } from '@/components/ui/checkbox';

const props = defineProps<{
    task: Task;
}>();

const currentProject = computed<Project>(() => getProjectById(props.task.project_id) as Project);
const creatorUser = computed<User>(() => getUserById(props.task.create_from) as User);
const updaterUser = computed<User | undefined>(() => getUserById(props.task.updated_from));
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
            errors.value = {};
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

watch(() => [taskModel.value.task_type, taskModel.value.status, taskModel.value.project_id, taskModel.value.deadline, taskModel.value.executors, taskModel.value.responsibles], () => {
    router.patch(route('tasks.update', taskModel.value.id), {
        task_type: taskModel.value.task_type,
        status: taskModel.value.status,
        project_id: taskModel.value.project_id,
        executors: taskModel.value.executors,
        responsibles: taskModel.value.responsibles,
        deadline: new Date(taskModel.value.deadline),
    });
});

const comment = ref("");

// Process checkboxes in the description
const taskCheckboxes = computed(() => {
    if (!taskModel.value.description) return [];

    const checkboxRegex = /^\s*\[([\s*x])\]\s*(.+)$/gm;
    const matches = Array.from(taskModel.value.description.matchAll(checkboxRegex));

    return matches.map((match, index) => ({
        id: index,
        checked: match[1] === '*' || match[1] === 'x',
        text: match[2].trim(),
        original: match[0]
    }));
});

// Modified description without checkbox lines
const cleanDescription = computed(() => {
    if (!taskModel.value.description) return '';

    let desc = taskModel.value.description;
    taskCheckboxes.value.forEach(checkbox => {
        desc = desc.replace(checkbox.original, '');
    });

    // Remove any consecutive empty lines that might be left
    return desc.replace(/\n{3,}/g, '\n\n').trim();
});

const formattedDescription = computed(() => {
    if (editorMode.value) return taskModel.value.description;
    return marked.parse(cleanDescription.value); // Convert clean Markdown to HTML
});

function toggleTaskCheckbox(index: number) {
    if (editorMode.value) return;

    const checkbox = taskCheckboxes.value[index];
    const updatedDescription = taskModel.value.description.replace(
        checkbox.original,
        checkbox.original.replace(
            checkbox.checked ? '[*]' : '[ ]',
            checkbox.checked ? '[ ]' : '[*]'
        )
    );

    router.patch(route('tasks.update', taskModel.value.id), {
        description: updatedDescription
    }, {
        preserveScroll: true,
        onSuccess: () => {
            taskModel.value.description = updatedDescription;
        }
    });
}

function sendComment() {
    router.post(route('comment.store'),{
        task_id: props.task.id,
        comment: comment.value
    },{
        onSuccess: ()=> {
            comment.value = "";
        }
    })
}
const commnetsReversed = computed(()=> props.task.comments.reverse())
</script>

<template>
    <Head :title="props.task.name" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full flex-col gap-5 p-5 xl:flex-row">
            <div class="ms-auto flex flex-col gap-3 w-full xl:w-1/2">
                <div class="flex flex-row items-center gap-4 text-sm">
                    <Button @click="editorMode = !editorMode" class="-me-2 h-10 w-10 !text-xs" variant="outline"><Edit /></Button>
                    <span class="opacity-50">{{ currentProject.code_name }}-{{ task.id }}</span>
                    <span>Создал <span class="text-rose-500">{{ creatorUser.full_name }}</span> - {{ formatDate(task.created_at) }}</span>
                    <span v-if="task.updated_from && updaterUser">Обновил <span class="text-rose-500">{{ updaterUser.full_name }}</span> - {{ formatDate(task.updated_at) }}</span>
                </div>
                <Input v-if="editorMode" v-model="taskModel.name" placeholder="Заголовок задачи" />
                <h1 v-else class="space-x-2 text-xl font-semibold">{{ taskModel.name }}</h1>
                <Textarea v-if="editorMode" class="min-h-[300px]" autosize v-model="taskModel.description" placeholder="Подробное описание задачи" />
                <template v-else>
                    <!-- Regular markdown content -->
                    <div class="content break-all" v-html="formattedDescription"></div>

                    <!-- Task checklist section -->
                    <div v-if="taskCheckboxes.length > 0" class="mt-4 rounded-lg border p-4">
                        <h3 class="mb-2 font-medium">Подзадачи</h3>
                        <div v-for="(checkbox, index) in taskCheckboxes" :key="`task-${index}`" class="flex items-start space-x-2 mb-2">
                            <Checkbox
                                :id="`task-${index}`"
                                :checked="checkbox.checked"
                                @update:checked="toggleTaskCheckbox(index)"
                            />
                            <label :for="`task-${index}`" class="text-sm leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" :class="{ 'line-through opacity-70': checkbox.checked }">
                                {{ checkbox.text }}
                            </label>
                        </div>
                    </div>
                </template>
                <p v-if="editorMode" class="italic text-rose-500 text-sm opacity-70">Чтобы добавить подзадачу или чекбокс в любом месте описания добавьте "[ ] Текст задачи"</p>
                <p v-for="(error,key) in errors" :key="key" class="text-sm text-rose-600">{{error}}</p>
                <span class="mt-3 text-sm opacity-50">Прикреплённые файлы</span>
                <hr />
                <span class="-т mt-3 text-sm opacity-50">Комментарии и история</span>
                <p v-if="task.comments.length <= 0" class="py-4 text-center text-sm opacity-50">Нет комментариев</p>
                <div v-for="comment in commnetsReversed" :key="comment.id" class="flex flex-col gap-2 rounded-xl border border-t-0 border-b-0 border-e-0 p-2">
                    <div class="flex flex-row justify-between">
                        <div class="flex flex-row gap-2 items-center">
                            <UserInfo small :user="getUserById(comment.user_id) as User" show-email />
                        </div>
                        <span class="text-sm opacity-50">{{formatDate(comment.created_at)}}</span>
                    </div>

                    <p class="text-sm">{{comment.comment}}</p>
                </div>
                <div class="flex flex-row gap-1">
                    <Input v-model="comment" @keyup.enter="sendComment" placeholder="Введите сообщение" />
                    <Button @click.prevent="sendComment" class="-me-2 h-10 w-10 !text-xs">
                        <Send />
                    </Button>
                </div>
            </div>
            <div class="me-auto flex h-fit w-full flex-col gap-2 rounded border p-4 xl:w-64 shadow-md sticky top-5">
                <TaskSideSelector label="Проект*" :items="page.props.auth.projects" v-model="taskModel.project_id" />
                <TaskSideSelector label="Статус*" :items="page.props.auth.statuses" v-model="taskModel.status" />
                <TaskSideSelector label="Тип*" :items="page.props.auth.task_types" v-model="taskModel.task_type" />
                <TaskSideMultiSelector label="Ответственный*" :items="page.props.auth.users_list" show="full_name" v-model="taskModel.responsibles" />
                <TaskSideMultiSelector label="Исполнитель" :items="page.props.auth.users_list" show="full_name" v-model="taskModel.executors" />
                <TaskSideDateSelector label="Дедлайн" v-model="taskModel.deadline" />
                <Button v-if="editorMode" @click="submit" class="mt-5"> Сохранить </Button>
            </div>
        </div>
    </AppLayout>
</template>
<style scoped>
/* Apply deep styles to markdown content */
::v-deep .content {
    line-height: 1.6;
}

::v-deep .content h1, ::v-deep .content h2, ::v-deep .content h3, ::v-deep .content h4, ::v-deep .content h5, ::v-deep .content h6 {
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

::v-deep .content h1 {
    font-size: 1.75rem;
}

::v-deep .content h2 {
    font-size: 1.5rem;
}

::v-deep .content h3 {
    font-size: 1.25rem;
}

::v-deep .content h4, ::v-deep .content h5, ::v-deep .content h6 {
    font-size: 1rem;
}

::v-deep .content p {
    margin: 0.5rem 0;
}

::v-deep .content a {
    color: #1d72b8;
    text-decoration: underline;
}

::v-deep .content a:hover {
    color: #0f62a1;
    text-decoration: none;
}

::v-deep .content ul, ::v-deep .content ol {
    padding-left: 20px;
}

::v-deep .content li {
    margin-bottom: 0.5rem;
}

::v-deep .content blockquote {
    padding-left: 20px;
    border-left: 4px solid #ccc;
    margin: 1rem 0;
    font-style: italic;
}

::v-deep .content code {
    background-color: #f0f0f0;
    padding: 2px 6px;
    border-radius: 4px;
}

::v-deep .content pre code {
    display: block;
    padding: 10px;
    background-color: #282c34;
    color: white;
    border-radius: 5px;
    white-space: pre-wrap;
    word-wrap: break-word;
}

::v-deep .content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin-top: 1rem;
    margin-bottom: 1rem;
}

::v-deep .content hr {
    border: 1px solid #ccc;
    margin: 1rem 0;
}
</style>
