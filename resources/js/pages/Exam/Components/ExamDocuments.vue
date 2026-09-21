<script setup lang="ts">
import { ExamDocument } from '@/interfaces/Exam';
import { RedirectUrl } from '@/interfaces/Interfaces';
import { useHttp } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
  documents: Record<DocumentKey, ExamDocument>
}>()

const documentDownloading = ref<string | null>(null)
const http = useHttp<{},RedirectUrl>()

const download = (url :string) => {
    documentDownloading.value = url
    
    http.get(url,{
        onSuccess:(response) => {
            if(response.redirectUrl){
                window.open(String(response.redirectUrl))
            }
        },
        onFinish:() => documentDownloading.value = null
    })
}

const label = {
    codes : 'Кода',
    protocol: 'Протокол',
    results: 'Результаты',
    list: 'Список'
}

type DocumentKey = keyof typeof label
</script>

<template>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <v-btn 
            v-for="(doc, key) in documents"
            :disabled="doc.availability.disabled || http.processing"
            :key="key"
            color="primary"
            rounded="lg"
            :loading="documentDownloading === doc.url"
            class="w-full rounded-xl border border-gray-200 bg-gray-50 !justify-start shadow-none hover:bg-gray-100"
            variant="outlined"
            @click="() => download(doc.url)"
        > {{label[ key ] }}</v-btn>
    </div>
</template>