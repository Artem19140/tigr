<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { AttemptChecking } from '@/interfaces/Attempt';
import AttemptCheckingPanel from '@/components/Attempt/AttemptCheckingPanel.vue';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { useConfirmDialog } from '@/composables/useConfirmDialog';

defineOptions({
  layout: [EmployeeLayout],
})

const props = defineProps<{
    attempt: {
        data: AttemptChecking
    },
    examId:number
}>()

const form = useForm()

const finishChecking = async () => {
    const {confirmOpen} = useConfirmDialog()
    const ok = await confirmOpen('Завершить проверку? После завершения оценивание станет недоступно.')
    if(!ok) return
    form.post(`/attempts/${props.attempt.data.id}/checking/finish`,{
        onSuccess:()=>{
            router.reload()
        }
    })
}

</script>

<template>
    <v-btn 
        class="mt-4 ml-4" 
        variant="text" 
        @click="() => router.visit(`/exams/${examId}/checking`)"
        prepend-icon="mdi-arrow-left"
    >
        Назад
    </v-btn>
    
    <AttemptCheckingPanel 
        v-if="attempt"
        :attempt="attempt.data"
        @finished="finishChecking"
        :checking="true"
    />
   
    <span 
        class="bg-red p-1.5 rounded" 
        v-if="attempt.data.checkedAt"
    >
        Попытка проверена. Изменения недоступны.
    </span>
    
    <div class="flex items-center justify-center">
        <AppPrimaryButton 
            :loading="form.processing"
            :disabled="form.processing || attempt.data.checkedAt"
            @click="finishChecking"
            text="Завершить проверку"
        />
    </div>
        
</template>