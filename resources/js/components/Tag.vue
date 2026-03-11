<script setup lang="ts">
import { Tag } from '@/types';

defineProps<{ tag: Tag }>();
</script>

<template>
    <div class="flex flex-row gap-2 rounded bg-orange-500 px-2 text-xs text-white">
        <span>{{ tag.name }}</span>
    </div>
</template>

<style scoped></style>
