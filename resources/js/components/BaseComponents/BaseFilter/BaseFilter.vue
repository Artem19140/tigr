<script setup lang="ts">
import { computed, ref } from 'vue';
import AppPrimaryButton from '@components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { router } from '@inertiajs/vue3';
import { mdiFilterVariant, mdiClose, mdiMagnify } from '@mdi/js'

const props = defineProps<{
    form: any,
    url: string,
    filters: any
}>()

const isOpen = ref<boolean>(false)
const loading = defineModel<boolean>({default:false})
    
const filledCount = computed(() => {
    if(!props.filters) return 0
    return Object.values(props.filters).some(value => value !== null) 
})

const find = () => {
    loading.value = true
    props.form.transform((data:any) => cleanFilters(data))
        .get(props.url, {
            preserveState: true,
            preserveScroll: true,
            replace: false,
            onFinish:() => {
                loading.value = false,
                isOpen.value = false
            }
        })
}

const clean = () => {
    props.form.reset()
    isOpen.value = false
    router.visit(props.url,  {
        onFinish:() => {
            loading.value = false
        }
    })
}

function cleanFilters(data: Record<string, any>) {
    return Object.fromEntries(
        Object.entries(data).map(([key, value]) => {
            if (
                value === '' ||
                value === null ||
                value === false ||
                (typeof value === 'number' && isNaN(value))
            ) {
                return [key, undefined]
            }
            return [key, value]
        })
    )
}
</script>

<template>
    <v-menu
        v-model="isOpen"
        width="400"
        :close-on-content-click="false"
        location="bottom start"
        offset="8"
    >
        <template #activator="{ props }">
            <v-btn
                v-bind="props"
                icon
                variant="text"
                size="small"
                class="rounded-lg"
            >
                <v-badge
                    :content="filledCount"
                    color="error"
                    :model-value="filledCount"
                    dot
                >
                    <v-icon :icon="mdiFilterVariant" />
                </v-badge>
            </v-btn>
        </template>

        <v-card class="overflow-hidden rounded-xl border border-gray-200 shadow-lg">
            <div class="flex items-center justify-between px-5 py-4">
                <div class="text-base font-semibold text-gray-900">
                    Фильтры
                </div>

                <v-btn
                    :icon="mdiClose"
                    variant="text"
                    size="small"
                    density="comfortable"
                    class="rounded-lg text-gray-500"
                    @click="isOpen = false"
                />
            </div>

            <div class="border-t border-gray-100" />

            <div class="max-h-[65vh] overflow-y-auto px-5 py-4">
                <div class="space-y-4">
                    <slot />
                </div>
            </div>

            <div class="border-t border-gray-100 px-5 py-3">
                <div class="flex items-center justify-end gap-2">
                    <v-btn
                        variant="text"
                        :disabled="form.processing"
                        @click="clean"
                    >
                        Очистить
                    </v-btn>

                    <AppPrimaryButton
                        :prepend-icon="mdiMagnify"
                        text="Найти"
                        :disabled="form.processing"
                        :loading="form.processing"
                        @click="find"
                    />
                </div>
            </div>
        </v-card>
    </v-menu>
</template>