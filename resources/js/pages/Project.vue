<script setup lang="ts">
import AddProjectParticipant from '@/components/AddProjectParticipant.vue';
import { Button } from '@/components/ui/button';
import UserInfo from '@/components/UserInfo.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Project, type SharedData, Task } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Cog, EllipsisVerticalIcon, PlusCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';

import InputError from '@/components/InputError.vue';
import TaskRow from '@/components/TaskRow.vue';
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
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps<{
    user: any;
    project: Project;
    tasks: Task[];
}>();

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
const isOwner = computed(() =>
    props.project.participants.some((participant) => participant.user.id === page.props.auth.user?.id && participant.role.id === 1),
);
const isManager = computed(() =>
    props.project.participants.some((participant) => participant.user.id === page.props.auth.user?.id && participant.role.id === 2),
);

const dialogOpened = ref(false);
const loading = ref(false);

function removeParticiant(user_id: number) {
    router.post(
        route('projects.participant.destroy'),
        {
            _method: 'post',
            project_id: props.project.id,
            user_id: user_id,
        },
        {
            onFinish: () => {
                loading.value = false;
            },
            onStart: () => {
                loading.value = true;
            },
        },
    );
}

function makeParticiantAsManager(user_id: number) {
    router.post(
        route('projects.participant.update'),
        {
            _method: 'post',
            project_id: props.project.id,
            user_id: user_id,
            role_id: 2,
        },
        {
            onFinish: () => {
                loading.value = false;
            },
            onStart: () => {
                loading.value = true;
            },
        },
    );
}

function makeParticiantAsUser(user_id: number) {
    router.post(
        route('projects.participant.update'),
        {
            _method: 'post',
            project_id: props.project.id,
            user_id: user_id,
            role_id: 3,
        },
        {
            onFinish: () => {
                loading.value = false;
            },
            onStart: () => {
                loading.value = true;
            },
        },
    );
}

const form = useForm({
    name: props.project.name,
    code_name: props.project.code_name,
});
const creationOpened = ref(false);
function submit() {
    form.patch(route('projects.update', props.project.id), {
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
    <Head :title="project.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full flex-col gap-5 p-5 xl:w-1/2">
            <div class="flex flex-row gap-2">
                <span
                    class="flex h-7 min-w-7 flex-col items-center justify-center rounded bg-primary font-semibold text-primary-foreground md:h-14 md:min-w-14 md:text-3xl"
                    >{{ project.name[0] }}</span
                >
                <div class="flex flex-col">
                    <p class="line-clamp-1 font-semibold md:text-xl">{{ project.name }}</p>
                    <p class="text-xs opacity-50 md:text-sm">{{ project.code_name }}</p>
                </div>
                <Dialog v-model:open="creationOpened">
                    <DialogTrigger as-child>
                        <Button v-if="isOwner || isManager" class="ms-auto md:w-auto" variant="outline">
                            <span class="hidden md:block">Редактировать проект</span>
                            <Cog class="block md:hidden" />
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Редактирование проекта</DialogTitle>
                            <DialogDescription> Заполните поля ниже чтобы обновить информацию об проекте </DialogDescription>
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
            </div>
            <hr />
            <div class="flex flex-col gap-3 rounded">
                <div class="flex flex-row items-center gap-2">
                    <p class="text-lg font-semibold">Участники проекта</p>
                    <button
                        v-if="isOwner || isManager"
                        @click="dialogOpened = true"
                        class="group flex flex-row items-center gap-2 rounded px-1 py-1 opacity-50 transition-all hover:bg-secondary hover:opacity-100"
                    >
                        <span class="flex h-5 w-5 flex-row items-center justify-center rounded p-0.5 text-xs text-primary transition-colors"
                            ><PlusCircle
                        /></span>
                        <span class="hidden text-xs group-hover:block">Добавить участника</span>
                    </button>
                </div>

                <div class="-m-2 flex flex-col">
                    <div
                        v-for="participant in project.participants"
                        :key="participant.user.id"
                        class="flex flex-row items-center gap-3 rounded-xl bg-opacity-40 p-2"
                    >
                        <UserInfo :user="participant.user" showEmail />
                        <span class="text-sm opacity-50">{{ participant.role.name }}</span>
                        <DropdownMenu>
                            <DropdownMenuTrigger>
                                <Button
                                    v-if="(isOwner || isManager) && participant.user.id != page.props.auth.user.id && participant.role.id != 1"
                                    variant="ghost"
                                    class="group w-3"
                                >
                                    <EllipsisVerticalIcon class="text-2xl opacity-50 group-hover:opacity-100" /> </Button
                            ></DropdownMenuTrigger>
                            <DropdownMenuContent>
                                <DropdownMenuItem @click="makeParticiantAsManager(participant.user.id)" v-if="isOwner && participant.role.id != 2"
                                    >Повысить до менеджера</DropdownMenuItem
                                >
                                <DropdownMenuItem @click="makeParticiantAsUser(participant.user.id)" v-if="isOwner && participant.role.id == 2"
                                    >Снять роль менеджера</DropdownMenuItem
                                >
                                <DropdownMenuItem
                                    @click="removeParticiant(participant.user.id)"
                                    v-if="participant.role.id != 1 && (isOwner || participant.role.id != 2)"
                                    ><span class="text-rose-500">Исключить из проекта</span></DropdownMenuItem
                                >
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>
                <hr />
                <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">
                    <div class="relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
                        <div class="flex flex-col divide-y">
                            <TaskRow v-for="task in tasks" :key="task.id" :task="task" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
    <AddProjectParticipant :project_id="project.id" :ignoreUsers="project.participants" v-model="dialogOpened" />
</template>
