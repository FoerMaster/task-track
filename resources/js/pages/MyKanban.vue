<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Task } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { getProjectById, getUserById } from '@/lib/utils';
import { formatDate } from '../lib/datetime';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Задачи', href: '/dashboard' },
    { title: 'Kanban', href: '/tasks/kanban' }
];

const props = defineProps<{
    user: any;
    tasks: Task[];
}>();

const page = usePage<{
    auth: {
        statuses: Array<{ id: number; name: string }>;
    };
}>();

const searchQuery = ref('');
const selectedProject = ref('all');

const filteredTasks = computed(() => {
    return props.tasks.filter(task => {
        const matchesSearch =
            searchQuery.value === '' ||
            task.name.toLowerCase().includes(searchQuery.value.toLowerCase());

        const matchesProject =
            selectedProject.value === 'all' ||
            task.project_id.toString() === selectedProject.value;

        return matchesSearch && matchesProject;
    });
});

const tasksByStatus = computed(() => {
    const grouped: Record<number, Task[]> = {};

    page.props.auth.statuses.forEach(status => {
        grouped[status.id] = [];
    });

    filteredTasks.value.forEach(task => {
        if (grouped[task.status]) {
            grouped[task.status].push(task);
        }
    });

    return grouped;
});

const projects = computed(() => {
    const uniqueProjects = new Map();

    props.tasks.forEach(task => {
        const project = getProjectById(task.project_id);
        if (project) {
            uniqueProjects.set(project.id, project);
        }
    });

    return Array.from(uniqueProjects.values());
});

const draggedTask = ref<Task | null>(null);
const dragOverStatus = ref<number | null>(null);

function onDragStart(task: Task, event: DragEvent) {
    draggedTask.value = task;

    if (event.dataTransfer && event.target instanceof HTMLElement) {
        event.dataTransfer.effectAllowed = 'move';
        const clone = event.target.cloneNode(true) as HTMLElement;
        clone.style.opacity = '0.5';
        clone.style.position = 'absolute';
        clone.style.top = '-1000px';
        document.body.appendChild(clone);

        event.dataTransfer.setDragImage(clone, 20, 20);

        setTimeout(() => {
            document.body.removeChild(clone);
        }, 100);
    }
}

function onDragOver(event: DragEvent, statusId: number) {
    event.preventDefault();
    dragOverStatus.value = statusId;
}

function onDragLeave(event: DragEvent) {
    if (
        event.relatedTarget &&
        event.currentTarget &&
        !(event.currentTarget as Node).contains(event.relatedTarget as Node)
    ) {
        dragOverStatus.value = null;
    }
}

function onDrop(statusId: number) {
    if (draggedTask.value && draggedTask.value.status !== statusId) {
        console.log(`Moving task ${draggedTask.value.id} to status ${statusId}`);
        const updatedTask = { ...draggedTask.value, status: statusId };

        const taskIndex = props.tasks.findIndex(t => t.id === draggedTask.value?.id);
        if (taskIndex !== -1) {
            props.tasks[taskIndex] = updatedTask;

            router.patch(route('tasks.update', draggedTask.value.id), {
                status: statusId,
            });
        }

        draggedTask.value = null;
        dragOverStatus.value = null;
    }
}

function onDragEnd() {
    draggedTask.value = null;
    dragOverStatus.value = null;
}

function renderGhostTask(statusId: number) {
    return (
        draggedTask.value &&
        dragOverStatus.value === statusId &&
        draggedTask.value.status !== statusId
    );
}

function getDeadlineStatus(task: Task) {
    if (!task.deadline) return 'normal';

    const deadline = new Date(task.deadline);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const deadlineDate = new Date(deadline);
    deadlineDate.setHours(0, 0, 0, 0);
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    if (deadlineDate < today) {
        return 'overdue';
    } else if (deadlineDate.getTime() === today.getTime()) {
        return 'today';
    } else if (deadlineDate.getTime() === tomorrow.getTime()) {
        return 'tomorrow';
    }

    return 'normal';
}

