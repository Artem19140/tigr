<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { Exam } from '@/interfaces/Exam';
import { RedirectUrl } from '@/interfaces/Interfaces';
import { useHttp } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
  exam : Exam
}>()

const permissions = computed(() => props.exam.permissions)
const availability = computed(() => props.exam.availability)

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

const downloadResultslDisabled  =  computed(() => !availability.value.documents.results?.available )
const downloadProtocolDisabled = computed(() =>!availability.value.documents.protocol?.available )
const downloadListDisabled =  computed(() =>!availability.value.documents.list?.available)
const downloadCodesDisabled  = computed(() =>!availability.value.documents.codes?.available)
</script>

<template>
  <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
          
    <AppPrimaryButton
      class="w-full !justify-start bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl shadow-none transition"
      text="Коды"
      v-if="permissions.documents.codes"
      :disabled="downloadCodesDisabled"
      variant="outlined"
      @click="() => download('codes')" 
      :loading="documentDownloading === 'codes'"
    />

    <AppPrimaryButton
      class="w-full !justify-start bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl shadow-none transition"
      text="Список"
      variant="outlined"
      v-if="permissions.documents.list"
      :disabled="downloadListDisabled"  
      @click="download('list')" 
      :loading="documentDownloading === 'list'"
    />
    <div class="flex flex-column items-center">
      <AppPrimaryButton
        class="w-full !justify-start bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl shadow-none transition"
        text="Результаты"
        variant="outlined"
        :subtitle="availability.documents.results?.code === 'exam_on_checking' ? availability.documents.results.reason : ''"
        :disabled="downloadResultslDisabled" 
        v-if="permissions.documents.results"
        @click="() => download('results')" 
        :loading="documentDownloading === 'results'"
      />
      <div class="text-xs text-gray-500">{{ availability.documents.results?.code === 'exam_on_checking' ? 'Проверка' : '' }}</div>
    </div>

    <AppPrimaryButton
      class="w-full !justify-start bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl shadow-none transition"
      text="Протокол"
      variant="outlined"
      v-if="permissions.documents.protocol"
      :disabled="downloadProtocolDisabled"
      @click="() => download('protocol')" 
      :loading="documentDownloading === 'protocol'"
    />

  </div>
</template>