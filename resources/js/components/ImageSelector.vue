<template>
    <div class="grid gap-2">
        <div class="flex items-center gap-4">
            <div class="relative">
                <img
                    v-if="previewUrl || currentImage"
                    :src="previewUrl || currentImage"
                    class="h-16 w-16 rounded-full object-cover border-2 border-neutral-200 dark:border-neutral-700"
                />
                <div
                    v-else
                    class="h-16 w-16 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center"
                >
                    <span class="text-neutral-400 text-xl">?</span>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <input
                    ref="fileInput"
                    type="file"
                    accept="image/jpeg, image/png, image/webp"
                    class="hidden"
                    @change="handleFileChange"
                />
                <Button
                    type="button"
                    variant="outline"
                    @click="fileInput?.click()"
                >
                    Выбрать файл
                </Button>
                <span class="text-sm text-neutral-500 dark:text-neutral-400">
          Поддерживаемые форматы: JPG, PNG, WEBP
        </span>
            </div>
        </div>
        <InputError v-if="error" :message="error" />
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { Button } from '@/components/ui/button'
import InputError from '@/components/InputError.vue'

const props = defineProps<{
    currentImage?: string
    error?: string
    modelValue?: File | null
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: File | null): void
}>()

const fileInput = ref<HTMLInputElement | null>(null)
const previewUrl = ref<string | null>(null)

const handleFileChange = (e: Event) => {
    const input = e.target as HTMLInputElement
    const file = input.files?.[0]

    if (file) {
        if (!file.type.startsWith('image/')) {
            emit('update:modelValue', null)
            previewUrl.value = null
            return
        }

        const reader = new FileReader()
        reader.onload = (e) => {
            previewUrl.value = e.target?.result as string
        }
        reader.readAsDataURL(file)
        emit('update:modelValue', file)
    } else {
        previewUrl.value = null
        emit('update:modelValue', null)
    }
}

watch(() => props.modelValue, (newVal) => {
    if (!newVal) {
        previewUrl.value = null
        if (fileInput.value) {
            fileInput.value.value = ''
        }
    }
})
</script>
