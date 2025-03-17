<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ref } from 'vue';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import TaskSideSelector from '@/components/TaskSideSelector.vue';
import TaskSideMultiSelector from '@/components/TaskSideMultiSelector.vue';
import TaskSideDateSelector from '@/components/TaskSideDateSelector.vue';
import { toast } from '@/components/ui/toast';
import { Upload, X } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Новая задача',
        href: '/dashboard',
    },
];

defineProps<{
    user: any;
}>();

const page = usePage<SharedData>();

const errors = ref({});

const taskModel = ref({
    name: '',
    description: '',
    responsibles: [],
    executors: [],
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
        project_id: taskModel.value.project,
        responsibles: taskModel.value.responsibles,
        executors: taskModel.value.executors,
        files: files.value,
    },{
        forceFormData: true,
        onError: (message) => {
            errors.value = message;
            toast({
                title: 'Ошибка',
                description: "Проверьте все поля",
                variant: 'destructive',
            });
        }
    })
}

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
</script>

<template>
    <Head title="Задача" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full xl:flex-row flex-col gap-5 p-5 ">
            <div class="flex flex-col gap-3 ms-auto w-full xl:w-1/2">
                <Input v-model="taskModel.name" placeholder="Заголовок задачи" />
                <Textarea class="min-h-[300px]" autosize v-model="taskModel.description" placeholder="Подробное описание задачи" />
                <p v-for="(error,key) in errors" :key="key" class="text-sm text-rose-600">{{error}}</p>
                <div class="space-y-3">
                    <span class="mt-3 text-sm opacity-50">Прикреплённые файлы</span>

                    <!-- Область для drag'n'drop -->
                    <div
                        ref="dropZone"
                        @dragover.prevent="isDragActive = true"
                        @dragleave.prevent="isDragActive = false"
                        @drop.prevent="onDrop"
                        :class="[
          'border-2 border-dashed rounded-lg p-8 text-center cursor-pointer',
          isDragActive ? 'border-primary bg-primary/10' : 'border-muted-foreground/30'
        ]"
                        @click="$refs.fileInput.click()"
                    >
                        <Upload class="mx-auto h-8 w-8 text-muted-foreground mb-2" />
                        <p class="text-sm text-muted-foreground">
                            Перетащите файлы сюда или кликните для выбора
                        </p>
                        <input
                            ref="fileInput"
                            type="file"
                            multiple
                            class="hidden"
                            @change="onFileSelect"
                        />
                    </div>

                    <!-- Список выбранных файлов -->
                    <div v-if="files.length" class="space-y-2">
                        <div
                            v-for="(file, index) in files"
                            :key="file.name + index"
                            class="flex items-center justify-between p-2 border rounded"
                        >
                            <span class="text-sm truncate">{{ file.name }}</span>
                            <Button
                                variant="ghost"
                                size="sm"
                                class="h-8 w-8 p-0"
                                @click.stop="removeFile(index)"
                            >
                                <X class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </div>
                <hr />
            </div>
            <div class="flex flex-col gap-2 me-auto rounded border p-4 h-fit xl:w-64 w-full">
                <TaskSideSelector label="Проект*" :items="page.props.auth.projects" v-model="taskModel.project" />
                <TaskSideSelector label="Статус*" :items="page.props.auth.statuses" v-model="taskModel.status" />
                <TaskSideSelector label="Тип*" :items="page.props.auth.task_types" v-model="taskModel.type_type" />
                <TaskSideMultiSelector label="Ответственный*" :items="page.props.auth.users_list" show="full_name" v-model="taskModel.responsibles" />
                <TaskSideMultiSelector label="Исполнитель" :items="page.props.auth.users_list" show="full_name" v-model="taskModel.executors" />
                <TaskSideDateSelector label="Дедлайн" v-model="taskModel.dead_line" />
                <Button @click="submit" class="mt-5">
                    Сохранить и создать
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
