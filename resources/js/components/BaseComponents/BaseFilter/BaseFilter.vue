<script setup lang="ts">
import { computed, ref } from 'vue';
import AppPrimaryButton from '@components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    form: any,
    url: string,
    filters: any
}>()

const isOpen = ref<boolean>(false)
const loading = defineModel<boolean>({default:false})
    
const filledCount = computed(() => {
    return Object.keys(props.filters).length 
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
    // props.form.get(props.url, {
    //         preserveState: true,
    //         preserveScroll: true,
    //         replace: false,
    //         onFinish:() => {
    //             loading.value = false,
    //             isOpen.value = false
    //         }
    //     })
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
        width="420"
        :close-on-content-click="false"
        v-model="isOpen"
    >
        <template v-slot:activator="{ props }">
            <v-btn 
                icon 
                variant="text"
                v-bind="props"
            >
                <v-badge :content="filledCount" 
                    color="red" 
                    :model-value="filledCount > 0"
                >  
                    <v-icon>mdi-filter-menu</v-icon>
                </v-badge>
            </v-btn>
        </template>
        <v-card
            title="Фильтры"
        >
            <v-card-text>
                <slot />
            </v-card-text>
            
            <v-card-actions class="flex justify-center">
                <AppPrimaryButton
                    prepend-icon="mdi-magnify"
                    text="Найти"
                    @click="find"
                    :disabled="form.processing"
                />
                <v-btn
                    @click="clean"
                >
                    Очистить
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-menu>
</template>