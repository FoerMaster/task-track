<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { ScrollArea } from '@/components/ui/scroll-area';
import { computed, ref } from 'vue';

const opened = ref(false);
const searchQuery = ref('');
const model = defineModel<number[]>();
const props = defineProps<{ items: { id: number; name: string }[]; label: string; show?: string; testid?: string }>();

model.value ||= [];

const selectedItems = computed(() => props.items.filter((item) => model.value?.includes(item.id)));

const filteredItems = computed(() =>
    props.items.filter((item) =>
        String(props.show ? item[props.show] : item.name)
            .toLowerCase()
            .includes(searchQuery.value.toLowerCase()),
    ),
);
</script>

<template>
    <div class="flex flex-col items-start gap-1">
        <Label class="me-1 text-sm font-normal opacity-70">{{ label }}</Label>
        <span @click="opened = true" class="cursor-pointer !text-start text-sm" :data-testid="testid">
            {{ selectedItems.length ? selectedItems.map((i) => (show ? i[show] : i.name)).join(', ') : 'Не выбрано' }}
        </span>

        <Popover v-model:open="opened">
            <PopoverTrigger></PopoverTrigger>
            <PopoverContent class="w-[200px] p-0">
                <div class="border-b p-2">
                    <Input v-model="searchQuery" placeholder="Поиск..." class="" :data-testid="testid ? `${testid}-search` : null" />
                </div>
                <ScrollArea class="h-[200px] w-full">
                    <div class="flex flex-col text-start">
                        <button
                            v-for="item in filteredItems"
                            :key="item.id"
                            @click.stop="model = model.includes(item.id) ? model.filter((id) => id !== item.id) : [...model, item.id]"
                            class="flex items-center justify-between px-3 py-2 text-start text-sm hover:bg-accent"
                            :class="{ 'bg-accent': model.includes(item.id) }"
                            :data-testid="testid ? `${testid}-option-${item.id}` : null"
                        >
                            <span>{{ show ? item[show] : item.name }}</span>
                            <span v-if="model.includes(item.id)">✓</span>
                        </button>
                    </div>
                </ScrollArea>
            </PopoverContent>
        </Popover>
    </div>
</template>
