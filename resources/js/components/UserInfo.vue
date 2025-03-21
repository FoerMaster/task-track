<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';
import { computed } from 'vue';

interface Props {
    user: Pick<User, 'avatar'|'name'|'full_name'>;
    showEmail?: boolean;
    small?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
});

const { getInitials } = useInitials();

const showAvatar = computed(() => props.user.avatar && props.user.avatar !== '');
</script>

<template>
    <Avatar class="h-8 w-8 overflow-hidden rounded-lg" :class="small && '!w-7 !h-7 ' ">
        <AvatarImage v-if="showAvatar && user.avatar" :src="user.avatar" :alt="user.name" />
        <AvatarFallback class="rounded-lg text-black dark:text-white">
            {{ getInitials(user.name) }}
        </AvatarFallback>
    </Avatar>

    <div class="grid flex-1 text-left text-sm leading-tight">
        <span class="truncate font-medium">{{ user.full_name }}</span>
        <span v-if="showEmail" class="truncate text-xs text-muted-foreground">@{{ user.name }}</span>
    </div>
</template>
