<script setup lang="ts">
import { Task } from '@/interfaces/Task';
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { mdiCheckCircle, mdiRefresh } from '@mdi/js'

const props = defineProps<{
    task:Task,
    readonly:boolean
}>()

const form = useForm({
    mark: props.task.attemptAnswer.mark
})

const error = ref<boolean>(false)

const rate = () => {
    error.value = false
    form.put(props.task.attemptAnswer.rateUrl,{
        preserveScroll:true,
        preserveState: true
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
    <div>
        <v-autocomplete
            v-model="form.mark"
            :label="`Выберите балл от 0 до ${task.mark}`"
            :items="marks"
            item-title="mark"
            :disabled="form.processing"
            :readonly="readonly"
            :error-messages="form.errors.mark"
            variant="outlined"
            density="comfortable"
            hide-details="auto"
            @update:model-value="rate"
        />

        <div class="mt-2 flex min-h-6 items-center gap-2 px-1">
            <template v-if="form.processing">
                <v-progress-circular
                    indeterminate
                    size="16"
                    width="2"
                    color="primary"
                />

                <span class="text-xs text-gray-500">
                    Сохраняем...
                </span>
            </template>

            <template v-else-if="isRated && !error">
                <v-icon
                    :icon="mdiCheckCircle"
                    size="16"
                    class="text-green-600"
                />

                <span class="text-xs font-medium text-green-600">
                    Сохранено
                </span>
            </template>

            <template v-else-if="error">
                <span class="text-xs font-medium text-red-600">
                    Не удалось сохранить
                </span>

                <v-btn
                    size="x-small"
                    variant="tonal"
                    color="error"
                    :prepend-icon="mdiRefresh"
                    :disabled="form.processing"
                    @click="rate"
                >
                    Повторить
                </v-btn>
            </template>
        </div>
    </div>
</template>