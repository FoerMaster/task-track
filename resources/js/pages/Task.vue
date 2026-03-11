<script setup lang="ts">
import TaskSideDateSelector from '@/components/TaskSideDateSelector.vue';
import TaskSideMultiSelector from '@/components/TaskSideMultiSelector.vue';
import TaskSideSelector from '@/components/TaskSideSelector.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { toast } from '@/components/ui/toast';
import UserInfo from '@/components/UserInfo.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatDate } from '@/lib/datetime';
import { getProjectById, getUserById } from '@/lib/utils';
import { type BreadcrumbItem, Project, type SharedData, Task, User } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Edit, File, Image, Play, Send, Sheet, Upload, X } from 'lucide-vue-next';
import { marked } from 'marked';
import { computed, ref, watch } from 'vue';

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
    router.patch(
        route('tasks.update', taskModel.value.id),
        {
            name: taskModel.value.name,
            description: taskModel.value.description,
            deadline: new Date(taskModel.value.deadline),
            status: taskModel.value.status,
            task_type: taskModel.value.task_type,
            project_id: taskModel.value.project_id,
        },
        {
            onSuccess: () => {
                editorMode.value = false;
                errors.value = {};
            },
            onError: (message) => {
                errors.value = message;
                toast({
                    title: 'Ошибка',
                    description: 'Проверьте все поля',
                    variant: 'destructive',
                });
            },
        },
    );
}

watch(
    () => [
        taskModel.value.task_type,
        taskModel.value.status,
        taskModel.value.project_id,
        taskModel.value.deadline,
        taskModel.value.executors,
        taskModel.value.responsibles,
    ],
    () => {
        router.patch(route('tasks.update', taskModel.value.id), {
            task_type: taskModel.value.task_type,
            status: taskModel.value.status,
            project_id: taskModel.value.project_id,
            executors: taskModel.value.executors,
            responsibles: taskModel.value.responsibles,
            deadline: new Date(taskModel.value.deadline),
        });
    },
);

const comment = ref('');

const taskCheckboxes = computed(() => {
    if (!taskModel.value.description) return [];

    const checkboxRegex = /^\s*\[([\s*x])\]\s*(.+)$/gm;
    const matches = Array.from(taskModel.value.description.matchAll(checkboxRegex));

    return matches.map((match, index) => ({
        id: index,
        checked: match[1] === '*' || match[1] === 'x',
        text: match[2].trim(),
        original: match[0],
    }));
});

const cleanDescription = computed(() => {
    if (!taskModel.value.description) return '';

    let desc = taskModel.value.description;
    taskCheckboxes.value.forEach((checkbox) => {
        desc = desc.replace(checkbox.original, '');
    });

    return desc.replace(/\n{3,}/g, '\n\n').trim();
});

const formattedDescription = computed(() => {
    if (editorMode.value) return taskModel.value.description;
    return marked.parse(cleanDescription.value);
});

function toggleTaskCheckbox(index: number) {
    if (editorMode.value) return;

    const checkbox = taskCheckboxes.value[index];
    const updatedDescription = taskModel.value.description.replace(
        checkbox.original,
        checkbox.original.replace(checkbox.checked ? '[*]' : '[ ]', checkbox.checked ? '[ ]' : '[*]'),
    );

    router.patch(
        route('tasks.update', taskModel.value.id),
        {
            description: updatedDescription,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                taskModel.value.description = updatedDescription;
            },
        },
    );
}

function sendComment() {
    router.post(
        route('comment.store'),
        {
            task_id: props.task.id,
            comment: comment.value,
        },
        {
            onSuccess: () => {
                comment.value = '';
            },
        },
    );
}
const commentsReversed = computed(() => [...props.task.comments].reverse());

const files = ref<File[]>([]);
const dropZone = ref<HTMLDivElement>();
const isDragActive = ref(false);

function onFileSelect(e: Event) {
    const input = e.target as HTMLInputElement;
    if (input.files?.length) {
        files.value = [...files.value, ...Array.from(input.files)];
    }
}

function onDrop(e: DragEvent) {
    e.preventDefault();
    isDragActive.value = false;

    if (e.dataTransfer?.files.length) {
        files.value = [...files.value, ...Array.from(e.dataTransfer.files)];
    }
}

function removeFile(index: number) {
    files.value.splice(index, 1);
}

function getFileExtension(filename) {
    return filename.split('.').pop().toLowerCase();
}

