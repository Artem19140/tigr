<script setup lang="ts">
import AppInput from '@/components/UI/AppInput/AppInput.vue';
import AppPasswordInput from '@/components/UI/AppPasswordInput/AppPasswordInput.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { Counter } from '@/interfaces/Counter';
import { router, useHttp } from '@inertiajs/vue3';

const props = defineProps<{
    counter: Counter
}>()

const http = useHttp<{
    value:number | null,
    password: number | null
}>({
    value: props.counter.value,
    password: null
})

const change = () => {
    http.patch(`counters/${props.counter.id}`,{
        onSuccess(response, httpResponse) {
            http.password = null
            router.reload()
        },
    })
}
const keyLabel = ( key: string) => {
    switch(key){
        case 'reg_num': 
            return 'Регистрационный номер'
        case 'group':
            return 'Группа'
    }
}
</script>

<template>
<v-card class="mb-4">
    <v-card-text >
        <span>{{ keyLabel(counter.key) }}</span>
        <AppInput
            v-model="http.value"
            :error-messages="http.errors.value"
            :readonly="http.processing"
        />  
        <AppPasswordInput
            v-model="http.password"
            :error-messages="http.errors.password"
            :readonly="http.processing"
        />       
        <div class="mb-2">Для обновления счетчика введите пароль</div>
        <AppPrimaryButton 
            text="Обновить"
            @click="change"
            :disabled="http.processing  || ! http.isDirty || ! http.password"
            :loading="http.processing"
        />
    </v-card-text>
</v-card>
</template>