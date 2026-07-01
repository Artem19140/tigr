<script setup lang="ts">
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
    onSuccess(response) {
        window.open(response.redirectUrl)
    },
   })
}

const gitLog = () => {
    window.open('/admin/logs/git')    
}

const audit = () => {
    http.get('/admin/logs/available?type=audit', {
        onSuccess(response) {
            window.open(response.redirectUrl)
        },
    })   
}
</script>

<template>
    <v-container>
        <v-card>
            <v-card-text>
                <v-text-field
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
                    @click="audit"
                    :disabled="!http.date || http.processing"
                    :loading="http.processing"
                    text="Выгрузить аудит"
                />
            </v-card-text>

            <v-card-text>
                <AppPrimaryButton 
                    text="гит лог"
                    :disabled="!http.date || http.processing"
                    @click="gitLog"
                />
            </v-card-text>
        </v-card>
    </v-container>
</template>