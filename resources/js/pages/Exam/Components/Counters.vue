<script setup lang="ts">
import { router, useHttp } from '@inertiajs/vue3';
import { mdiPencil } from '@mdi/js';
import { computed } from 'vue';

const props = defineProps<{
    session: number | null
    group: number | null
    updateUrl : string | null
}>()

const isOpen = defineModel<boolean>()

const form = useHttp({
    session: props.session,
    group: props.group,
})

const hasEmptyFields = computed(
    () => ! form.session || ! form.group
)

const change = () => {
    if(!props.updateUrl) return 
    form.put(props.updateUrl, {
        onSuccess:() => {
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
        v-if="props.updateUrl"
    >
        <template #activator="{ props }">
            <div
                v-bind="props"
                class="group inline-flex cursor-pointer items-center gap-1 rounded transition-colors hover:bg-gray-100"
            >
                <span class="">
                    {{ session ?? '—'}} / {{ group ?? '—'}}
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
                <v-number-input
                    v-model="form.session"
                    label="Сессия"
                    :error-messages="form.errors.session"
                    :disabled="form.processing"
                    autofocus
                    min="1"
                    @keyup.enter="change"
                />

                <v-number-input
                    v-model="form.group"
                    label="Группа"
                    :error-messages="form.errors.group"
                    :disabled="form.processing"
                    min="1"
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
                        :disabled="form.processing || hasEmptyFields"
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
        {{ session ?? '—'}} / {{ group ?? '—'}}
    </span>
</template>

<style lang="css" scoped>
    .group:hover .group-hover\:opacity-60 {
        opacity: 0.6;
    }
</style>