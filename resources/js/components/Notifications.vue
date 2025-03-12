<script setup lang="ts">
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { type SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { Bell } from 'lucide-vue-next';

const page = usePage<SharedData>();

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
