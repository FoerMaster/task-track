<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { ScrollArea } from '@/components/ui/scroll-area';

import UserInfo from '@/components/UserInfo.vue';
import { Input } from '@/components/ui/input';
import type { SharedData, User } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage<SharedData>();
const props = defineProps<{ ignoreUsers: any; project_id: number }>();
const model = defineModel<boolean>();

const searchQuery = ref('');

const filteredUsers = computed((): User[] => {
    const users = page.props.auth.users_list as User[];

    return users.filter(
        (user) =>
            !props.ignoreUsers.some((participant: any) => participant.user.id === user.id) &&
            (user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                user.full_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                user.email.toLowerCase().includes(searchQuery.value.toLowerCase())),
    );
});

function addParticiant(user_id: number) {
    router.post(
        route('projects.participant.add'),
        {
            _method: 'post',
            project_id: props.project_id,
            user_id: user_id,
        },
        {
            onSuccess: () => {
                model.value = false;
            },
        },
    );
}
</script>
<template>
    <Dialog v-model:open="model">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Добавить участника</DialogTitle>
                <DialogDescription>Нажмите на нужного пользователя в списке ниже</DialogDescription>
            </DialogHeader>

            <Input v-model="searchQuery" placeholder="Поиск пользователя" />
            <ScrollArea class="h-64 w-full rounded-md border">
                <div class="">
                    <button
                        v-for="user in filteredUsers"
                        :key="user.id"
                        @click="addParticiant(user.id)"
                        class="flex w-full flex-row gap-2 p-2 hover:bg-accent"
                    >
                        <UserInfo :user="user" showEmail />
                    </button>
                </div>
            </ScrollArea>

            <DialogFooter>
                <DialogClose as-child>
                    <Button type="button" variant="ghost"> Отмена </Button>
                </DialogClose>
                <!--                <Button type="button" @click.prevent="submit"> Добавить </Button>-->
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
