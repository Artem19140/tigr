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
                class="filter-btn"
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

        <v-card
            class="filter-panel"
            rounded="xl"
            elevation="8"
        >
            <div class="filter-header">
                <div class="d-flex align-center justify-space-between">
                    <div class="text-subtitle-2 font-weight-medium">
                        Фильтры
                    </div>

                    <v-btn
                        :icon="mdiClose"
                        variant="text"
                        size="small"
                        @click="isOpen = false"
                    />
                </div>
            </div>

            <v-divider />

            <div class="filter-content">
                <slot />
            </div>

            <v-divider />

            <div class="filter-actions">
                <v-btn
                    variant="text"
                    @click="clean"
                >
                    Очистить
                </v-btn>

                <AppPrimaryButton
                    :prepend-icon="mdiMagnify"
                    text="Найти"
                    @click="find"
                    :disabled="form.processing"
                />
            </div>
        </v-card>
    </v-menu>
</template>

<style lang="css" scoped>
.filter-panel {
    overflow: hidden;
    backdrop-filter: blur(10px);
}

.filter-header {
    padding: 12px 14px;
}

.filter-content {
    padding: 12px 14px;
    max-height: 65vh;
    overflow-y: auto;
}

.filter-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    padding: 10px 12px;
}

/* button polish */
.filter-btn {
    opacity: 0.9;
}

.filter-btn:hover {
    opacity: 1;
}
</style>