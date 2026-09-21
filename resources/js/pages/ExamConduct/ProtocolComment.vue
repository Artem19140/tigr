<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { useConfirm } from '@/composables/useConfirm';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { router, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    exam: {
        id:number,
        comment: string,
        shortName: string,
        date: string
    },
    updateUrl: string,
    backUrl: string
}>()

defineOptions({
  layout: [EmployeeLayout]
})

const form = useForm({
    protocolComment:props.exam.comment
})

const cancel = async () => {
     if(form.isDirty){
            const {confirmOpen} = useConfirm()
            const ok = await confirmOpen('Отменить редактирование?')
            if(!ok) return
        }   
    form.resetAndClearErrors()
    router.visit(props.backUrl)
}
</script>

<template>
    <v-container max-width="700">
        <v-card class="overflow-hidden rounded-xl">
            <v-card-text class="px-6 pt-6">
                <div class="text-xl font-semibold text-gray-900">
                    {{ exam.shortName }}
                </div>

                <div class="mt-1 text-sm text-gray-500">
                    {{ exam.date }} · Комментарий протокола
                </div>
            </v-card-text>

            <!-- Form -->
            <v-card-text class="px-6">
                <v-textarea
                    v-model="form.protocolComment"
                    maxlength="1000"
                    counter
                    :error-messages="form.errors.protocolComment"
                    label="Комментарий или нарушение"
                    placeholder="Введите комментарий..."
                    auto-grow
                    rows="4"
                    variant="outlined"
                    hide-details="auto"
                />

                <div
                    class="mt-4 rounded-lg bg-gray-50 px-4 py-3 text-center text-sm text-gray-500"
                >
                    Комментарий можно редактировать в течение всего дня после
                    экзамена.
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

                    <AppPrimaryButton
                        text="Сохранить"
                        :disabled="form.processing || !form.isDirty"
                        :loading="form.processing"
                        @click="form.put(updateUrl)"
                    />
                </div>
            </v-card-text>
        </v-card>
    </v-container>
</template>