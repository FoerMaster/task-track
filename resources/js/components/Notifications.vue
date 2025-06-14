<script setup lang="ts">
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { Bell } from 'lucide-vue-next';

const {
    open
} = useSidebar()
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <SidebarMenuButton class="flex flex-row items-center relative">
                <Bell />
                <span>Уведомления</span>
                <span :class="!open && 'absolute left-1.5'" class="ms-auto text-xs text-white w-5 h-5 bg-rose-600 flex flex-row items-center justify-center rounded-full">2</span>
            </SidebarMenuButton>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
