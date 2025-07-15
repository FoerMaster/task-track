<script setup lang="ts">
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { computed, ref } from 'vue';

const opened = ref(false);
const model = defineModel<string>();
const props = defineProps<{ label: string }>();

const formattedDate = computed(() => {
    if (!new Date(model.value)) return 'Не указано';
    const day = String(new Date(model.value).getDate()).padStart(2, '0');
    const month = String(new Date(model.value).getMonth() + 1).padStart(2, '0');
    const year = new Date(model.value).getFullYear();
    return `${day}.${month}.${year}`;
});
</script>

<template>
    <div class="flex flex-col items-start gap-1">
        <Label class="text-sm font-normal opacity-70 me-1">{{ label }}</Label>
        <Popover v-model:open="opened">
            <PopoverTrigger as-child>
                <span class="text-sm !text-start">{{ ( model ) ? formattedDate : "Не выбрано" }}</span>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0">
                <Calendar
                    v-model="model"
                    :initial-focus="true"
                    @update:date="opened = false"
                />
            </PopoverContent>
        </Popover>
    </div>
</template>
