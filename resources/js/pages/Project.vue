<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Project, type SharedData, type User } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Search } from 'lucide-vue-next'
import { Button } from '@/components/ui/button';
import TaskRow from '@/components/TaskRow.vue';
import { computed } from 'vue';
import { tasks } from '@/lib/moked';
import UserInfo from '@/components/UserInfo.vue';

const props = defineProps<{
    user: any
    project: Project
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Проекты',
        href: `/`,
    },
    {
        title: props.project.name,
        href: `/project/${props.project.id}`,
    },
];


const page = usePage<SharedData>();
const usertest = page.props.auth.user as User;
</script>

<template>
    <Head :title="project.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col p-5">
            <p><span class="opacity-50 text-sm">{{project.code_name}}</span> -  {{project.name}}</p>
            <div class="flex flex-col gap-3 rounded">
                <p class="font-semibold text-lg">Участники проекта</p>
                <div class="flex flex-col gap-3">
                    <div class="flex flex-row gap-3">
                        <UserInfo :user="usertest" showEmail />
                    </div>
                    <div class="flex flex-row gap-3">
                        <UserInfo :user="usertest" showEmail />
                    </div>
                    <div class="flex flex-row gap-3">
                        <UserInfo :user="usertest" showEmail />
                    </div>
                    <div class="flex flex-row gap-3">
                        <UserInfo :user="usertest" showEmail />
                    </div>
                    <div class="flex flex-row gap-3">
                        <UserInfo :user="usertest" showEmail />
                    </div>
                    <div class="flex flex-row gap-3">
                        <UserInfo :user="usertest" showEmail />
                    </div>
                    <div class="flex flex-row gap-3">
                        <UserInfo :user="usertest" showEmail />
                    </div>
                    <div class="flex flex-row gap-3">
                        <UserInfo :user="usertest" showEmail />
                    </div>
                    <div class="flex flex-row gap-3">
                        <UserInfo :user="usertest" showEmail />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
