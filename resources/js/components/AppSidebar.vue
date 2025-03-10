<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { GitForkIcon, ListCheck, SquareKanban, FolderOpenDot } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import NavProjectList from '@/components/NavProjectList.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Задачи',
        href: '/dashboard',
        icon: ListCheck,
    },
    {
        title: 'Доски',
        href: '/agiles',
        icon: SquareKanban,
    },
    {
        title: 'Проекты',
        href: '/projects',
        icon: FolderOpenDot,
    }
];

const projects: NavItem[] = [
    {
        title: 'Типовой',
        href: '/dashboard',
        icon: ListCheck,
    },
    {
        title: 'RGPlay',
        href: '/agiles',
        icon: SquareKanban,
    }
];

const footerNavItems: NavItem[] = [
    {
        title: 'Git репозиторий',
        href: 'https://git.server-ru.ru/Foer/task-tracker',
        icon: GitForkIcon,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain title="Задачник" :items="mainNavItems" />
            <NavProjectList title="Проекты" :items="projects" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
