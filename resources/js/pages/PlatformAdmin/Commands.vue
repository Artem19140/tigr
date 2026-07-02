<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { useConfirmationOptionsDialog } from '@/composables/useConfirmationOptionsDialog';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import PlatformAdminLayout from '@/layouts/PlatformAdminLayout.vue';
import { useHttp } from '@inertiajs/vue3';

defineOptions({
  layout: [EmployeeLayout, PlatformAdminLayout],
})

const http = useHttp<{command :number | null}>({
    command: null
})

const execute = async (number: number) => {
    const {open} = useConfirmationOptionsDialog()
    const ok = await open(`Выполнить команду? (${number})`)
    if(!ok) return
    http.command = number
    http.post('/admin/commands',{
        onSuccess(response, httpResponse) {
            alert('ok')
        },
    })
}
</script>

<template>
    <v-container>
        <v-card>
            <v-card-text >
                <div class="flex flex-column gap-4">
                    <AppPrimaryButton
                        text="php artisan migrate --force (1)"
                        @click="() => execute(1)"
                        :loading="http.processing"
                        :disabled="http.processing"
                    />

                    <AppPrimaryButton
                        text="php artisan optimize:clear (2)"
                        @click="() => execute(2)"
                        :loading="http.processing"
                        :disabled="http.processing"
                    />

                    <AppPrimaryButton
                        text="php artisan optimize (3)"
                        @click="() => execute(3)"
                        :loading="http.processing"
                        :disabled="http.processing"
                    />

                    <AppPrimaryButton
                        text="php artisan migrate:fresh --seed (4)"
                        @click="() => execute(4)"
                        :loading="http.processing"
                        :disabled="http.processing"
                    />

                    <AppPrimaryButton
                        text="deploy (5)"
                        @click="() => execute(5)"
                        :loading="http.processing"
                        :disabled="http.processing"
                    />
                </div>
            </v-card-text>
        </v-card>
    </v-container>
</template>