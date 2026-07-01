<script setup lang="ts">
import { useHttp } from '@inertiajs/vue3'
import { usePromptDialog } from '@composables/usePromptDialog';
import BaseThreeDotDropdown from '@components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import { useModals } from '@composables/useModals';
import { useLoadingSnackbar } from '@composables/useLoadingSnackBar';
import { Exam } from '@/interfaces/Exam';
import { RedirectUrl } from '@/interfaces/Interfaces';
import { computed } from 'vue';

const props = defineProps<{
  exam : Exam
}>()

const permissions = computed(() => props.exam.permissions)
const availability = computed(() => props.exam.availability)

const emit = defineEmits<{
  (e:'cancel', value:string):void,
  (e:'edit', value:Exam):void
}>()

const http = useHttp({
  cancelledReason: ''
})

const prompt = usePromptDialog()
const loadingSnackbar = useLoadingSnackbar()

const cancelExam = async () => {
  const res = await prompt.open('Укажите причину отмены экзамена')
  if(!res){
    return
  }
  http.cancelledReason = res
  loadingSnackbar.open('Идет отмена')
  http.delete(`/exams/${props.exam?.id}`,{
    onSuccess:()=>{
      emit('cancel', res)
    },
    onFinish() {
      loadingSnackbar.close()
    },
  })
  
}

const download = (document :string) => {
  if(!props.exam?.id || !document){
      return
  }
  const http = useHttp<{},RedirectUrl>()
  loadingSnackbar.open('Скачивание')
  http.get(`/exams/${props.exam.id}/documents/${document}/available`,{
    onSuccess:(response) => {
      if(response.redirectUrl){
        //modals.open('pdf', {url:response.redirectUrl})
        window.open(String(response.redirectUrl))
      }
    },
    onFinish:()=>{
      loadingSnackbar.close()
    }
  })
}
const modals = useModals()

const downloadResultslDisabled  =  computed(() => !availability.value.documents.results?.available )
const downloadProtocolDisabled = computed(() =>!availability.value.documents.protocol?.available )
const downloadListDisabled =  computed(() =>!availability.value.documents.list?.available)
const editDisabled  =   computed(() =>! availability.value.actions.edit)
const cancelDisabled = computed(() =>! availability.value.actions.cancell)
const downloadCodesDisabled  = computed(() =>!availability.value.documents.codes?.available)
</script>

<template>
  <BaseThreeDotDropdown>
    <v-list-item
      title="Кода" 
      :disabled="downloadCodesDisabled"
      @click="() => download('codes')" 
      v-if="permissions.documents.codes"
    />

    <v-list-item 
      title="Список"
      v-if="permissions.documents.list"
      :disabled="downloadListDisabled"  
      @click="download('list')" 
    />

    <v-list-item 
      title="Результаты"
      :subtitle="availability.documents.results?.code === 'exam_on_checking' ? availability.documents.results.reason : ''"
      :disabled="downloadResultslDisabled" 
      v-if="permissions.documents.results"
      @click="() => download('results')" 
    />

    <v-list-item
      title="Протокол" 
      v-if="permissions.documents.protocol"
      :disabled="downloadProtocolDisabled"
      @click="() => download('protocol')" 
    />

    <v-divider></v-divider>

    <v-list-item 
      title="Редактировать" 
      v-if="permissions.actions.edit"
      @click="modals.open('examEdit', {exam:exam, onEdit:(exam:Exam) => emit('edit', exam)})"
      :disabled="editDisabled"
    />
    
    <v-list-item 
      base-color="red"
      title="Отменить" 
      @click="cancelExam"
      :disabled="cancelDisabled" 
      v-if="permissions.actions.cancell"
    />
  </BaseThreeDotDropdown>
</template>