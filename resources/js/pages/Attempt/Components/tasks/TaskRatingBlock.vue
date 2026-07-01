<script setup lang="ts">
import { AttemptAnswer, Task } from '@/interfaces/Task';
import { useHttp } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    task:Task,
    readonly:boolean
}>()

const emit = defineEmits<{
    (e:'rated', value:AttemptAnswer):void
}>()

const answerId = props.task?.attemptAnswer?.id

const http = useHttp<
    {mark: number | null}, 
    {attemptAnswer: AttemptAnswer}
>({
    mark: props.task.attemptAnswer.mark
})

const error = ref<boolean>(false)

const rate = () => {
    error.value = false
    http.put(`/attempts/${props.task.attemptAnswer.attemptId}/answers/${answerId}/rate`,{
        onSuccess:(response)=>{
            emit('rated', response.attemptAnswer)
        },
        onFinish() {
            if(!http.wasSuccessful){
                error.value = true
            }
        },
    })
}

const isRated = computed(
    () => props.task.attemptAnswer.checkedAt !== null
)

const marks = computed(() =>
    Array.from(
        { length: props.task.mark + 1 },
        (_, i) => i
    )
)
</script>

<template>
    <V-autocomplete
        v-model="http.mark"
        :label="`Выберите балл от 0 до ${task.mark}`"
        :items="marks"
        item-title="mark"
        :disabled="http.processing"
        :error-messages="http.errors.mark"
        @update:model-value="rate"
        class="mb-2"
        :readonly="readonly"
    />
    <div
        class="d-flex align-center ga-2 text-caption ml-2"
        style="min-height: 24px"
    >
        <template v-if="http.processing">
            <v-progress-circular
                indeterminate
                size="16"
                width="2"
            />
            <span>Сохраняем...</span>
        </template>

        <template v-else-if="isRated && ! error">
            <v-icon
                size="16"
                color="success"
            >
                mdi-check-circle
            </v-icon>

            <span class="text-success">
                Сохранено
            </span>
        </template>

        <template v-else-if="error">
            <span class="text-error">
                Не удалось сохранить
            </span>

            <v-btn
                size="small"
                variant="outlined"
                color="error"
                @click="rate"
                prepend-icon="mdi-refresh"
            >
                Повторить
            </v-btn>
        </template>
    </div>
</template>