<script setup lang="ts">
import BaseContainer from '@/components/BaseComponents/BaseContainer/BaseContainer.vue';
import AppDateInput from '@/components/UI/AppDateInput/AppDateInput.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import PlatformAdminLayout from '@/layouts/PlatformAdminLayout.vue';
import { useHttp } from '@inertiajs/vue3';
import { RedirectUrl } from '@/interfaces/Interfaces';

defineOptions({
  layout: [EmployeeLayout, PlatformAdminLayout],
})
const http = useHttp<{date:string | null}, RedirectUrl>({
    date:null
})
const getLog = () => {
   http.get('/admin/logs/available', {
    onSuccess(response, httpResponse) {
        window.open(response.redirectUrl)
    },
   })
}
</script>

<template>
    <BaseContainer
        
    >
        <v-card>

            <v-card-text>

            
                <AppDateInput 
                    v-model="http.date"
                    :error-messages="http.errors.date"

                    label="Дата лога"
                />
                <AppPrimaryButton
                    @click="getLog"
                    :disabled="!http.date || http.processing"
                    :loading="http.processing"
                    text="Выгрузить"
                />
            </v-card-text>
        </v-card>
    </BaseContainer>
   
</template>