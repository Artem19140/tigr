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

const cancelEdit = () => {
    http.cancel()
    http.resetAndClearErrors()
    confirmation.value = false
    mode.value = 'view'
}
</script>

<template>
    <v-card
        class="counter-card"
        variant="text"
        rounded="xl"
    >

        <!-- HEADER -->
        <div class="counter-header">

            <div class="counter-label">
                {{ keyLabel(counter.key) }}
            </div>

            <div class="counter-value">

                <!-- VIEW -->
                <div v-if="mode === 'view'" class="text-h6 font-weight-medium">
                    {{ http.value }}
                </div>

                <!-- EDIT -->
                <div v-else>
                    <AppInput
                        v-model="http.value"
                        :error-messages="http.errors.value"
                        :readonly="http.processing"
                    />

                    <v-checkbox
                        v-model="confirmation"
                        label="Подтвердите действие"
                        class="mt-2"
                    />
                </div>

            </div>

        </div>

        <!-- ACTIONS -->
        <div class="counter-actions">

            <v-btn
                v-if="mode === 'view'"
                variant="text"
                @click="mode = 'edit'"
            >
                Редактировать
            </v-btn>

            <div v-else class="d-flex ga-2">

                <AppPrimaryButton
                    text="Обновить"
                    @click="change"
                    :disabled="http.processing || !confirmation"
                    :loading="http.processing"
                />

                <v-btn
                    variant="text"
                    @click="cancelEdit"
                >
                    Отмена
                </v-btn>

            </div>

        </div>

    </v-card>
</template>

<style lang="css" scoped>
.counter-card {
    padding: 14px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* HEADER */
.counter-header {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.counter-label {
    font-size: 12px;
    font-weight: 500;
    color: rgba(var(--v-theme-on-surface), 0.6);
    letter-spacing: 0.3px;
}

/* VALUE */
.counter-value {
    display: flex;
    flex-direction: column;
}

/* ACTIONS */
.counter-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;

    padding-top: 10px;
    border-top: 1px solid rgba(var(--v-border-color), 0.1);
}
</style>