<script setup lang="ts">
import { ref } from 'vue';
import EmployeeEntryForm from './Components/EmployeeEntryForm.vue';
import ForeignNationalEntryForm from './Components/ForeignNationalEntryForm.vue';
import BaseEntryCard from '@/components/BaseComponents/BaseEntryCard/BaseEntryCard.vue';
import { Head } from '@inertiajs/vue3';
import { mdiChevronUp } from '@mdi/js'

const isForeignNationalEntry = ref<boolean>(true)
</script>

<template>
    <Head>
        <title>Вход</title>
    </Head>
    <BaseEntryCard
        :subtitle="isForeignNationalEntry ? 'Введите код из 6 цифр' : 'Войдите в свой аккаунт'"
    >
        <ForeignNationalEntryForm v-if="isForeignNationalEntry" />
        <EmployeeEntryForm v-else  />
    </BaseEntryCard>

    <v-menu location="top start" width="200">
        <template v-slot:activator="{ props }">
            <v-btn
                variant="text"
                color="grey"
                v-bind="props"
                class="position-fixed bottom-0 left-0 ma-4"
            >
                <v-icon :icon="mdiChevronUp" />
            </v-btn>
        </template>
        <v-list>
            <v-list-item 
                :title="isForeignNationalEntry ? 'Вход сотрудник' : 'Вход ИГ'"
                @click="isForeignNationalEntry = !isForeignNationalEntry" 
            />
        </v-list>
    </v-menu>
</template>