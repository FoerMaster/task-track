<script setup lang="ts">
import { Tag } from '@/types';

defineProps<{tag: Tag}>()
</script>

<template>
<div class="flex flex-row gap-2 px-2 bg-orange-500 text-xs rounded text-white">
    <span>{{tag.name}}</span>
</div>
</template>

<style scoped>

</style>
