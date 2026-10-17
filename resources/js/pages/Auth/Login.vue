<script setup lang="ts">
import { ref } from 'vue';
import EmployeeEntryForm from './Components/EmployeeEntryForm.vue';
import ForeignNationalEntryForm from './Components/ForeignNationalEntryForm.vue';
import { Head } from '@inertiajs/vue3';
import { mdiAccount, mdiAccountOutline, mdiChevronUp } from '@mdi/js'
import AppLogo from '@/components/UI/AppLogo/AppLogo.vue';

const isForeignNationalEntry = ref<boolean>(true)

const props = defineProps<{
    loginUrl: string,
    codesVerificationUrl: string,
    codesLength: number
}>()
</script>

<template>
    <Head>
        <title>Вход</title>
    </Head>
    
    <v-container class="min-h-screen d-flex align-center justify-center">
    <div class="w-100 max-w-md mx-auto">
        <v-card>
            <v-card-text>
                <div class="d-flex justify-center align-center mb-5">
                    <AppLogo max-width="150" />
                </div>
            </v-card-text>

            <v-card-text>
                <ForeignNationalEntryForm
                    v-if="isForeignNationalEntry"
                    :codesLength="codesLength"
                    :url="codesVerificationUrl"
                />

                <EmployeeEntryForm
                    v-else
                    :url="loginUrl"
                />
            </v-card-text>
        </v-card>
    </div>
</v-container>

    <div class="fixed bottom-5 left-1/2 z-20 -translate-x-1/2">
        <v-menu
            location="top center"
            width="220"
        >
            <template #activator="{ props }">
                <v-btn
                    v-bind="props"
                    variant="text"
                    size="small"
                    color="grey-darken-1"
                    class="rounded-lg"
                    :append-icon="mdiChevronUp"
                >
                    {{
                        isForeignNationalEntry
                            ? 'Вход для сотрудника'
                            : 'Вход для ИГ'
                    }}
                </v-btn>
            </template>

            <v-list
                density="comfortable"
                class="rounded-xl"
            >
                <v-list-item
                    :title="
                        isForeignNationalEntry
                            ? 'Вход сотрудника'
                            : 'Вход иностранного гражданина'
                    "
                    :prepend-icon="
                        isForeignNationalEntry
                            ? mdiAccount
                            : mdiAccountOutline
                    "
                    @click="isForeignNationalEntry = !isForeignNationalEntry"
                />
            </v-list>
        </v-menu>
    </div>
</template>