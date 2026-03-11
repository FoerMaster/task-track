<script setup lang="ts">
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { ScrollArea } from '@/components/ui/scroll-area';
import { computed, ref } from 'vue';

const opened = ref(false);
const model = defineModel<number>();
const props = defineProps<{ items: { id: number; name: string }[]; label: string; show?: string; testid?: string }>();
const selectedItem = computed(() => props.items.find((element) => element.id == model.value));
</script>

<template>
    <div class="flex flex-col items-start gap-1">
        <Label class="me-1 text-sm font-normal opacity-70">{{ label }}</Label>
        <span @click="opened = true" class="cursor-pointer !text-start text-sm" :data-testid="testid">{{
            selectedItem ? (show ? selectedItem[show] : selectedItem['name']) : 'Не указано'
        }}</span>
        <Popover v-model:open="opened">
            <PopoverTrigger> </PopoverTrigger>
            <PopoverContent class="w-[200px] p-0">
                <ScrollArea class="h-max-[200px] w-full text-sm">
                    <div class="flex flex-col text-start">
                        <button
                            class="px-3 py-2 text-start hover:bg-accent"
                            v-for="item in items"
                            @click="
                                model = item.id;
                                opened = false;
                            "
                            :key="item.id"
                            :data-testid="testid ? `${testid}-option-${item.id}` : null"
                        >
                            {{ show ? item[show] : item['name'] }}
                        </button>
                    </div>
                </ScrollArea>
            </PopoverContent>
        </Popover>
    </div>
</template>