function getFileIcon(filename) {
    const ext = getFileExtension(filename);

    const iconMap = {
        // Документы
        pdf: Send,
        doc: Edit,
        docx: Edit,
        txt: Edit,
        rtf: Edit,

        // Таблицы
        xls: Sheet,
        xlsx: Sheet,
        csv: Sheet,

        // Изображения
        png: Image,
        jpg: Image,
        jpeg: Image,
        gif: Image,
        svg: Image,
        webp: Image,

        // Архивы
        zip: File,
        rar: File,
        tar: File,
        gz: File,

        // Аудио
        mp3: Play,
        wav: Play,
        ogg: Play,
    };

    return iconMap[ext] || File; // File как иконка по умолчанию
}

function sendFiles() {
    //todo
    router.post(
        route('tasks.attachment.add'),
        {
            files: files.value,
            task_id: props.task.id,
        },
        {
            forceFormData: true,
            onSuccess: () => {
                files.value = [];
                toast({
                    title: 'Успешно',
                    description: 'Файлы добавлены!',
                });
            },
        },
    );
}
</script>

<template>
    <Head :title="props.task.name" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full flex-col gap-5 p-5 xl:flex-row">
            <div class="ms-auto flex w-full flex-col gap-3 xl:w-1/2">
                <div class="flex flex-row items-center gap-4 text-sm">
                    <Button @click="editorMode = !editorMode" class="-me-2 h-10 w-10 !text-xs" variant="outline"><Edit /></Button>
                    <span class="opacity-50">{{ currentProject.code_name }}-{{ task.id }}</span>
                    <span
                        >Создал <span class="text-rose-500">{{ creatorUser.full_name }}</span> - {{ formatDate(task.created_at) }}</span
                    >
                    <span v-if="task.updated_from && updaterUser"
                        >Обновил <span class="text-rose-500">{{ updaterUser.full_name }}</span> - {{ formatDate(task.updated_at) }}</span
                    >
                </div>
                <Input v-if="editorMode" v-model="taskModel.name" placeholder="Заголовок задачи" />
                <h1 v-else class="space-x-2 text-xl font-semibold">{{ taskModel.name }}</h1>
                <Textarea v-if="editorMode" class="min-h-[300px]" autosize v-model="taskModel.description" placeholder="Подробное описание задачи" />
                <template v-else>
                    <div class="content break-all" v-html="formattedDescription"></div>
                    <div v-if="taskCheckboxes.length > 0" class="mt-4 rounded-lg border p-4">
                        <h3 class="mb-2 font-medium">Подзадачи</h3>
                        <div v-for="(checkbox, index) in taskCheckboxes" :key="`task-${index}`" class="mb-2 flex items-start space-x-2">
                            <Checkbox :id="`task-${index}`" :checked="checkbox.checked" @update:checked="toggleTaskCheckbox(index)" />
                            <label
                                :for="`task-${index}`"
                                class="text-sm leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                :class="{ 'line-through opacity-70': checkbox.checked }"
                            >
                                {{ checkbox.text }}
                            </label>
                        </div>
                    </div>
                </template>
                <p v-if="editorMode" class="text-sm italic text-rose-500 opacity-70">
                    Чтобы добавить подзадачу или чекбокс в любом месте описания добавьте "[ ] Текст задачи"
                </p>
                <p v-for="(error, key) in errors" :key="key" class="text-sm text-rose-600">{{ error }}</p>
                <div class="space-y-3">
                    <span class="mt-3 text-sm opacity-50">Прикреплённые файлы</span>
                    <div class="flex flex-wrap gap-2">
                        <div
                            v-for="attachment in task.attachments"
                            :key="attachment.id"
                            class="flex flex-row items-center gap-1 rounded-lg border p-2 px-3"
                        >
                            <!-- Иконка файла -->
                            <component :is="getFileIcon(attachment.file_name)" class="mr-2 h-4 w-4 flex-shrink-0" />

                            <!-- Ссылка на файл -->
                            <a :href="`/${attachment.attachment_url}`" target="_blank" class="truncate text-xs">
                                {{ attachment.file_name }}
                            </a>
                            <Button
                                @click="router.post(route('tasks.attachment.destroy'), { attachment_id: attachment.id, _method: 'delete' })"
                                variant="ghost"
                                class="!h-8 !w-5"
                                ><X
                            /></Button>
                        </div>
                    </div>
                    <!-- Область для drag'n'drop -->
                    <div
                        ref="dropZone"
                        @dragover.prevent="isDragActive = true"
                        @dragleave.prevent="isDragActive = false"
                        @drop.prevent="onDrop"
                        :class="[
                            'cursor-pointer rounded-lg border-2 border-dashed p-8 text-center',
                            isDragActive ? 'border-primary bg-primary/10' : 'border-muted-foreground/30',
                        ]"
                        @click="$refs.fileInput.click()"
                    >
                        <Upload class="mx-auto mb-2 h-8 w-8 text-muted-foreground" />
                        <p class="text-sm text-muted-foreground">Перетащите файлы сюда или кликните для выбора</p>
                        <input ref="fileInput" type="file" multiple class="hidden" @change="onFileSelect" />
                    </div>

                    <!-- Список выбранных файлов -->
                    <div v-if="files.length" class="space-y-2">
                        <div v-for="(file, index) in files" :key="file.name + index" class="flex items-center justify-between rounded border p-2">
                            <span class="truncate text-sm">{{ file.name }}</span>
                            <Button variant="ghost" size="sm" class="h-8 w-8 p-0" @click.stop="removeFile(index)">
                                <X class="h-4 w-4" />
                            </Button>
                        </div>
                        <Button @click.prevent="sendFiles" class="w-full"> Прикрепить файлы </Button>
                    </div>
                </div>

                <hr />
                <span class="-т mt-3 text-sm opacity-50">Комментарии и история</span>
                <p v-if="task.comments.length <= 0" class="py-4 text-center text-sm opacity-50">Нет комментариев</p>

                <div
                    v-for="comment in commentsReversed"
                    :key="comment.id"
                    class="flex flex-col gap-2 rounded-xl border border-b-0 border-e-0 border-t-0 p-2"
                >
                    <div v-if="comment.comment.startsWith('![ACTION]')" class="flex flex-col opacity-70">
                        <div class="flex flex-row justify-between">
                            <div class="flex flex-row items-center gap-2">
                                <UserInfo small :user="getUserById(comment.user_id) as User" show-email />
                            </div>
                            <span class="text-sm opacity-50">Выполнил изменение - {{ formatDate(comment.created_at) }}</span>
                        </div>
                        <p class="text-sm" v-html="comment.comment.replace('![ACTION] ', '')"></p>
                    </div>

                    <div v-if="!comment.comment.startsWith('![ACTION]')" class="flex flex-row justify-between">
                        <div class="flex flex-row items-center gap-2">
                            <UserInfo small :user="getUserById(comment.user_id) as User" show-email />
                        </div>
                        <span class="text-sm opacity-50">{{ formatDate(comment.created_at) }}</span>
                    </div>

                    <p v-if="!comment.comment.startsWith('![ACTION]')" class="text-sm">{{ comment.comment }}</p>
                </div>
                <div class="flex flex-row gap-1">
                    <Input v-model="comment" @keyup.enter="sendComment" placeholder="Введите сообщение" />
                    <Button @click.prevent="sendComment" class="-me-2 h-10 w-10 !text-xs">
                        <Send />
                    </Button>
                </div>
            </div>
            <div class="sticky top-5 me-auto flex h-fit w-full flex-col gap-2 rounded border p-4 shadow-md xl:w-64">
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
:deep(.content) {
    line-height: 1.6;
}

