<script setup lang="ts">
import BaseThreeDotDropdown from '@/components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import { useConfirmationOptionsDialog } from '@/composables/useConfirmationOptionsDialog';
import { useLoadingSnackbar } from '@/composables/useLoadingSnackBar';
import { Center } from '@/interfaces/Center';
import { useHttp } from '@inertiajs/vue3';
const props=defineProps<{
    center: Center
}>()

const http = useHttp()

const deactivate = async () => {
    const {open} = useConfirmationOptionsDialog()
    
    const ok = await open('Деактивировать центр?')
    if(!ok) return
    const loadingSnack = useLoadingSnackbar()
    loadingSnack.open('Идет деактивация')
    http.delete(`/admin/centers/${props.center.id}`, {
        onSuccess:() => {
            alert('Успех!')
        },
        onFinish:() => {
            loadingSnack.close()
        }
    })
}
</script>

<template>
    <BaseThreeDotDropdown>
        <v-list-item
            title=""
            prepend-icon="mdi-delete"
            title-color="red"
            @click="deactivate"
        >
            <template #title>
                <span class="text-red">Деактивировать</span>
            </template>
        </v-list-item>
            

    </BaseThreeDotDropdown>
</template>