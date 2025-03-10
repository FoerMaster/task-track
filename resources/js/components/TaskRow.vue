<script setup lang="ts">
import { Task } from '@/types';
import Tag from '@/components/Tag.vue';
import { computed } from 'vue';
import { formatDate } from '@/lib/datetime';
import { Paperclip } from 'lucide-vue-next'
const props = defineProps<{task: Task}>();

const assinged = computed(()=> props.task.assigned.map((user) => user.fullName).join(", "))
const createdAt = computed(()=> formatDate(props.task.created_at))
</script>

<template>
    <div class="flex flex-col gap-2 hover:bg-blue-500/10 dark:hover:bg-white/10 py-3 px-4 rounded">
        <div class="flex flex-row gap-3 font-semibold text-sm items-center">
            <span class="text-xs dark:text-neutral-500">{{task.project.shortName}}-{{task.id}}</span>
            <span class="text-primary">{{task.summary}}</span>
            <div class="flex flex-row gap-1">
                <Tag v-for="tag in task.tags" :key="tag.id" :tag="tag" />
            </div>
            <div class="flex flex-row gap-1.5 ms-auto items-center text-muted-foreground">
                <span>{{task.attachments.length}}</span>
                <Paperclip class="w-3.5 h-3.5"/>
            </div>

        </div>
        <div class="flex flex-row gap-2 text-xs font-semibold text-muted-foreground">

            <div class="masked">
                <span>{{task.project.name}}</span>
            </div>

            <div class="masked">
            <span>{{task.state.name}}</span>
            </div>
            <div class="masked flex flex-row items-center gap-2">
                <div class="w-2 h-2 rounded-full" :style="`background: ${task.type.color}`"></div>
                <span :style="`color: ${task.type.color}`">{{task.type.name}}</span>
            </div>
                <div class="masked !w-32 !border-e-0">
            <span v-if="task.assigned.length > 0" class="text-nowrap">{{assinged}}</span>
            <span v-else class="text-nowrap">Нет отвественных</span>
                </div>
            <span class="ms-auto text-nowrap text-blue-500">{{task.initiator.fullName}}</span>
            <span>{{createdAt}}</span>
        </div>
    </div>
</template>

<style scoped>
.masked {
    mask-image: linear-gradient(90deg, black 70%, transparent 99%, black 1%);
    @apply w-24 pe-2 border-e border-e-gray-300 dark:border-e-gray-700;
}
.masked > span {
    @apply text-nowrap
}
</style>
