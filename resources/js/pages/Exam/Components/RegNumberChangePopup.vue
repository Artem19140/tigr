<script setup lang="ts">
import { Enrollment } from '@/interfaces/Enrollment';
import { router, useHttp } from '@inertiajs/vue3';
import { mdiPencil } from '@mdi/js';

const props = defineProps<{
    enrollment: Enrollment,
}>()

const isOpen = defineModel<boolean>({default:false})

const form = useHttp<{newRegNumber: string | null}>({
    newRegNumber: null
})

const change = () => {
    form.put(props.enrollment.actions.changeRegNumber.url ?? '', {
        onSuccess: () => {
            isOpen.value = false
            router.reload()
        }
    })
}

</script>

<template>
    <v-menu
        v-model="isOpen"
        :close-on-content-click="false"
        location="bottom start"
        :offset="6"
        width="320"
        v-if="props.enrollment.actions.changeRegNumber.url"
    >
        <template #activator="{ props }">
            <div
                v-bind="props"
                class="group inline-flex cursor-pointer items-center gap-1 rounded px-1.5 py-0.5 transition-colors hover:bg-gray-100"
            >
                <span class="">
                    {{ enrollment.regNumber }}
                </span>

                <v-icon
                    :icon="mdiPencil"
                    size="14"
                    class="opacity-0 transition-opacity duration-150 group-hover:opacity-60"
                />
            </div>
        </template>

        <v-card>
            <v-card-text>
                <v-text-field
                    v-model="form.newRegNumber"
                    maxlength="6"
                    label="Новый рег. номер"
                    :error-messages="form.errors.newRegNumber"
                    :disabled="form.processing"
                    hide-details="auto"
                    autofocus
                    @keyup.enter="change"
                />

                <div class="mt-3 flex justify-end gap-2">
                    <v-btn
                        variant="text"
                        :disabled="form.processing"
                        @click="isOpen = false"
                    >
                        Отмена
                    </v-btn>

                    <v-btn
                        color="primary"
                        :disabled="form.processing || (form.newRegNumber?.length ?? 0) < 6"
                        :loading="form.processing"
                        @click="change"
                    >
                        Сменить
                    </v-btn>
                </div>
            </v-card-text>
        </v-card>
    </v-menu>

    <span
        v-else
    >
        {{ enrollment.regNumber }}
    </span>
</template>

<style lang="css" scoped>
    .group:hover .group-hover\:opacity-60 {
        opacity: 0.6;
    }
</style>