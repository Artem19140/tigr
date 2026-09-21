<script setup lang="ts">
import { Center } from '@/interfaces/Center';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import CenterManagementLayout from './CenterManagementLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { useConfirm } from '@/composables/useConfirm.js';

const props = defineProps<{
  center : {
    data: Center
  }
  backUrl:string,
  updateUrl:string
}>()

defineOptions({
  layout: [EmployeeLayout, CenterManagementLayout],
})

const form = useForm({
    name: props.center.data.name,
    ogrn: props.center.data.ogrn,
    inn: props.center.data.inn,
    address: props.center.data.address,
    certificatesIssueAddress: props.center.data.certificatesIssueAddress,
    directorFio: props.center.data.directorFio,
    commissionChairman: props.center.data.commissionChairman,
    nameGenitive: props.center.data.nameGenitive
})

const cancel = async () => {
    if(form.isDirty){
            const {confirmOpen} = useConfirm()
            const ok = await confirmOpen('Отменить редактирование данных центра?')
            if(!ok) return
        }   
    form.resetAndClearErrors()
    router.visit(props.backUrl)
}
</script>

<template>
    <Head title="Редактирование центра" />

    <v-container max-width="700">
        <v-card class="overflow-hidden rounded-xl">
            <v-card-text class="px-6 pt-6">
                <div class="text-xl font-semibold text-gray-900">
                    Редактирование данных центра
                </div>

                <div class="mt-1 text-sm text-gray-500">
                    Измените нужные поля и нажмите «Сохранить».
                </div>
            </v-card-text>

            <v-card-text class="px-6">
                <div class="space-y-5">
                    <v-textarea
                        v-model="form.name"
                        label="Название"
                        placeholder="Введите название центра"
                        variant="outlined"
                        density="comfortable"
                        auto-grow
                        rows="1"
                        :error-messages="form.errors.name"
                    />

                    <v-textarea
                        v-model="form.ogrn"
                        label="ОГРН"
                        placeholder="Введите ОГРН"
                        variant="outlined"
                        density="comfortable"
                        auto-grow
                        rows="1"
                        :error-messages="form.errors.ogrn"
                    />

                    <v-textarea
                        v-model="form.inn"
                        label="ИНН"
                        placeholder="Введите ИНН"
                        variant="outlined"
                        density="comfortable"
                        auto-grow
                        rows="1"
                        :error-messages="form.errors.inn"
                    />

                    <v-textarea
                        v-model="form.address"
                        label="Адрес центра"
                        placeholder="Введите адрес центра"
                        variant="outlined"
                        density="comfortable"
                        auto-grow
                        rows="1"
                        :error-messages="form.errors.address"
                    />

                    <v-textarea
                        v-model="form.certificatesIssueAddress"
                        label="Адрес выдачи сертификатов"
                        placeholder="Введите адрес"
                        variant="outlined"
                        density="comfortable"
                        auto-grow
                        rows="1"
                        :error-messages="form.errors.certificatesIssueAddress"
                    />

                    <v-textarea
                        v-model="form.directorFio"
                        label="Директор"
                        placeholder="Введите ФИО директора"
                        variant="outlined"
                        density="comfortable"
                        auto-grow
                        rows="1"
                        :error-messages="form.errors.directorFio"
                    />

                    <v-textarea
                        v-model="form.commissionChairman"
                        label="Председатель комиссии"
                        placeholder="Введите ФИО председателя комиссии"
                        variant="outlined"
                        density="comfortable"
                        auto-grow
                        rows="1"
                        :error-messages="form.errors.commissionChairman"
                    />

                    <v-textarea
                        v-model="form.nameGenitive"
                        label="Название в родительном падеже"
                        placeholder="Название для документов"
                        variant="outlined"
                        density="comfortable"
                        auto-grow
                        rows="1"
                        :error-messages="form.errors.nameGenitive"
                    />
                </div>
            </v-card-text>

            <v-card-text class="px-6 pb-6">
                <div class="flex justify-end gap-2">
                    <v-btn
                        variant="text"
                        :disabled="form.processing"
                        @click="cancel"
                    >
                        Отмена
                    </v-btn>

                    <app-primary-button
                        text="Сохранить"
                        :loading="form.processing"
                        :disabled="form.processing || !form.isDirty"
                        @click="form.put(updateUrl)"
                    />
                </div>
            </v-card-text>
        </v-card>
    </v-container>
</template>