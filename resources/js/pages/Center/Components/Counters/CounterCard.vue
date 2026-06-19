<script setup lang="ts">
import AppInput from '@/components/UI/AppInput/AppInput.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { Counter } from '@/interfaces/Counter';
import { router, useHttp } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    counter: Counter
}>()

const http = useHttp<{
    value:number | null
}>({
    value: props.counter.value,
})

const change = () => {
    http.patch(`counters/${props.counter.id}`,{
        onSuccess(response, httpResponse) {
            router.reload()
            confirmation.value = false
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

const confirmation = ref<boolean>(false)
const mode = ref<string>('view')
</script>

<template>
    <v-card class="mb-4">
        <v-card-text >
            <v-card-subtitle>{{ keyLabel(counter.key) }}</v-card-subtitle>

            <v-card-text v-if="mode === 'view'">
                <v-card-title>
                    <div>{{ http.value }}</div>
                </v-card-title>
            </v-card-text>

            <v-card-text v-if="mode === 'edit'">
                <AppInput
                    v-model="http.value"
                    :error-messages="http.errors.value"
                    :readonly="http.processing"
                    v-if="mode==='edit'"
                    class="mt-4"
                />   
                <v-checkbox
                    label="Подтвердите действие"
                    v-model="confirmation"
                />   
                
            </v-card-text>
            
            <v-card-actions>
                <v-btn
                    @click="mode = 'edit'"
                    variant="text"
                    v-if="mode === 'view'"
                >   
                    Редактировать
                </v-btn>
        
                <div v-else>
                    <div class="flex gap-4">
                        <AppPrimaryButton 
                            text="Обновить"
                            @click="change"
                            :disabled="http.processing  || ! confirmation"
                            :loading="http.processing"
                        />

                        <v-btn
                            @click="() => {
                                http.cancel()
                                http.resetAndClearErrors()
                                confirmation = false
                                mode = 'view'
                            }"
                        >
                            Отмена
                        </v-btn>
                    </div>
                </div>
            </v-card-actions>
        </v-card-text>
    </v-card>
</template>