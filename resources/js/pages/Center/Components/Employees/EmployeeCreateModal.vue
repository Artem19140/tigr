<script setup lang="ts">
import AppAddButton from '@components/UI/AppAddButton/AppAddButton.vue';
import BaseDialog from '@components/BaseComponents/BaseDialog/BaseDialog.vue';
import { useConfirmDialog } from '@composables/useConfirmDialog';
import {router, useHttp} from '@inertiajs/vue3';
import { useSnackbarQueue } from '@/composables/useSnackbarQueue';
import EmployeeForm from './EmployeeForm.vue';
import { EmployeeFormI } from '@/interfaces/Employee';

const isOpen = defineModel<boolean>({default:false})

const http = useHttp<EmployeeFormI>({
    surname:'',
    name:'',
    patronymic:'',
    roles:[],
    email:''
})

const canClose = async (fn: () => void) => {
    if(http.isDirty){
        const {confirmOpen} = useConfirmDialog()
        const ok = await confirmOpen('Отменить добавление?')
        if(!ok) return
    }
    http.resetAndClearErrors()
    fn()
}

const create = () => {
    http.post(`employees`,{
        onSuccess:() => {
            router.reload()
            isOpen.value=false
            http.resetAndClearErrors()
            const {add} = useSnackbarQueue()
            add('Приглашение было отправлено на почту', 'green')
        }
    })
}
</script>

<template>
    <BaseDialog 
        width="500"
        v-model="isOpen"
        @before-close="(done) => canClose(done)"
    >

        <template #header>
            <div>Добавление</div>
        </template>
            <EmployeeForm 
                v-model:form="http"
                :errors="http.errors"
                :loading="http.processing"
            />

        <template #actions>
            <div>
                <AppAddButton 
                    text="Добавить"
                    @click="create"
                    :loading="http.processing"
                    :disabled="http.processing"
                />
            </div>
        </template>

    </BaseDialog>
</template>