function getDeadlineClass(task: Task) {
    const status = getDeadlineStatus(task);
    if (status === 'overdue') return 'bg-red-500/10 dark:bg-red-900/20 border-red-400/50 dark:border-red-700/50';
    if (status === 'today') return 'bg-orange-500/10 dark:bg-orange-900/20 border-orange-400/50 dark:border-orange-700/50';
    if (status === 'tomorrow') return 'bg-yellow-500/10 dark:bg-yellow-900/20 border-yellow-400/50 dark:border-yellow-700/50';
    return '';
}

const collapsedColumns = ref<Record<number, boolean>>({});

onMounted(() => {
    const stored = localStorage.getItem('collapsedColumns');
    if (stored) {
        collapsedColumns.value = JSON.parse(stored);
    } else {
        page.props.auth.statuses.forEach(status => {
            collapsedColumns.value[status.id] = false;
        });
    }
});

function toggleCollapse(statusId: number) {
    collapsedColumns.value[statusId] = !collapsedColumns.value[statusId];
}

watch(
    collapsedColumns,
    newVal => {
        localStorage.setItem('collapsedColumns', JSON.stringify(newVal));
    },
    { deep: true }
);
</script>

<template>
    <Head title="Kanban" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-1 gap-4 overflow-x-auto pb-4">
                <div
                    v-for="status in page.props.auth.statuses"
                    :key="status.id"
                    :class="collapsedColumns[status.id] ? 'w-fit' : 'flex-1'"
                    class="flex flex-col rounded-xl bg-muted/30 p-2 transition-all duration-300"
                    @dragover="onDragOver($event, status.id)"
                    @dragleave="onDragLeave"
                    @drop="onDrop(status.id)"
                >
                    <div class="mb-3 flex items-center justify-between p-2">
                        <div class="flex items-center gap-2">
                            <h3 class="font-medium" v-if="!collapsedColumns[status.id]">
                                {{ status.name }}
                            </h3>
                            <h3 class="font-medium" v-else>
                                {{ status.name.charAt(0) }}
                            </h3>
                            <button
                                @click="toggleCollapse(status.id)"
                                class="text-xs text-muted-foreground focus:outline-none"
                            >
                                <span v-if="collapsedColumns[status.id]">Развернуть</span>
                                <span v-else>Свернуть</span>
                            </button>
                        </div>
                        <span v-if="!collapsedColumns[status.id]" class="rounded bg-muted px-2 py-1 text-xs">
                            {{ tasksByStatus[status.id]?.length || 0 }}
                        </span>
                    </div>
                    <div v-if="!collapsedColumns[status.id]" class="flex flex-col gap-2">
                        <div
                            v-if="renderGhostTask(status.id)"
                            class="flex flex-col gap-2 rounded-lg bg-background/70 p-3 shadow-sm border-2 border-dashed border-primary/50 animate-pulse"
                        >
                            <div class="flex flex-row items-center justify-between">
                                <span class="text-xs text-muted-foreground">
                                    {{ getProjectById(draggedTask?.project_id)?.code_name }}-{{ draggedTask?.id }}
                                </span>
                                <div class="h-2 w-2 rounded-full" :style="`background: ${draggedTask?.task_type}`"></div>
                            </div>
                            <div class="text-sm font-medium text-primary/80">
                                {{ draggedTask?.name }}
                            </div>
                            <div class="flex flex-wrap gap-1 opacity-70">
                                <div
                                    v-if="draggedTask?.executors && draggedTask.executors.length > 0"
                                    class="flex"
                                >
                                    <div
                                        v-for="userId in draggedTask.executors.slice(0, 3)"
                                        :key="userId"
                                        class="h-6 w-6 rounded-full bg-primary/20 text-xs flex items-center justify-center overflow-hidden"
                                    >
                                        <img
                                            v-if="getUserById(userId)?.avatar"
                                            :src="getUserById(userId)?.avatar"
                                            :alt="getUserById(userId)?.full_name"
                                            class="h-full w-full object-cover"
                                        />
                                        <span v-else>
                                            {{ getUserById(userId)?.full_name.charAt(0) }}
                                        </span>
                                    </div>
                                    <div
                                        v-if="draggedTask.executors.length > 3"
                                        class="h-6 w-6 rounded-full bg-muted text-xs flex items-center justify-center"
                                    >
                                        +{{ draggedTask.executors.length - 3 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            v-for="task in tasksByStatus[status.id]"
                            :key="task.id"
                            draggable="true"
                            @dragstart="onDragStart(task, $event)"
                            @dragend="onDragEnd"
                            class="cursor-move transition-all duration-200"
                            :class="{ 'opacity-40': draggedTask && draggedTask.id === task.id }"
                        >
                            <a :href="route('tasks.show', task.id)" target="_blank" class="block">
                                <div
                                    class="flex flex-col gap-2 rounded-lg bg-background p-3 shadow-sm border"
                                    :class="[ task.status == 5 && 'opacity-60', getDeadlineClass(task) ]"
                                >
                                    <div class="flex flex-row items-center justify-between">
                                        <span class="text-xs text-muted-foreground">
                                            {{ getProjectById(task.project_id)?.code_name }}-{{ task.id }}
                                        </span>
                                        <span class="text-xs opacity-50">
                                            {{ formatDate(task.created_at) }}
                                        </span>
                                    </div>
                                    <div class="text-sm font-medium" :class="task.status == 5 && 'line-through'">
                                        {{ task.name }}
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-muted-foreground">
                                        <div class="flex items-center">
                                            <div
                                                v-if="task.executors && task.executors.length > 0"
                                                class="flex"
                                            >
                                                <div
                                                    v-for="(userId, index) in task.executors.slice(0, 3)"
                                                    :key="userId"
                                                    class="h-6 w-6 rounded-full bg-primary/20 text-xs flex items-center justify-center overflow-hidden"
                                                    :title="getUserById(userId)?.full_name"
                                                    :style="{'margin-left': index === 0 ? '0' : '-8px'}"
                                                >
                                                    <img
                                                        v-if="getUserById(userId)?.avatar"
                                                        :src="getUserById(userId)?.avatar"
                                                        :alt="getUserById(userId)?.full_name"
                                                        class="h-full w-full object-cover"
                                                    />
                                                    <span v-else>
                                                        {{ getUserById(userId)?.full_name.charAt(0) }}
                                                    </span>
                                                </div>
                                                <div
                                                    v-if="task.executors.length > 3"
                                                    class="h-6 w-6 rounded-full bg-muted text-xs flex items-center justify-center -ml-2"
                                                >
                                                    +{{ task.executors.length - 3 }}
                                                </div>
                                            </div>
                                            <div v-else class="text-xs text-muted-foreground">
                                                Нет исполнителей
                                            </div>
                                        </div>
                                        <span>{{ getUserById(task.create_from)?.full_name }}</span>
                                    </div>
                                    <div
                                        v-if="task.deadline"
                                        class="text-xs mt-1"
                                        :class="{
                                            'text-red-500 dark:text-red-400': getDeadlineStatus(task) === 'overdue',
                                            'text-orange-500 dark:text-orange-400': getDeadlineStatus(task) === 'today',
                                            'text-yellow-500 dark:text-yellow-400': getDeadlineStatus(task) === 'tomorrow',
                                            'text-muted-foreground': getDeadlineStatus(task) === 'normal'
                                        }"
                                    >
                                        Срок: {{ formatDate(task.deadline) }}
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div
                            v-if="(!tasksByStatus[status.id] || tasksByStatus[status.id].length === 0) && !renderGhostTask(status.id)"
                            class="flex h-20 items-center justify-center rounded-lg border border-dashed text-sm text-muted-foreground"
                        >
                            Нет задач
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
    height: 8px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background-color: rgba(155, 155, 155, 0.5);
    border-radius: 20px;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
