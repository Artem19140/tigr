<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { useConfirmDialog } from '@/composables/useConfirmDialog';
import { Center } from '@/interfaces/Center';
import AppInput from '@components/UI/AppInput/AppInput.vue';
import AppTextarea from '@components/UI/AppTextarea/AppTextarea.vue';
import { router, useHttp } from '@inertiajs/vue3';
const props = defineProps<{
    center : Center
}>()

const emit = defineEmits<{
    (e:'modeShow'):void
}>()

const http = useHttp<Omit<Center, 'id' | 'employees'>>({
  name: props.center.name ?? '',
  ogrn: props.center.ogrn ?? '',
  inn: props.center.inn ?? '',
  address: props.center.address ?? '',
  certificatesIssueAddress: props.center.certificatesIssueAddress ?? '',
  directorFio: props.center.directorFio ?? '',
  commissionChairman: props.center.commissionChairman ?? '',
  nameGenitive: props.center.nameGenitive ?? '',
})

const edit = () => {
    http.put('',{ 
        onSuccess(response, httpResponse) {
            router.reload()
            emit('modeShow')
        },
    })
}

const beforeClose = async () => {
    if(http.isDirty){
        const {confirmOpen} = useConfirmDialog()
        const ok = await confirmOpen('Отменить редактирование?')
        if(!ok) return 
    }
    http.resetAndClearErrors()
    emit('modeShow')
}
</script>

<template>
    <card>
        <v-card-text>
            <div v-if=center class="flex flex-column gap-1">
                <AppTextarea
                    v-model="http.name"
                    :error-messages="http.errors.name"
                    label="Название"
                />
                <AppInput 
                    v-model="http.ogrn"
                    :error-messages="http.errors.ogrn"
                    label="ОГРН"
                />

                <AppInput 
                    v-model="http.inn"
                    :error-messages="http.errors.inn"
                    label="ИНН"
                />

                <AppTextarea
                    v-model="http.address"
                    :error-messages="http.errors.address"
                    label="Адрес центра"
                />

                <AppTextarea
                    v-model="http.certificatesIssueAddress"
                    :error-messages="http.errors.certificatesIssueAddress"
                    label="Адрес выдачи сертификатов"
                />

                <AppTextarea
                    v-model="http.directorFio"
                    :error-messages="http.errors.directorFio"
                    label="Директор"
                />

                <AppTextarea
                    v-model="http.commissionChairman"
                    :error-messages="http.errors.commissionChairman"
                    label="Председатель комиссии"
                />
                <AppTextarea
                        v-model="http.nameGenitive"
                        :error-messages="http.errors.nameGenitive"
                        label="Название в родительском падеже(для документов)"
                    />
                <div>
                    <AppPrimaryButton
                        text="Обновить"
                        @click="edit"
                        :disabled="http.processing || !http.isDirty"
                        :loading="http.processing"
                    />
                    <v-btn @click="beforeClose" class="ml-2">
                        Отмена
                    </v-btn>
                </div>
            </div>
        </v-card-text>
    </card>
</template>