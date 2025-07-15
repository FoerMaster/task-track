<script setup lang="ts">
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Label } from '@/components/ui/label';
import { computed, ref } from 'vue';
import { Input } from '@/components/ui/input';

const opened = ref(false);
const searchQuery = ref('');
const model = defineModel<number[]>();
const props = defineProps<{items: {id: number,name: string}[], label: string, show?: string}>()

// Инициализация модели пустым массивом
model.value ||= [];

const selectedItems = computed(() =>
    props.items.filter(item => model.value?.includes(item.id)))

const filteredItems = computed(() =>
    props.items.filter(item =>
        String(props.show ? item[props.show] : item.name)
            .toLowerCase()
            .includes(searchQuery.value.toLowerCase())
    )
);
</script>

<template>
    <div class="flex flex-col items-start gap-1">
        <Label class="text-sm font-normal opacity-70 me-1">{{ label }}</Label>
        <span @click="opened = true" class="text-sm !text-start cursor-pointer">
            {{ selectedItems.length
            ? selectedItems.map(i => show ? i[show] : i.name).join(', ')
            : 'Не выбрано' }}
        </span>

        <Popover v-model:open="opened">
            <PopoverTrigger></PopoverTrigger>
            <PopoverContent class="w-[200px] p-0">
                <div class="p-2 border-b">
                    <Input
                        v-model="searchQuery"
                        placeholder="Поиск..."
                        class=""
                    />
                </div>
                <ScrollArea class="w-full h-[200px]">
                    <div class="flex flex-col text-start">
                        <button
                            v-for="item in filteredItems"
                            :key="item.id"
                            @click.stop="model = model.includes(item.id)
                                ? model.filter(id => id !== item.id)
                                : [...model, item.id]"
                            class="flex justify-between items-center text-start hover:bg-accent px-3 py-2 text-sm"
                            :class="{'bg-accent': model.includes(item.id)}"
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
