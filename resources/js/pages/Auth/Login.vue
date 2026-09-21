<script setup lang="ts">
import { ref } from 'vue';
import EmployeeEntryForm from './Components/EmployeeEntryForm.vue';
import ForeignNationalEntryForm from './Components/ForeignNationalEntryForm.vue';
import BaseEntryCard from '@/components/BaseComponents/BaseEntryCard/BaseEntryCard.vue';
import { Head } from '@inertiajs/vue3';
import { mdiAccount, mdiAccountOutline, mdiChevronUp } from '@mdi/js'

const isForeignNationalEntry = ref<boolean>(true)
</script>

<template>
    <Head>
        <title>Вход</title>
    </Head>

    <BaseEntryCard
        :subtitle="
            isForeignNationalEntry
                ? 'Введите код из 6 цифр'
                : 'Войдите в свой аккаунт'
        "
    >
        <ForeignNationalEntryForm v-if="isForeignNationalEntry" />
        <EmployeeEntryForm v-else />
    </BaseEntryCard>

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