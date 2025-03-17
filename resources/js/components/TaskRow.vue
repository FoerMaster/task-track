<script setup lang="ts">
import { Project, SharedData, Task, User } from '@/types';
import { computed } from 'vue';
import { formatDate } from '@/lib/datetime';
import { getProjectById, getUserById } from '@/lib/utils';
import { usePage } from '@inertiajs/vue3';

const props = defineProps<{ task: Task }>();
const page = usePage<SharedData>();
const project = getProjectById(props.task.project_id) as Project;
const creatorUser = getUserById(props.task.create_from) as User;
const currentStatus = page.props.auth.statuses.find((status) => status.id == props.task.status);
const currentType = page.props.auth.task_types.find((type) => type.id == props.task.task_type);
const createdAt = computed(() => formatDate(props.task.created_at));

const executors = computed(() => {
    return props.task.executors.map((userId) => (getUserById(userId) as User).full_name);
});
</script>

<template>
    <a :href="route('tasks.show', task.id)" class="flex flex-col gap-2 rounded px-4 py-3 hover:bg-blue-500/10 dark:hover:bg-white/10 ">
        <div class="flex flex-row items-center gap-3 text-sm font-semibold" :class="task.status == 5 && 'line-through'">
            <span class="text-xs dark:text-neutral-500">{{ project.code_name }}-{{ task.id }}</span>
            <span class="text-primary">{{ task.name }}</span>
        </div>
        <div class="flex flex-row gap-2 text-xs font-semibold text-muted-foreground opacity-50">
            <div class="masked">
                <span>{{ project.name }}</span>
            </div>

            <div class="masked">
                <span>{{ currentStatus.name }}</span>
            </div>
            <div class="masked flex flex-row items-center gap-2">
                <div class="h-2 w-2 rounded-full" :style="`background: ${task.task_type}`"></div>
                <span :style="`color: ${task.task_type}`">{{ currentType.name }}</span>
            </div>
            <div class="masked !w-32 !border-e-0">
                <span v-if="executors.length <= 0" class="text-nowrap">Нет исполнителей</span>
                <span v-else class="text-nowrap">{{executors.join(', ')}}</span>
            </div>
            <div class="masked !w-32 !border-e-0">
                <span v-if="executors.length <= 0" class="text-nowrap">Нет отвественных</span>
                <span v-else class="text-nowrap">{{executors.join(', ')}}</span>
            </div>
            <span class="ms-auto text-nowrap text-blue-500">{{ creatorUser.full_name }}</span>
            <span>{{ createdAt }}</span>
        </div>
    </a>
</template>

<style scoped>
.masked {
    mask-image: linear-gradient(90deg, black 70%, transparent 99%, black 1%);
    @apply w-24 border-e border-e-gray-300 pe-2 dark:border-e-gray-700;
}
.masked > span {
    @apply text-nowrap;
}
</style>
