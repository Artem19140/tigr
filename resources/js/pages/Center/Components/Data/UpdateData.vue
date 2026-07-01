<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { useConfirmDialog } from '@/composables/useConfirmDialog';
import { Center } from '@/interfaces/Center';
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
    <v-card rounded="xl" variant="text">
        <v-card-text class="form-grid">

            <div v-if="center" class="form-layout">

                <div class="form-section">

                    <div class="section-title">Основная информация</div>

                    <v-textarea
                        v-model="http.name"
                        :error-messages="http.errors.name"
                        label="Название"
                    />

                    <v-text-field
                        v-model="http.ogrn"
                        :error-messages="http.errors.ogrn"
                        label="ОГРН"
                    />

                    <v-text-field
                        v-model="http.inn"
                        :error-messages="http.errors.inn"
                        label="ИНН"
                    />

                    <v-textarea
                        v-model="http.nameGenitive"
                        :error-messages="http.errors.nameGenitive"
                        label="Название (родительный падеж)"
                    />
                </div>

                <div class="form-section">

                    <div class="section-title">Адреса</div>

                    <v-textarea
                        v-model="http.address"
                        :error-messages="http.errors.address"
                        label="Адрес центра"
                    />

                    <v-textarea
                        v-model="http.certificatesIssueAddress"
                        :error-messages="http.errors.certificatesIssueAddress"
                        label="Адрес выдачи сертификатов"
                    />

                    <div class="section-title mt-4">Ответственные лица</div>

                    <v-textarea
                        v-model="http.directorFio"
                        :error-messages="http.errors.directorFio"
                        label="Директор"
                    />

                    <v-textarea
                        v-model="http.commissionChairman"
                        :error-messages="http.errors.commissionChairman"
                        label="Председатель комиссии"
                    />

                </div>

            </div>

            <div class="form-actions">
                <AppPrimaryButton
                    text="Обновить"
                    @click="edit"
                    :disabled="http.processing || !http.isDirty"
                    :loading="http.processing"
                />

                <v-btn
                    variant="text"
                    @click="beforeClose"
                >
                    Отмена
                </v-btn>
            </div>

        </v-card-text>

    </v-card>
</template>

<style lang="css" scoped>
.form-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

@media (max-width: 960px) {
    .form-layout {
        grid-template-columns: 1fr;
    }
}

.form-section {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.section-title {
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.3px;
    color: rgba(var(--v-theme-on-surface), 0.6);
    margin-bottom: 4px;
    text-transform: uppercase;
}

/* actions */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;

    margin-top: 20px;
    padding-top: 12px;

    border-top: 1px solid rgba(var(--v-border-color), 0.1);
}
</style>