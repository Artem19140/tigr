<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { Counter } from '@/interfaces/Counter';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    counter: Counter
}>()

const form = useForm<{
    value:number | null
}>({
    value: props.counter.value,
})

const change = () => {
    form.patch(props.counter.updateUrl,{ 
        onSuccess() {
            mode.value = 'view'
        },
    })
}
const keyLabel = ( key: string) => {
    switch(key){
        case 'reg_num': 
            return 'Регистрационный номер'
        case 'group':
            return 'Группа'
        case 'session':
            return 'Сессия'
    }
}

const mode = ref<string>('view')

const cancelEdit = () => {
    form.cancel()
    form.resetAndClearErrors()
    mode.value = 'view'
}
</script>

<template>
    <v-card class="overflow-hidden rounded-xl">
        <!-- Header -->
        <v-card-text class="px-6 pt-6">
            <div class="text-base font-semibold text-gray-900">
                {{ keyLabel(counter.key) }}
            </div>
        </v-card-text>

        <!-- Content -->
        <v-card-text class="px-6">
            <template v-if="mode === 'view'">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <div class="text-sm text-gray-500">
                            Текущий номер
                        </div>

                        <div class="mt-1 text-2xl font-semibold text-gray-900">
                            {{ form.value }}
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">
                            Следующий номер
                        </div>

                        <div class="mt-1 text-2xl font-semibold text-gray-900">
                            {{ counter.nextValue }}
                        </div>
                    </div>
                </div>
            </template>

            <div v-else>
                <v-text-field
                    v-model="form.value"
                    label="Номер"
                    variant="outlined"
                    density="comfortable"
                    :error-messages="form.errors.value"
                    :readonly="form.processing"
                    hide-details="auto"
                />
            </div>
        </v-card-text>

        <!-- Actions -->
        <v-card-text class="px-6 pb-6">
            <div class="flex justify-end gap-2">
                <template v-if="mode === 'view'">
                    <v-btn
                        variant="text"
                        @click="mode = 'edit'"
                    >
                        Редактировать
                    </v-btn>
                </template>

                <template v-else>
                    <v-btn
                        variant="text"
                        :disabled="form.processing"
                        @click="cancelEdit"
                    >
                        Отмена
                    </v-btn>

                    <AppPrimaryButton
                        text="Обновить"
                        :disabled="form.processing"
                        :loading="form.processing"
                        @click="change"
                    />
                </template>
            </div>
        </v-card-text>
    </v-card>
</template>