<script setup lang="ts">
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { Bell } from 'lucide-vue-next';

const { open } = useSidebar();
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <SidebarMenuButton class="relative flex flex-row items-center">
                <Bell />
                <span>Уведомления</span>
                <span
                    :class="!open && 'absolute left-1.5'"
                    class="ms-auto flex h-5 w-5 flex-row items-center justify-center rounded-full bg-rose-600 text-xs text-white"
                    >2</span
                >
            </SidebarMenuButton>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
