<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { Exam } from '@/interfaces/Exam';
import { RedirectUrl } from '@/interfaces/Interfaces';
import { useHttp } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
  exam : Exam
}>()

const documentDownloading = ref<string | null>(null)
const download = (document :string) => {
  documentDownloading.value = document
  const http = useHttp<{},RedirectUrl>()
  http.get(`/exams/${props.exam.id}/documents/${document}/available`,{
    onSuccess:(response) => {
      if(response.redirectUrl){
        window.open(String(response.redirectUrl))
      }
    },
    onFinish:() => documentDownloading.value = null
  })
}
</script>

<template>
  <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
    <AppPrimaryButton
      class="w-full !justify-start bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl shadow-none transition"
      text="Коды"
      v-if="exam.actions.codes.can"
      :disabled="! exam.actions.codes.availability.available"
      variant="outlined"
      @click="() => download('codes')" 
      :loading="documentDownloading === 'codes'"
    />

    <AppPrimaryButton
      class="w-full !justify-start bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl shadow-none transition"
      text="Список"
      variant="outlined"
      v-if="exam.actions.list.can"
      :disabled="! exam.actions.list.availability.available"  
      @click="download('list')" 
      :loading="documentDownloading === 'list'"
    />
    <div class="flex flex-column items-center">
      <AppPrimaryButton
        class="w-full !justify-start bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl shadow-none transition"
        text="Результаты"
        variant="outlined"
        :disabled="! exam.actions.results.availability.available" 
        v-if="exam.actions.results.can"
        @click="() => download('results')" 
        :loading="documentDownloading === 'results'"
      />
      <div class="text-xs text-gray-500">{{ exam.actions.results.availability.code === 'exam_on_checking' ? 'Проверка' : '' }}</div>
    </div>

    <AppPrimaryButton
      class="w-full !justify-start bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl shadow-none transition"
      text="Протокол"
      variant="outlined"
      v-if="exam.actions.protocol.can"
      :disabled="! exam.actions.protocol.availability.available"
      @click="() => download('protocol')" 
      :loading="documentDownloading === 'protocol'"
    />

  </div>
</template>