:deep(.content h1),
:deep(.content h2),
:deep(.content h3),
:deep(.content h4),
:deep(.content h5),
:deep(.content h6) {
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

:deep(.content h1) {
    font-size: 1.75rem;
}

:deep(.content h2) {
    font-size: 1.5rem;
}

:deep(.content h3) {
    font-size: 1.25rem;
}

:deep(.content h4),
:deep(.content h5),
:deep(.content h6) {
    font-size: 1rem;
}

:deep(.content p) {
    margin: 0.5rem 0;
}

:deep(.content a) {
    color: #1d72b8;
    text-decoration: underline;
}

:deep(.content a:hover) {
    color: #0f62a1;
    text-decoration: none;
}

:deep(.content ul),
:deep(.content ol) {
    padding-left: 20px;
}

:deep(.content li) {
    margin-bottom: 0.5rem;
}

:deep(.content blockquote) {
    padding-left: 20px;
    border-left: 4px solid #ccc;
    margin: 1rem 0;
    font-style: italic;
}

:deep(.content code) {
    background-color: #f0f0f0;
    padding: 2px 6px;
    border-radius: 4px;
}

:deep(.content pre code) {
    display: block;
    padding: 10px;
    background-color: #282c34;
    color: white;
    border-radius: 5px;
    white-space: pre-wrap;
    word-wrap: break-word;
}

:deep(.content img) {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin-top: 1rem;
    margin-bottom: 1rem;
}

:deep(.content hr) {
    border: 1px solid #ccc;
    margin: 1rem 0;
}
</style>
