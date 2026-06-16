<script setup lang="ts">
import BaseContainer from '@/components/BaseComponents/BaseContainer/BaseContainer.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import PlatformAdminLayout from '@/layouts/PlatformAdminLayout.vue';
import { useHttp } from '@inertiajs/vue3';
import { RedirectUrl } from '@/interfaces/Interfaces';
import AppInput from '@/components/UI/AppInput/AppInput.vue';

defineOptions({
  layout: [EmployeeLayout, PlatformAdminLayout],
})
const http = useHttp<{date:string | null}, RedirectUrl>({
    date:null
})
const getLog = () => {
   http.get('/admin/logs/available', {
    onSuccess(response) {
        window.open(response.redirectUrl)
    },
   })
}

const gitLog = () => {
    window.open('/admin/logs/git')    
}
</script>

<template>
    <BaseContainer>
        <v-card>
            <v-card-text>
                <AppInput
                    v-model="http.date"
                    :error-messages="http.errors.date"
                    type="date"
                    label="Дата лога"
                />
                <AppPrimaryButton
                    @click="getLog"
                    :disabled="!http.date || http.processing"
                    :loading="http.processing"
                    text="Выгрузить"
                />
            </v-card-text>

            <v-card-text>
                <AppPrimaryButton 
                    text="гит лог"
                    @click="gitLog"
                />
            </v-card-text>
        </v-card>
    </BaseContainer>
</template>