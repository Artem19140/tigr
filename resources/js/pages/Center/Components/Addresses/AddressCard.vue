<script setup lang="ts">
import AppNumberInput from '@/components/UI/AppNumberInput/AppNumberInput.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import AppTextarea from '@/components/UI/AppTextarea/AppTextarea.vue';
import { useConfirmationOptionsDialog } from '@/composables/useConfirmationOptionsDialog';
import { useConfirmDialog } from '@/composables/useConfirmDialog';
import { Address } from '@/interfaces/Address';
import { router, useHttp } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    address:Address
}>()

const editMode = ref<boolean>(false)

const http = useHttp()

const deleteAddress = async () => {
    const {open} = useConfirmationOptionsDialog() 
    const ok = await open('Деактивировать адрес')
    if(!ok) return

    http.delete(`addresses/${props.address.id}`,{
        onSuccess:() => {
            router.reload()
        },
    })
}

const editHttp = useHttp({
    address:props.address.address ?? '',
    capacity:props.address.capacity
})

const edit = () => {
    editHttp.patch(`addresses/${props.address.id}`,{
        onSuccess:() => {
            router.reload({
                onSuccess:() => {
                    editMode.value = false
                },
            })
        }
    })
}

const cancellEdit = async () => {
    if(editHttp.isDirty){
        const {confirmOpen} = useConfirmDialog()
        const ok = await confirmOpen('Отменить редактирование?')
        if(!ok) return
    }
    editHttp.resetAndClearErrors()
    editMode.value = false
}
</script>

<template>
    <v-card
        class="address-card"
        rounded="xl"
        variant="text"
    >
        <div v-if="!editMode" class="pa-4">

            <div class="d-flex justify-space-between align-start ga-4">

                <div class="flex-grow-1 min-w-0">
                    <div class="text-h6 font-weight-medium text-wrap">
                        {{ address.address }}
                    </div>

                    <div class="text-caption text-medium-emphasis mt-1">
                        Вместимость: {{ address.capacity }} человек
                    </div>
                </div>

                <v-btn
                    icon="mdi-pencil"
                    variant="text"
                    @click="editMode = true"
                />
            </div>

            <div class="d-flex ga-2 mt-4">
                <v-btn
                    color="error"
                    variant="text"
                    :loading="http.processing"
                    :disabled="http.processing"
                    @click="deleteAddress"
                >
                    Деактивировать
                </v-btn>
            </div>

        </div>

        <!-- EDIT MODE -->
        <v-expand-transition>
            <div v-if="editMode" class="pa-4">

                <div class="text-subtitle-2 font-weight-medium mb-3">
                    Редактирование адреса
                </div>

                <div
                    v-if="address.examsExists"
                    class="text-caption text-medium-emphasis mb-3"
                >
                    Редактирование недоступно — уже есть привязанные экзамены
                </div>

                <div class="d-flex flex-column ga-3">

                    <AppTextarea
                        v-model="editHttp.address"
                        :error-messages="editHttp.errors.address"
                        :disabled="address.examsExists"
                        label="Адрес"
                        maxlength="256"
                    />

                    <AppNumberInput
                        v-model="editHttp.capacity"
                        :error-messages="editHttp.errors.capacity"
                        label="Вместимость"
                        :min="1"
                    />
                </div>

                <div class="d-flex justify-end ga-2 mt-4">
                    <v-btn
                        variant="text"
                        @click="cancellEdit"
                    >
                        Отмена
                    </v-btn>

                    <AppPrimaryButton
                        text="Сохранить"
                        :disabled="!editHttp.isDirty"
                        :loading="editHttp.processing"
                        @click="edit"
                    />
                </div>

            </div>
        </v-expand-transition>

    </v-card>
</template>

<style lang="css" scoped>
    .address-card {
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .address-card:hover {
        transform: translateY(-1px);
    }
</style>