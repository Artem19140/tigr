<script setup lang="ts">
import AppAddButton from '@/components/UI/AppAddButton/AppAddButton.vue';
import { AddressIndex } from '@/interfaces/Address';
import { Head, router } from '@inertiajs/vue3';
import { mdiClipboardTextOffOutline } from '@mdi/js'
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import CenterManagementLayout from './CenterManagementLayout.vue';
import AddressCard from './Components/AddressCard.vue';

const props = defineProps<{
    addresses:{
        data:AddressIndex[]
    },
    createUrl: string
}>()

defineOptions({
  layout: [EmployeeLayout, CenterManagementLayout],
})

</script>

<template>
    <Head>
        <title>Адреса</title>
    </Head>

    <v-container max-width="700">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xl font-semibold text-gray-900">
                    Адреса
                </div>

                <div class="mt-1 text-sm text-gray-500">
                    Экзаменационные адреса центра.
                </div>
            </div>

            <AppAddButton
                v-if="addresses.data.length > 0 && createUrl"
                @click="router.visit(createUrl)"
            />
        </div>

        <div
            v-if="addresses.data.length > 0"
            class="mt-5 space-y-3"
        >
            <AddressCard
                v-for="address in addresses.data"
                :key="address.id"
                :address="address"
            />
        </div>

        <v-empty-state
            v-else
            :icon="mdiClipboardTextOffOutline"
            title="Адресов пока нет"
            text="Добавьте первый экзаменационный адрес."
            class="py-10"
        >
            <AppAddButton
                v-if="createUrl"
                @click="router.visit(createUrl)"
            />
        </v-empty-state>
    </v-container>
</template>