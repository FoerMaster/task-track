<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem, useSidebar
} from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { PlusCircle } from 'lucide-vue-next';

defineProps<{
    items: NavItem[];
    title?: string;
}>();
const { open } = useSidebar();
const page = usePage<SharedData>();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>{{ title }}</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton as-child :is-active="item.href === page.url">
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
            <a
                :href="route('tasks.index')"
                class="group flex flex-row items-center gap-2 rounded py-1 opacity-50 transition-all hover:bg-secondary hover:opacity-100"
                :class="open ? 'px-2' : 'justify-center px-1'"
            >
                        <span class="flex h-6 w-6 flex-row items-center justify-center rounded p-0.5 text-xs text-primary transition-colors"
                        ><PlusCircle
                        /></span>
                <span v-if="open" class="text-sm">Создать задачу</span>
            </a>
        </SidebarMenu>
    </SidebarGroup>
</template>
