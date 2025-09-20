<script setup lang="ts">
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Label } from '@/components/ui/label';
import { computed, ref } from 'vue';

const opened = ref(false);
const model = defineModel<number>();
const props = defineProps<{items: {id: number,name: string}[], label: string, show?: string}>()
const selectedItem = computed(()=>props.items.find((element) => element.id == model.value))
</script>

<template>
    <div class="flex flex-col items-start gap-1">
        <Label class="text-sm font-normal opacity-70 me-1">{{label}}</Label>
        <span @click="opened = true" class="cursor-pointer text-sm !text-start">{{selectedItem ? (show ? selectedItem[show] : selectedItem['name']) : 'Не указано'}}</span>
        <Popover v-model:open="opened">
            <PopoverTrigger>

            </PopoverTrigger>
            <PopoverContent class="w-[200px] p-0">
                <ScrollArea class="w-full h-max-[200px] text-sm">
                    <div class="flex flex-col text-start">
                        <button class="text-start hover:bg-accent px-3 py-2" v-for="item in items" @click="model = item.id; opened = false" :key="item.id">{{show ? item[show] : item['name']}}</button>
                    </div>
                </ScrollArea>
            </PopoverContent>
        </Popover>
    </div>
</template>
