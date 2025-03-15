<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { PlusCircle } from 'lucide-vue-next';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { computed, ref } from 'vue';

defineProps<{
    items: NavItem[];
    title?: string;
}>();

const page = usePage<SharedData>();

const form = useForm({
    name: '',
    code_name: '',
});
const { open } = useSidebar();
const projects = computed(() => page.props.auth.assigned_projects);
const creationOpened = ref(false);
function submit() {
    form.post(route('projects.store'), {
        preserveScroll: true,
        onSuccess: () => {
            creationOpened.value = false;
            form.reset();
        },
    });
}

function formatCodeName(event: any) {
    let value = event.target.value;
    value = value.replace(/[^A-Za-z]/g, '');
    if (value.length > 20) value = value.substring(0, 20);
    event.target.value = value.toUpperCase();
    form.code_name = value.toUpperCase();
}
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>{{ title }}</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="project in projects" :key="project.id">
                <SidebarMenuButton as-child :is-active="`/projects/${project.id}` === page.url">
                    <Link :href="route('projects.show', project.id.toString())" class="overflow-hidden">
                        <span
                            :class="!open && '-mx-1'"
                            class="flex h-6 min-w-6 flex-row items-center justify-center rounded bg-primary text-xs font-semibold text-primary-foreground"
                            >{{ project.name[0] }}</span
                        >
                        <span v-if="open">{{ project.name }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>

            <Dialog v-model:open="creationOpened">
                <DialogTrigger as-child>
                    <button
                        @click.prevent="creationOpened = true"
                        class="group flex flex-row items-center gap-2 rounded py-1 opacity-50 transition-all hover:bg-secondary hover:opacity-100"
                        :class="open ? 'px-2' : 'justify-center px-1'"
                    >
                        <span class="flex h-6 w-6 flex-row items-center justify-center rounded p-0.5 text-xs text-primary transition-colors"
                            ><PlusCircle
                        /></span>
                        <span v-if="open" class="text-sm">Создать проект</span>
                    </button>
                </DialogTrigger>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Создание проекта</DialogTitle>
                        <DialogDescription> Заполните поля ниже чтобы создать свой проект </DialogDescription>
                    </DialogHeader>

                    <form class="flex flex-col gap-4">
                        <div class="grid gap-2">
                            <Label for="name">Название проекта</Label>
                            <Input
                                id="name"
                                ref="name"
                                v-model="form.name"
                                class="mt-1 block w-full"
                                autocomplete="project-name"
                                placeholder="Абрис"
                            />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="code_name">Кодовое название проекта</Label>
                            <Input
                                id="code_name"
                                ref="code_name"
                                v-model="form.code_name"
                                class="mt-1 block w-full"
                                autocomplete="project-code-name"
                                placeholder="abris"
                                @input="formatCodeName"
                            />
                            <InputError :message="form.errors.code_name" />
                        </div>
                    </form>

                    <DialogFooter>
                        <DialogClose as-child>
                            <Button type="button" variant="ghost"> Отмена </Button>
                        </DialogClose>
                        <Button type="button" @click.prevent="submit"> Создать </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </SidebarMenu>
    </SidebarGroup>
</template>
